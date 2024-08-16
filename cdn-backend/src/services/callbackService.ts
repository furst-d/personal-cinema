import axios, { AxiosResponse } from 'axios';
import Project from "../entities/project";
import Callback from "../entities/callback";
import { callbackLogger } from "../config/logger"
import {getSignedThumbnails, getVideo, prepareVideoData} from "./videoService";

/**
 * Send a callback to a given URL with the given data
 * @param url
 * @param data
 */
async function sendCallback(url: string, data: object): Promise<AxiosResponse> {
    try {
        return await axios.post(url, data, { timeout: 5000 });
    } catch (error) {
        if (axios.isAxiosError(error)) {
            if (error.response) {
                callbackLogger.error(`Failed to send callback to ${url}: ${error.message} | Response body: ${JSON.stringify(error.response.data)}`);
            } else {
                callbackLogger.error(`Failed to send callback to ${url}: No response received`);
            }
        } else {
            callbackLogger.error(`Failed to send callback to ${url}: ${error}`);
        }
        throw error;
    }
}

/**
 * Get the callback for a given project
 * @param projectId
 */
const getCallback = async (projectId: string): Promise<Callback | null> => {
    const project = await Project.findOne({
        where: { id: projectId },
        include: [Callback]
    });

    if (!project) {
        return null;
    }

    return project.callback;
};

/**
 * Send a notification callback for a given video
 * @param videoId
 */
export const sendNotificationCallback = async (videoId: string): Promise<void> => {
    const video = await getVideo(videoId);

    if (video.isDeleted) {
        return;
    }

    const callback = await getCallback(video.projectId);

    if (!callback) {
        callbackLogger.error(`No callback found for project ${video.projectId}`);
        return;
    }

    const data = await prepareVideoData(video);

    try {
        const response = await sendCallback(callback.notificationUrl, { video: data });
        callbackLogger.info(`Sent notification callback for video ${video.id}, response: ${response.status}, ${JSON.stringify(response.data)}`);
    } catch (error) {}
}

/**
 * Send a delete notification callback for a given video
 * @param videoId
 */
export const sendDeleteNotificationCallback = async (videoId: string): Promise<void> => {
    const video = await getVideo(videoId);

    const callback = await getCallback(video.projectId);

    if (!callback) {
        callbackLogger.error(`No callback found for project ${video.projectId}`);
        return;
    }

    const data = {
        id: video.id,
        deleted: true
    }

    try {
        const response = await sendCallback(callback.notificationUrl, { video: data });
        callbackLogger.info(`Sent delete notification callback for video ${video.id}, response: ${response.status}, ${JSON.stringify(response.data)}`);
    } catch (error) {}
}

/**
 * Send a thumbnail callback for a given video
 * @param videoId
 */
export const sendThumbnailCallback = async (videoId: string): Promise<void> => {
    const video = await getVideo(videoId);

    if (video.isDeleted) {
        return;
    }

    const callback = await getCallback(video.projectId);

    if (!callback) {
        callbackLogger.error(`No callback found for project ${video.projectId}`);
        return;
    }

    const videoData = await prepareVideoData(video);
    const thumbs = await getSignedThumbnails(video.id);

    try {
        const response = await sendCallback(callback.thumbUrl, { video: videoData, thumbs: thumbs });
        callbackLogger.info(`Sent thumbnail callback for video ${video.id}, response: ${response.status}, ${JSON.stringify(response.data)}`);
    } catch (error) {}
}