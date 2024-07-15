import axios from 'axios';
import Video from "../entities/video";
import Project from "../entities/project";
import Callback from "../entities/callback";
import { callbackLogger } from "../config/logger"

/**
 * Send a callback to a given URL with the given data
 * @param url
 * @param data
 */
const sendCallback = async (url: string, data: object): Promise<void> => {
    try {
        callbackLogger.info(`Attempting to send callback to ${url}`);
        await axios.post(url, null, { timeout: 5000 });
        callbackLogger.info(`Callback sent to ${url} with data: ${JSON.stringify(data)}`);
    } catch (error) {
        if (axios.isAxiosError(error)) {
            if (error.response) {
                // Server responded with a status other than 200 range
                // callbackLogger.error(`Failed to send callback to ${url}: ${error.message} | Response: ${JSON.stringify(error.response.data)}`);
            } else if (error.request) {
                // No response was received from server
                console.log(error.request)
                callbackLogger.error(`Failed to send callback to ${url}: No response received`);
            } else {
                // Something happened in setting up the request
                callbackLogger.error(`Failed to send callback to ${url}: ${error.message}`);
            }
        } else {
            // Generic error
            callbackLogger.error(`Failed to send callback to ${url}: ${error}`);
        }
    }
};

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

    await sendCallback(callback.notificationUrl, { video });
}