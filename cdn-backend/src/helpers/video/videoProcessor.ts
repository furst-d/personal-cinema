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

        await minioClient.putObject(bucketName, urlPath, fileBuffer, size, {
            'Content-Type': mimetype,
        });

        video.status = 'uploaded-original';
        await video.save();

        await videoProcessQueue.add({ videoId }, { removeOnComplete: true, removeOnFail: true });
        await videoConversionQueue.add({ videoId }, { removeOnComplete: true, removeOnFail: true });
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
    const hlsManifest = `${videoTempDir}/hls/${hlsHash}.m3u8`;

    // Create the directory if it doesn't exist
    mkdirSync(hlsDir, { recursive: true });

    try {
        await new Promise((resolve, reject) => {
            ffmpeg(videoUrl)
                .outputOptions([
                    '-profile:v baseline', // HLS requires baseline profile
                    '-level 3.0',
                    '-start_number 0',
                    '-hls_time 10', // Segment length in seconds
                    '-hls_list_size 0',
                    '-hls_segment_filename', `${hlsDir}/%03d.ts`,
                    '-f hls'
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

        // Read the manifest file and all segment files
        const hlsBuffer = await fs.readFile(hlsManifest);
        const segmentFiles = await fs.readdir(hlsDir);

        // Upload manifest file
        await minioClient.putObject(bucketName, `${path.dirname(videoPath)}/hls/${hlsHash}.m3u8`, hlsBuffer);

        // Upload segment files
        for (const segment of segmentFiles) {
            if (segment.endsWith('.ts')) {
                const segmentBuffer = await fs.readFile(path.join(hlsDir, segment));
                await minioClient.putObject(bucketName, `${path.dirname(videoPath)}/hls/segments/${segment}`, segmentBuffer);
            }
        }

        // Delete the local HLS files after upload
        await fs.unlink(hlsManifest);
        for (const segment of segmentFiles) {
            if (segment.endsWith('.ts')) {
                await fs.unlink(path.join(hlsDir, segment));
            }
        }

        // Clean up HLS temp directory
        await VideoProcessingUtils.cleanUpTempSubDir(videoId, 'hls');

        const video = await getVideo(videoId);
        video.hlsPath = `${path.dirname(videoPath)}/hls/${hlsHash}.m3u8`;
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
