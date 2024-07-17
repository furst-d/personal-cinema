import { Request, Response } from 'express';
import { getVideo } from "../services/videoService";
import Video from "../entities/video";
import minioClient, { bucketName } from "../config/minio";
import path from "path";

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

    if (!video.hlsPath) {
        res.status(404).json({ error: "Video not processed" });
        return;
    }

    try {
        // Načíst manifestový soubor z Minio
        const manifestStream = await minioClient.getObject(bucketName, video.hlsPath);
        const manifestBuffer = await streamToBuffer(manifestStream);
        const manifestContent = manifestBuffer.toString('utf-8');

        // Načíst seznam segmentů
        const segmentFiles = manifestContent.match(/(\d+\.ts)/g) || [];

        // Generovat podepsané URL pro segmenty
        const signedUrls: Record<string, string> = {};
        for (const segment of segmentFiles) {
            const segmentPath = `${path.dirname(video.hlsPath)}/segments/${segment}`;
            const signedUrl = await minioClient.presignedUrl('GET', bucketName, segmentPath, 24 * 60 * 60);
            signedUrls[segment] = signedUrl;
        }

        // Aktualizovat manifestový soubor s podepsanými URL
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

const streamToBuffer = async (stream: NodeJS.ReadableStream): Promise<Buffer> => {
    return new Promise((resolve, reject) => {
        const chunks: Buffer[] = [];
        stream.on('data', (chunk) => chunks.push(Buffer.from(chunk)));
        stream.on('end', () => resolve(Buffer.concat(chunks)));
        stream.on('error', (err) => reject(err));
    });
};

