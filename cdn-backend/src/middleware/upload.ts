// middlewares/uploadVideoMiddleware.ts
import multer, { FileFilterCallback } from 'multer';
import { Request, Response, NextFunction } from 'express';
import { getVideoSizeLimit } from '../services/settingsService';

const storage = multer.memoryStorage();

const fileFilter = (req: Request, file: Express.Multer.File, callback: FileFilterCallback): void => {
    if (file.mimetype.startsWith('video/')) {
        callback(null, true);
    } else {
        const error = new Error('Only video files are allowed') as any;
        error.tag = 'INVALID_FILE_TYPE';
        callback(error as null, false);
    }
};

const getMulterUpload = async (): Promise<multer.Multer> => {
    const sizeLimit = await getVideoSizeLimit();
    return multer({
        storage: storage,
        fileFilter: fileFilter,
        limits: { fileSize: sizeLimit }
    });
};

export const uploadVideoMiddleware = async (req: Request, res: Response, next: NextFunction) => {
    try {
        const upload = await getMulterUpload();
        upload.single('video')(req, res, (err: any) => {
            if (err instanceof multer.MulterError) {
                res.status(400).json({ error: err.message, tag: err.code });
            } else if (err) {
                res.status(400).json({ error: err.message, tag: err.tag || 'UNKNOWN_ERROR' });
            } else {
                if (req.file) {
                    req.body.fileSize = req.file.size;
                }
                next();
            }
        });
    } catch (error) {
        res.status(500).json({ error: 'Internal server error' });
    }
};
