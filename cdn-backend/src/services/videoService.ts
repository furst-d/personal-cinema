import { v4 as uuidv4 } from 'uuid';
import path from 'path';
import crypto from 'crypto';
import Video from '../entities/video';
import Md5 from '../entities/md5';
import minioClient from '../config/minio';
import videoQueue from "../config/bull";

export const uploadVideo = async (file: Express.Multer.File, params: string, project_id: string) => {
    const { originalname, buffer, mimetype, size } = file;

    const extension = path.extname(originalname);
    const hash = crypto.createHash('md5').update(buffer).digest('hex');

    const videoId = uuidv4();
    const urlPath = `${videoId}/${hash}${extension}`;

    await videoQueue.add({
        videoId,
        buffer: buffer.toString('base64'),
        urlPath,
        mimetype,
        size
    });

    return await Video.create({
        id: videoId,
        title: originalname,
        status: 'uploading',
        originalPath: urlPath,
        extension: extension,
        size: size,
        projectId: project_id,
        parameters: JSON.parse(params),
    });
};

export const getVideoUrl = async (videoId: string) => {
    const video = await Video.findByPk(videoId) as any;
    if (!video) {
        throw new Error('Video not found');
    }

    const objectName = `${video.id}/${video.hash}${video.extension}`;
    return  await minioClient.presignedUrl('GET', 'videos', objectName, 24 * 60 * 60); // 24 hours
};

export const getVideo = async (id: string) => {
    const video = await Video.findByPk(id, { include: Md5 }) as any;
    if (!video) {
        throw new Error('Video not found');
    }
    return video;
};

/**
 * Prepare video data for sending in a callback
 * @param video
 */
export const prepareVideoData = async (video: Video) => {
    return {
        id: video.id,
        title: video.title,
        status: video.status,
        type: video.type,
        extension: video.extension,
        size: video.size,
        length: video.length,
        resolution: {
            width: video.originalWidth,
            height: video.originalHeight,
        },
        md5: video.md5 ? video.md5.md5 : null,
        createdAt: video.createdAt,
        updatedAt: video.updatedAt,
        parameters: video.parameters
    };
}
