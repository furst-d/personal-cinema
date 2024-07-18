import { Request, Response } from 'express';
import {getSignedThumbnails, getVideo, prepareVideoData} from "../services/videoService";
import Video from "../entities/video";
import minioClient, { bucketName } from "../config/minio";
import path from "path";
import {StreamUtils} from "../helpers/video/StreamUtils";

export const getVideoRoute = async (req: Request, res: Response): Promise<void> => {
    try {
        const video = await getVideo(req.params.id);
        res.status(200).json(await prepareVideoData(video));
    } catch (error) {
        res.status(500).json({ error: (error as Error).message });
    }
};

export const getThumbsRoute = async (req: Request, res: Response): Promise<void> => {
    try {
        const thumbs = await getSignedThumbnails(req.params.id);
        res.status(200).json({ thumbs });
    } catch (error) {
        res.status(500).json({ error: (error as Error).message });
    }
}

export const getVideoUrlRoute = async (req: Request, res: Response): Promise<void> => {
    const videoId = req.params.id;

    const video = await Video.findByPk(videoId) as any;
    if (!video) {
        res.status(404).json({ error: "Video not found" });
        return;
    }

    if (!video.hlsPath) {
        res.status(404).json({ error: "Video not processed" });
        return;
    }

    try {
        // Load manifest file from Minio
        const manifestStream = await minioClient.getObject(bucketName, video.hlsPath);
        const manifestBuffer = await StreamUtils.streamToBuffer(manifestStream);
        const manifestContent = manifestBuffer.toString('utf-8');

        // Load segment files from manifest
        const segmentFiles = manifestContent.match(/(\d+\.ts)/g) || [];

        // Generate signed URLs for segment files
        const signedUrls: Record<string, string> = {};
        for (const segment of segmentFiles) {
            const segmentPath = `${path.dirname(video.hlsPath)}/segments/${segment}`;
            signedUrls[segment] =  await minioClient.presignedUrl('GET', bucketName, segmentPath, 24 * 60 * 60);
        }

        // Update manifest file with signed URLs
        let updatedContent = manifestContent;
        for (const [segment, signedUrl] of Object.entries(signedUrls)) {
            updatedContent = updatedContent.replace(new RegExp(segment, 'g'), signedUrl);
        }

        res.set('Content-Type', 'application/vnd.apple.mpegurl');
        res.send(updatedContent);
    } catch (error) {
        console.error('Error generating HLS manifest:', error);
        res.status(500).send('Internal Server Error');
    }
};

export const getThumbnailRoute = async (req: Request, res: Response): Promise<void> => {
    const videoId = req.params.id;
    const thumbNumber = req.params.thumbNumber;

    try {
        const video = await getVideo(videoId);

        if (!video || !video.thumbnailPath) {
            res.status(404).json({ error: "Thumbnail not found" });
            return;
        }

        const thumbnailPath = `${video.thumbnailPath}/thumb_${thumbNumber}.png`;

        // Fetch the thumbnail from Minio
        const objectStream = await minioClient.getObject(bucketName, thumbnailPath);

        // Convert stream to buffer
        const chunks = [];
        for await (const chunk of objectStream) {
            chunks.push(chunk);
        }
        const buffer = Buffer.concat(chunks);

        // Set the appropriate content type
        res.set('Content-Type', 'image/png');
        res.send(buffer);
    } catch (error) {
        console.error('Error fetching thumbnail:', error);
        res.status(500).send('Internal Server Error');
    }
};
