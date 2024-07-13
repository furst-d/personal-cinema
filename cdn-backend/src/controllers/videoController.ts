import { Request, Response } from 'express';
import { getVideo, getVideoUrl } from "../services/videoService";

export const getVideoRoute = async (req: Request, res: Response): Promise<void> => {
    try {
        const video = await getVideo(req.params.id);
        res.status(200).json(video);
    } catch (error) {
        res.status(500).json({ error: (error as Error).message });
    }
};

export const getVideoUrlRoute = async (req: Request, res: Response): Promise<void> => {
    try {
        const videoId = req.params.id;
        const url = await getVideoUrl(videoId);
        res.status(200).json({ url });
    } catch (error) {
        res.status(500).json({ error: (error as Error).message });
    }
};
