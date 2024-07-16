import { v4 as uuidv4 } from 'uuid';
import path from 'path';
import crypto from 'crypto';
import Video from '../entities/video';
import Md5 from '../entities/md5';
import minioClient, {bucketName} from '../config/minio';
import { videoUploadQueue } from "../config/bull";

/**
 * Upload a video
 * @param file
 * @param params
 * @param project_id
 */
export const uploadVideo = async (file: Express.Multer.File, params: string, project_id: string) => {
    const { originalname, buffer, mimetype, size } = file;

    const extension = path.extname(originalname);
    const hash = crypto.createHash('md5').update(buffer).digest('hex');

    const videoId = uuidv4();
    const urlPath = `${videoId}/${hash}${extension}`;

    await videoUploadQueue.add({
        videoId,
        buffer,
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

/**
 * Get a presigned URL for a video
 * @param path
 */
export const getVideoUrl = async (path: string) => {
    return await minioClient.presignedUrl('GET', bucketName, path, 24 * 60 * 60); // 24 hours
};

/**
 * Get a video by ID
 * @param id
 */
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
        codec: video.codec,
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

/**
 * Get or create an MD5 hash
 * @param hash
 */
export const getOrCreateMd5 = async (hash: string): Promise<Md5> => {
    let md5 = await Md5.findOne({ where: { md5: hash } });

    if (!md5) {
        md5 = await Md5.create({ md5: hash });
    }

    return md5;
};
