import path from "path";
import { mkdirSync } from 'fs';
import * as fs from 'fs/promises';
import ffmpeg from 'fluent-ffmpeg';
import { PassThrough } from 'stream';

const tempDir = path.resolve(__dirname, '../../../temp');

export class VideoProcessingUtils {
    static ensureTempDir(videoId: string) {
        const videoTempDir = path.join(tempDir, videoId);
        mkdirSync(videoTempDir, { recursive: true });
        return videoTempDir;
    }

    static async cleanUpTempSubDir(videoId: string, subDir: string) {
        try {
            const dirPath = path.join(tempDir, videoId, subDir);
            await fs.rm(dirPath, { recursive: true, force: true });

            const videoTempDir = path.join(tempDir, videoId);
            const remainingFiles = await fs.readdir(videoTempDir);
            if (remainingFiles.length === 0) {
                await fs.rmdir(videoTempDir);
            }
        } catch (error) {
            console.error(`Error cleaning up ${subDir} directory:`, error);
        }
    }

    static calculateMd5(videoUrl: string): Promise<string> {
        return new Promise((resolve, reject) => {
            let md5Hash = '';
            ffmpeg(videoUrl)
                .outputOptions('-f', 'md5')
                .on('end', () => {
                    resolve(md5Hash);
                })
                .on('error', (err) => {
                    console.error('Error calculating MD5 hash:', err);
                    reject(err);
                })
                .pipe(new PassThrough().on('data', (chunk) => {
                    md5Hash += chunk.toString().split('=')[1]?.trim();
                }));
        });
    }
}
