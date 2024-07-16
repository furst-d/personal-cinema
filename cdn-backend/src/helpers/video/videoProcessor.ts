// helpers/video/videoProcessor.ts
import videoQueue from '../../config/bull';
import minioClient from '../../config/minio';
import Video from '../../entities/video';

videoQueue.process(async (job, done) => {
    const { videoId, buffer, urlPath, mimetype, size } = job.data;

    try {
        const bufferData = Buffer.from(buffer);

        await minioClient.putObject('videos', urlPath, bufferData, size, {
            'Content-Type': mimetype,
        });

        const video = await Video.findByPk(videoId);
        if (video) {
            video.status = 'uploaded-original';
            await video.save();
        }
        done();
    } catch (error) {
        console.error('Error uploading to Minio:', error);
        const video = await Video.findByPk(videoId);
        if (video) {
            video.status = 'upload-error';
            await video.save();
        }
        done(error as Error);
    }
});
