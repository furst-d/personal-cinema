const Bull = require('bull');
const Video = require('../../entities/video');

const videoQueue = new Bull('video-processing', {
    redis: {
        host: 'redis',
        port: 6379
    }
});

videoQueue.process(async (job, done) => {
    const video = await Video.findByPk(job.data.videoId);
    if (!video) {
        return done(new Error('Video not found'));
    }

    // Add logic to process the video using ffmpeg and upload to MinIO

    video.status = 'completed';
    video.url = 'https://your-cdn-url/' + video.id;
    await video.save();
    done();
});

module.exports = videoQueue;
