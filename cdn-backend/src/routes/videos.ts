import express, { Router } from 'express';
import { getVideoRoute, getVideoUrlRoute } from '../controllers/videoController';
import authMiddleware from '../middleware/auth';

const router: Router = express.Router();

router.get('/:id', authMiddleware, getVideoRoute);
router.get('/:id/sign', authMiddleware, getVideoUrlRoute);

export default router;