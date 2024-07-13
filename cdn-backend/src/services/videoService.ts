import { v4 as uuidv4 } from 'uuid';
import path from 'path';
import crypto from 'crypto';
import Video from '../entities/video';
import Md5 from '../entities/md5';
import minioClient from '../config/minio';

export const uploadVideo = async (file: Express.Multer.File, params: string, project_id: string) => {
    const { originalname, buffer, mimetype, size } = file;

    const extension = path.extname(originalname);
    const hash = crypto.createHash('md5').update(buffer).digest('hex');

    const videoId = uuidv4();
    const urlPath = `${videoId}/${hash}${extension}`;

    try {
        await minioClient.putObject('videos', urlPath, buffer, size, {
            'Content-Type': mimetype,
        });
    } catch (error) {
        console.error('Error uploading to Minio:', error);
        throw new Error('Error uploading to storage server');
    }

    return await Video.create({
        id: videoId,
        title: originalname,
        status: 'uploaded-original',
        originalPath: urlPath,
        hash: hash,
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
