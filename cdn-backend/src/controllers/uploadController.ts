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
        const err = error as any;
        if (err.message === 'Only video files are allowed') {
            res.status(400).json({ error: err.message });
        } else {
            res.status(500).json({ error: (error as Error).message });
        }
    }
};
