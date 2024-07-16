import Bull from 'bull';
import dotenv from 'dotenv';

dotenv.config();

const videoQueue = new Bull('video-processing', {
    redis: {
        host: process.env.REDIS_HOST || 'localhost',
        port: parseInt(process.env.REDIS_PORT || '6379', 10),
        password: process.env.REDIS_PASSWORD || undefined,
    }
});

export default videoQueue;
