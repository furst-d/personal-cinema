import {
    videoConversionQueue,
    videoProcessQueue,
    videoThumbnailQueue,
    videoUploadQueue
} from '../../config/bull';
import minioClient, { bucketName } from '../../config/minio';
import Video from '../../entities/video';
import { Job } from 'bull';
import ffmpeg from 'fluent-ffmpeg';
import {getOrCreateMd5, getVideo, getVideoUrl} from '../../services/videoService';
import {sendNotificationCallback, sendThumbnailCallback} from "../../services/callbackService";
import path from "path";
import { promises as fs } from 'fs';
import crypto from 'crypto';
import {mkdirSync} from "fs";
import {VideoProcessingUtils} from "./VideoProcessingUtils";

const processVideoUpload = async (job: Job) => {
    const { videoId, tempFilePath, urlPath, mimetype, size } = job.data;

    const video = await getVideo(videoId);
    VideoProcessingUtils.ensureTempDir(videoId);

    try {
        const fileBuffer = await fs.readFile(tempFilePath);

        console.log("Starting upload video of id:" + videoId + " to Minio");
        await minioClient.putObject(bucketName, urlPath, fileBuffer, size, {
            'Content-Type': mimetype,
        });

        const video = await getVideo(videoId);
        video.status = 'uploaded-original';
        await video.save();

        await videoProcessQueue.add({ videoId }, { removeOnComplete: true, removeOnFail: true });
        await videoThumbnailQueue.add({ videoId }, { removeOnComplete: true, removeOnFail: true });
    } catch (error) {
        console.error('Error uploading to Minio:', error);
        await VideoProcessingUtils.markForDeletion(video);
        throw error;
    }

    await VideoProcessingUtils.checkForDeletion(videoId);
};

const processVideoInfo = async (job: Job) => {
    const { videoId } = job.data;
    const video = await getVideo(videoId);

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
                        const md5Hash = await VideoProcessingUtils.calculateMd5(videoUrl);
                        const md5 = await getOrCreateMd5(md5Hash);

                        const video = await getVideo(videoId);
                        video.md5Id = md5.id;

                        video.length = Math.round(metadata.format.duration);
                        video.originalWidth = videoStream.width;
                        video.originalHeight = videoStream.height;
                        video.codec = videoStream.codec_name;

                        video.status = 'processed-info';
                        await video.save();
                        await videoConversionQueue.add({ videoId }, { removeOnComplete: true, removeOnFail: true });
                        await sendNotificationCallback(video.id);
                    } else {
                        throw new Error('Cannot get video info');
                    }
                } else {
                    throw new Error('No video stream found');
                }
            });
    } catch (error) {
        console.error('Error processing video info:', error);
        await VideoProcessingUtils.markForDeletion(video);
        throw error;
    }

    await VideoProcessingUtils.checkForDeletion(videoId);
};

