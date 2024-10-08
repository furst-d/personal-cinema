import { v4 as uuidv4 } from 'uuid';
import path from 'path';
import crypto from 'crypto';
import Video from '../entities/video';
import Md5 from '../entities/md5';
import minioClient, {bucketName} from '../config/minio';
import { videoUploadQueue } from "../config/bull";
import fs from 'fs';
import {VideoProcessingUtils} from "../helpers/video/VideoProcessingUtils";
import {sendDeleteNotificationCallback} from "./callbackService";

/**
 * Upload a video
 * @param file
 * @param params
 * @param projectId
 */
export const uploadVideo = async (file: Express.Multer.File, params: string, projectId: string) => {
    const { originalname, buffer, mimetype, size } = file;

    const extension = path.extname(originalname);
    const hash = crypto.createHash('md5').update(buffer).digest('hex');

    const videoId = uuidv4();
    const urlPath = `${videoId}/${hash}${extension}`;

    const tempFilePath = path.join('/tmp', `${videoId}${extension}`);

    try {
        await fs.promises.writeFile(tempFilePath, buffer);

        const video = await Video.create({
            id: videoId,
            title: originalname,
            status: 'uploading',
            originalPath: urlPath,
            extension: extension,
            size: size,
            projectId: projectId,
            parameters: JSON.parse(params),
        });

        await videoUploadQueue.add(
            {
                videoId,
                tempFilePath,
                urlPath,
                mimetype,
                size
            },
            {
                removeOnComplete: true,
                removeOnFail: true
            }
        ).then(() => {
            console.log("Successfully added to queue");
        }).catch((err) => {
            console.error("Failed to add to queue", err);
        });

        return video;
    } catch (err) {
        console.error("Failed to save file locally", err);
        throw err;
    }
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
export const getVideo = async (id: string): Promise<Video> => {
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
        deleted: video.isDeleted,
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
        conversions: video.conversions,
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

    await md5.save();

    return md5;
};

/**
 * Get signed URLs for video thumbnails
 * @param videoId
 */
export const getSignedThumbnails = async (videoId: string) => {
    const video = await getVideo(videoId);

    if (!video.thumbnailPath) {
        throw new Error('Thumbnails not processed');
    }

    const thumbnailPath = `${videoId}/thumbs`;

    const thumbnailUrls: { [key: number]: string } = {};
    for (let i = 1; i <= 20; i++) {
        const thumbnailFileName = `thumb_${i}.png`;
        thumbnailUrls[i] =  await minioClient.presignedUrl('GET', bucketName, `${thumbnailPath}/${thumbnailFileName}`, 24 * 60 * 60); // 24 hours
    }

    return thumbnailUrls;
};

export const deleteVideo = async (video: Video) => {
    console.log("Deleting video: ", video.title);

    try {
        await deleteFromStorage(video);
        await sendDeleteNotificationCallback(video.id);
        await video.destroy();
    } catch (err) {
        console.error("Failed to delete video:", err);
        throw err;
    }
};

const deleteFromStorage = async (video: Video) => {
    const folderPath = `${video.id}/`;

    const objectsList = [];
    const stream = minioClient.listObjectsV2(bucketName, folderPath, true);

    for await (const obj of stream) {
        objectsList.push(obj.name);
    }

    if (objectsList.length > 0) {
        await minioClient.removeObjects(bucketName, objectsList);
        console.log("Successfully deleted all objects in the folder:", folderPath);
    } else {
        console.log("No objects found in the folder:", folderPath);
    }

    await VideoProcessingUtils.cleanUpTempSubDir(video.id, '');
}

