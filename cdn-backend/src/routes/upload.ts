import express, { Router } from 'express';
import multer from 'multer';
import { uploadVideoRoute } from "../controllers/uploadController";
import verifySignature from '../middleware/verifySignature';

const router: Router = express.Router();
const upload = multer();

router.post('/', upload.single('file'), verifySignature, uploadVideoRoute);

export default router;
