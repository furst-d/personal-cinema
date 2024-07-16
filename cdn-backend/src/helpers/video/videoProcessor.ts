import { videoConversionQueue, videoProcessQueue, videoThumbnailQueue, videoUploadQueue } from '../../config/bull';
import minioClient, { bucketName } from '../../config/minio';
import Video from '../../entities/video';
import { Job } from 'bull';
import ffmpeg from 'fluent-ffmpeg';
import { PassThrough } from 'stream';
import { getOrCreateMd5, getVideoUrl } from '../../services/videoService';

const processVideoUpload = async (job: Job) => {
    const { videoId, buffer, urlPath, mimetype, size } = job.data;

    try {
        await minioClient.putObject(bucketName, urlPath, Buffer.from(buffer, 'base64'), size, {
            'Content-Type': mimetype,
        });

        const video = await Video.findByPk(videoId);
        if (video) {
            video.status = 'uploaded-original';
            await video.save();

            await videoProcessQueue.add({ videoId });
            await videoConversionQueue.add({ videoId });
            await videoThumbnailQueue.add({ videoId });
        }
    } catch (error) {
        console.error('Error uploading to Minio:', error);
        const video = await Video.findByPk(videoId);
        if (video) {
            video.status = 'upload-error';
            await video.save();
        }
        throw error;
    }
};

const processVideoInfo = async (job: Job) => {
    const { videoId } = job.data;
    const video = await Video.findByPk(videoId);

    if (!video) {
        throw new Error('Video not found');
    }

    const videoPath = video.originalPath;

    try {
        const videoUrl = await getVideoUrl(videoPath);

        ffmpeg(videoUrl)
            .ffprobe(async (err, metadata) => {
                if (err) {
                    console.error('Error probing video:', err);
                    throw err;
                }

                const videoStream = metadata.streams.find((stream) => stream.codec_type === 'video');
                if (videoStream) {
                    if (metadata.format.duration && videoStream.width && videoStream.height && videoStream.codec_name) {
                        video.length = Math.round(metadata.format.duration);
                        video.originalWidth = videoStream.width;
                        video.originalHeight = videoStream.height;
                        video.codec = videoStream.codec_name;

                        const md5Hash = await calculateMd5(videoUrl);
                        const md5 = await getOrCreateMd5(md5Hash);

                        video.md5Id = md5.id;
                        video.status = 'processed-info';
                        await video.save();
                    } else {
                        throw new Error('Cannot get video info');
                    }
                } else {
                    throw new Error('No video stream found');
                }
            });
    } catch (error) {
        console.error('Error processing video info:', error);
        throw error;
    }
};

const processVideoConversion = async (job: Job) => {
    const { videoId } = job.data;
    const video = await Video.findByPk(videoId);

    if (!video) {
        throw new Error('Video not found');
    }

    // Přidejte logiku pro konverzi videa do HLS formátu
    // Příklad: Použití ffmpeg pro konverzi do HLS

    // Aktualizujte video v databázi s novým HLS URL
    // video.hlsPath = 'path/to/hls';
    await video.save();
};

const processVideoThumbnail = async (job: Job) => {
    const { videoId } = job.data;
    const video = await Video.findByPk(videoId);

    if (!video) {
        throw new Error('Video not found');
    }

    // Přidejte logiku pro generování náhledových obrázků
    // Příklad: Použití ffmpeg pro generování náhledových obrázků

    // Aktualizujte video v databázi s novými informacemi
    // video.thumbnailPath = 'path/to/thumbnail';
    await video.save();
};

/**
 * Calculate MD5 hash of a video
 * @param videoUrl
 */
const calculateMd5 = (videoUrl: string): Promise<string> => {
    return new Promise((resolve, reject) => {
        let md5Hash = '';
        ffmpeg(videoUrl)
            .outputOptions('-f', 'md5')
            .on('end', () => {
                resolve(md5Hash);
            })
            .on('error', (err) => {
                console.error('Error calculating MD5 hash:', err);
                reject(err);
            })
            .pipe(new PassThrough().on('data', (chunk) => {
                md5Hash += chunk.toString().split('=')[1]?.trim();
            }));
    });
};



// Procesory front
videoUploadQueue.process(processVideoUpload);
videoProcessQueue.process(processVideoInfo);
videoConversionQueue.process(processVideoConversion);
videoThumbnailQueue.process(processVideoThumbnail);

export { processVideoUpload, processVideoInfo, processVideoConversion, processVideoThumbnail };
