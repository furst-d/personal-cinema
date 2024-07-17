import express, { Router } from 'express';
import { getVideoRoute, getVideoUrlRoute } from '../controllers/videoController';
import authMiddleware from '../middleware/auth';

const router: Router = express.Router();

router.get('/:id', authMiddleware, getVideoRoute);
router.get('/:id/file.m3u8', authMiddleware, getVideoUrlRoute);

export default router;