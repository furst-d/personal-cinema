import videoQueue from '../../config/bull';
import Video from '../../entities/video';

videoQueue.process(async (job, done) => {
    try {
        const video = await Video.findByPk(job.data.videoId);
        if (!video) {
            throw new Error('Video not found');
        }

        // Add logic to process the video using ffmpeg and upload to MinIO

        video.status = 'completed';
        await video.save();
        done();
    } catch (error) {
        done(error);
    }
});

export default videoQueue;
