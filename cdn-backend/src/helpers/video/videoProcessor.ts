import {videoConversionQueue, videoProcessQueue, videoThumbnailQueue, videoUploadQueue} from '../../config/bull';
import minioClient from '../../config/minio';
import Video from '../../entities/video';
import {Job} from "bull";

const processVideoUpload = async (job: Job) => {
    const { videoId, buffer, urlPath, mimetype, size } = job.data;

    try {
        await minioClient.putObject('videos', urlPath, buffer, size, {
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

    // Přidejte logiku pro zjištění informací o videu (délka, šířka, výška, atd.)
    // Příklad: Použití ffmpeg pro získání metadat

    // Aktualizujte video v databázi s novými informacemi
    // video.length = 120; // délka videa v sekundách
    // video.originalWidth = 1920; // šířka videa
    // video.originalHeight = 1080; // výška videa
    await video.save();
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

videoUploadQueue.process(processVideoUpload);
videoProcessQueue.process(processVideoInfo);
videoConversionQueue.process(processVideoConversion);
videoThumbnailQueue.process(processVideoThumbnail);