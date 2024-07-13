import { Router } from 'express';
import { createProjectRoute, updateProjectRoute } from '../controllers/projectController';

const router = Router();

router.post('/', createProjectRoute);
router.put('/:id', updateProjectRoute);

export default router;
