import Bull from 'bull';
import dotenv from 'dotenv';

dotenv.config();

const redisConfig = {
    redis: {
        host: process.env.REDIS_HOST || 'localhost',
        port: parseInt(process.env.REDIS_PORT || '6379', 10),
        password: process.env.REDIS_PASSWORD || undefined,
    }
};

export const videoUploadQueue = new Bull('video-upload-queue', redisConfig);
export const videoProcessQueue = new Bull('video-process-queue', redisConfig);
export const videoConversionQueue = new Bull('video-conversion-queue', redisConfig);
export const videoThumbnailQueue = new Bull('video-thumbnail-queue', redisConfig);