const processVideoConversion = async (job: Job) => {
    const { videoId } = job.data;
    const video = await getVideo(videoId);
    const videoTempDir = VideoProcessingUtils.ensureTempDir(videoId);

    const videoPath = video.originalPath;
    const videoUrl = await getVideoUrl(videoPath);

    const hlsHash = crypto.createHash('md5').update(videoUrl).digest('hex');
    const hlsDir = `${videoTempDir}/hls/segments`;
    const hlsManifestDir = `${videoTempDir}/hls/manifests`;

    // Create the directory if it doesn't exist
    mkdirSync(hlsDir, { recursive: true });
    mkdirSync(hlsManifestDir, { recursive: true });

    const resolutions = [
        { width: 256, height: 144, label: "144p" },
        { width: 426, height: 240, label: "240p" },
        { width: 640, height: 360, label: "360p" },
        { width: 854, height: 480, label: "480p" },
        { width: 1280, height: 720, label: "720p" },
        { width: 1920, height: 1080, label: "1080p" },
    ];

    try {
        const originalWidth = video.originalWidth;
        const originalHeight = video.originalHeight;

        // Filter the resolutions based on the original video resolution
        const validResolutions = resolutions.filter(resolution =>
            resolution.width <= originalWidth && resolution.height <= originalHeight
        );

        // Generate HLS streams for each valid resolution
        for (const { width, height, label } of validResolutions) {
            const resolutionDir = `${hlsDir}/${label}`;
            const hlsManifest = `${hlsManifestDir}/${label}.m3u8`;

            mkdirSync(resolutionDir, { recursive: true });

            await new Promise((resolve, reject) => {
                ffmpeg(videoUrl)
                    .outputOptions([
                        `-vf scale=w=${width}:h=-2:force_original_aspect_ratio=decrease`,
                        '-c:a aac',
                        '-ar 48000',
                        '-c:v h264',
                        '-profile:v baseline',
                        '-crf 20',
                        '-sc_threshold 0',
                        '-g 48',
                        '-keyint_min 48',
                        '-hls_time 10',
                        '-hls_playlist_type vod',
                        '-b:v 800k',
                        '-maxrate 856k',
                        '-bufsize 1200k',
                        '-b:a 96k',
                        `-hls_segment_filename ${hlsDir}/${label}/%03d.ts`,
                        '-f hls',
                    ])
                    .output(hlsManifest)
                    .on('end', resolve)
                    .on('error', (err, stdout, stderr) => {
                        console.error('Error: ' + err.message);
                        console.error('ffmpeg stdout: ' + stdout);
                        console.error('ffmpeg stderr: ' + stderr);
                        reject(err);
                    })
                    .run();
            });
        }

        // Upload individual manifests and segments
        for (const { label } of validResolutions) {
            const hlsManifest = `${hlsManifestDir}/${label}.m3u8`;
            const hlsBuffer = await fs.readFile(hlsManifest);
            await minioClient.putObject(bucketName, `${path.dirname(videoPath)}/hls/${label}.m3u8`, hlsBuffer);

            const resolutionDir = `${hlsDir}/${label}`;
            const segmentFiles = await fs.readdir(resolutionDir);
            for (const segment of segmentFiles) {
                if (segment.endsWith('.ts')) {
                    const segmentBuffer = await fs.readFile(path.join(resolutionDir, segment));
                    await minioClient.putObject(bucketName, `${path.dirname(videoPath)}/hls/segments/${label}/${segment}`, segmentBuffer);
                }
            }
        }

        for (const { label } of validResolutions) {
            const resolutionDir = `${hlsDir}/${label}`;
            const segmentFiles = await fs.readdir(resolutionDir);
            for (const segment of segmentFiles) {
                if (segment.endsWith('.ts')) {
                    await fs.unlink(path.join(resolutionDir, segment));
                }
            }
        }

        // Clean up HLS temp directory
        await VideoProcessingUtils.cleanUpTempSubDir(videoId, 'hls');

        // Update video with HLS path
        video.hlsPath = `${path.dirname(videoPath)}/hls/master.m3u8`;
        video.status = 'converted';
        await video.save();
        await sendNotificationCallback(video.id);
    } catch (error) {
        await VideoProcessingUtils.markForDeletion(video);
        throw error;
    }

    await VideoProcessingUtils.checkForDeletion(videoId);
};





const processVideoThumbnail = async (job: Job) => {
    const { videoId } = job.data;
    const video = await Video.findByPk(videoId);
    const videoTempDir = VideoProcessingUtils.ensureTempDir(videoId);

    if (!video) {
        throw new Error('Video not found');
    }

    const videoPath = video.originalPath;
    const videoUrl = await getVideoUrl(videoPath);

    const thumbnailDir = `${videoTempDir}/thumbs`;

    // Create the directory if it doesn't exist
    mkdirSync(thumbnailDir, { recursive: true });

    try {
        await new Promise((resolve, reject) => {
            ffmpeg(videoUrl)
                .on('end', function () {
                    console.log('Screenshots taken');
                    resolve(true);
                })
                .on('error', function (err) {
                    console.error('Error taking screenshots:', err.message);
                    reject(err);
                })
                .screenshots({
                    count: 20,
                    folder: thumbnailDir,
                    filename: 'thumb.png'
                });
        });

        // Read the thumbnail files
        const thumbnailFiles = await fs.readdir(thumbnailDir);

        // Upload thumbnail files
        for (const thumbnail of thumbnailFiles) {
            const thumbnailPath = path.join(thumbnailDir, thumbnail);
            const thumbnailBuffer = await fs.readFile(thumbnailPath);
            const stats = await fs.stat(thumbnailPath);
            const size = stats.size;

            await minioClient.putObject(bucketName, `${path.dirname(videoPath)}/thumbs/${thumbnail}`, thumbnailBuffer, size, {
                'Content-Type': 'image/png'
            });
        }

        // Delete the local thumbnail files after upload
        for (const thumbnail of thumbnailFiles) {
            await fs.unlink(path.join(thumbnailDir, thumbnail));
        }

        // Clean up thumbnails temp directory
        await VideoProcessingUtils.cleanUpTempSubDir(videoId, 'thumbs');

        const video = await getVideo(videoId);
        video.thumbnailPath = `${videoId}/thumbs`;
        video.status = 'thumbnails-generated';
        await video.save();
        await sendThumbnailCallback(video.id);
    } catch (error) {
        console.error('Error generating thumbnails:', error);
        await VideoProcessingUtils.markForDeletion(video);
        throw error;
    }

    await VideoProcessingUtils.checkForDeletion(videoId);
};

videoUploadQueue.process(processVideoUpload);
videoProcessQueue.process(processVideoInfo);
videoConversionQueue.process(processVideoConversion);
videoThumbnailQueue.process(processVideoThumbnail);
