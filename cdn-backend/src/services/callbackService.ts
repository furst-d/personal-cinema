import axios, { AxiosResponse } from 'axios';
import Video from "../entities/video";
import Project from "../entities/project";
import Callback from "../entities/callback";
import { callbackLogger } from "../config/logger"
import { prepareVideoData } from "./videoService";

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
 * @param video
 */
export const sendNotificationCallback = async (video: Video): Promise<void> => {
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