import { Request, Response } from 'express';
import { uploadVideo } from '../services/videoService';
import { sendNotificationCallback } from "../services/callbackService";

export const uploadVideoRoute = async (req: Request, res: Response): Promise<void> => {
    try {
        const file = req.file;

        if (!file) {
            res.status(400).json({ error: 'No file uploaded' });
            return;
        }

        const video = await uploadVideo(file, req.body.params, req.body.project_id);
        await sendNotificationCallback(video);

        res.status(201).json({ message: 'Video uploaded successfully' });
    } catch (error) {
        res.status(500).json({ error: (error as Error).message });
    }
};
