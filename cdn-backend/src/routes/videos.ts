import express, { Router } from 'express';
import {
    getVideoDownloadLinkRoute,
    getThumbnailRoute,
    getThumbsRoute,
    getVideoRoute,
    getVideoUrlRoute, batchDeleteVideos,
} from '../controllers/videoController';
import authMiddleware from '../middleware/auth';

const router: Router = express.Router();

router.get('/:id', authMiddleware, getVideoRoute);
router.get('/:id/file.m3u8', authMiddleware, getVideoUrlRoute);
router.get('/:id/thumbs', authMiddleware, getThumbsRoute);
router.get('/:id/thumbs/:thumbNumber', getThumbnailRoute);
router.get('/:id/download', getVideoDownloadLinkRoute);
router.delete('/', authMiddleware, batchDeleteVideos);

export default router;