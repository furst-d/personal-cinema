import express, { Router } from 'express';
import { uploadVideoRoute } from "../controllers/uploadController";
import verifySignature from '../middleware/verifySignature';
import {uploadVideoMiddleware} from "../middleware/upload";

const router: Router = express.Router();

router.post('/', uploadVideoMiddleware, verifySignature, uploadVideoRoute);

export default router;
