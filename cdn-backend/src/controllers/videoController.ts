import { Request, Response } from 'express';
import { getVideo, getVideoUrl } from "../services/videoService";
import Video from "../entities/video";

export const getVideoRoute = async (req: Request, res: Response): Promise<void> => {
    try {
        const video = await getVideo(req.params.id);
        res.status(200).json(video);
    } catch (error) {
        res.status(500).json({ error: (error as Error).message });
    }
};

export const getVideoUrlRoute = async (req: Request, res: Response): Promise<void> => {
    const videoId = req.params.id;

    const video = await Video.findByPk(videoId) as any;
    if (!video) {
        res.status(404).json({ error: "Video not found" });
        return;
    }

    const url = await getVideoUrl(video.originalPath);
    res.status(200).json({ url });
};
