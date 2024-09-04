import { Router } from 'express';
import { createProjectRoute, updateProjectRoute } from '../controllers/projectController';
import authMiddleware from "../middleware/auth";

const router = Router();

router.post('/', authMiddleware, createProjectRoute);
router.put('/:id', authMiddleware, updateProjectRoute);

export default router;
