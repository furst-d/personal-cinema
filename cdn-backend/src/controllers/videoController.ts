import { Request, Response } from 'express';
import {getSignedThumbnails, getVideo, prepareVideoData} from "../services/videoService";
import Video from "../entities/video";
import minioClient, { bucketName } from "../config/minio";
import path from "path";
import {StreamUtils} from "../helpers/video/StreamUtils";
import {isUUID} from "validator";
import {VideoProcessingUtils} from "../helpers/video/VideoProcessingUtils";

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
    const quality = req.query.quality as string;

    if (!quality) {
        res.status(400).json({ error: "Quality parameter is required" });
        return;
    }

    const video = await getVideo(videoId);

    if (!video.hlsPath) {
        res.status(404).json({ error: "Video not processed" });
        return;
    }

    const manifestPath = `${video.hlsPath}/${quality}.m3u8`;
    console.log("manifestPath", manifestPath);

    try {
        // Load manifest file from Minio
        const manifestStream = await minioClient.getObject(bucketName, manifestPath);
        const manifestBuffer = await StreamUtils.streamToBuffer(manifestStream);
        const manifestContent = manifestBuffer.toString('utf-8');

        // Load segment files from manifest
        const segmentFiles = manifestContent.match(/(\d+\.ts)/g) || [];
        console.log("segmentFiles", segmentFiles);

        // Generate signed URLs for segment files
        const signedUrls: Record<string, string> = {};
        for (const segment of segmentFiles) {
            const segmentPath = `${video.hlsPath}/segments/${quality}/${segment}`;
            signedUrls[segment] =  await minioClient.presignedUrl('GET', bucketName, segmentPath, 24 * 60 * 60);
        }
        console.log("signedUrls", signedUrls);

        // Update manifest file with signed URLs
        let updatedContent = manifestContent;
        for (const [segment, signedUrl] of Object.entries(signedUrls)) {
            updatedContent = updatedContent.replace(new RegExp(segment, 'g'), signedUrl);
        }

        res.set('Content-Type', 'application/vnd.apple.mpegurl');
        res.send(updatedContent);
    } catch (error: any) {
        if (error.code === 'NoSuchKey') {
            res.status(404).json({ error: "Quality not found" });
            return;
        }

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

export const getVideoDownloadLinkRoute = async (req: Request, res: Response): Promise<void> => {
    const videoId = req.params.id;

    try {
        const video = await Video.findByPk(videoId);
        if (!video) {
            res.status(404).json({ error: "Video not found" });
            return;
        }

        if (!video.originalPath) {
            res.status(404).json({ error: "Video not processed" });
            return;
        }

        const signedUrl = await minioClient.presignedUrl('GET', bucketName, video.originalPath, 24 * 60 * 60, {
            "response-content-disposition": `attachment; filename="${encodeURIComponent(video.title)}"`
        });

        res.status(200).json({ downloadLink: signedUrl });
    } catch (error) {
        console.error('Error generating download link:', error);
        res.status(500).send('Internal Server Error');
    }
};

export const batchDeleteVideos = async (req: Request, res: Response): Promise<void> => {
    const { videoIds } = req.body;

    if (!videoIds || !Array.isArray(videoIds)) {
        res.status(400).json({ error: "Invalid video IDs" });
        return;
    }

    const validVideoIds = videoIds.filter((id: string) => isUUID(id));

    const videos = await Video.findAll({
        where: {
            id: validVideoIds
        }
    });

    for (const video of videos) {
        await VideoProcessingUtils.markForDeletion(video);
    }

    res.status(200).json({ message: "Videos were marked for deletion" });
}
