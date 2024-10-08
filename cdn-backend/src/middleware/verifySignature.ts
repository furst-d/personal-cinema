import { Request, Response, NextFunction } from 'express';
import crypto from 'crypto';
import Project from '../entities/project';
import Nonce from '../entities/nonce';
import http_build_query from '../utils/http_build_query';

const verifySignature = async (req: Request, res: Response, next: NextFunction): Promise<Response | void> => {
    const { nonce, params, signature, projectId, fileSize } = req.body;

    if (!nonce || !params || !signature || !projectId) {
        return res.status(400).json({ error: 'Missing required parameters' });
    }

    const existingNonce = await Nonce.findOne({ where: { value: nonce } });
    if (existingNonce) {
        return res.status(400).json({ error: 'Nonce already used' });
    }

    const project = await Project.findByPk(projectId);

    if (!project) {
        return res.status(401).json({ error: 'Project not authenticated' });
    }

    const secretKey = project.apiKey;
    const data: any = { nonce, params, projectId };

    if (fileSize) {
        data.size = fileSize;
    }

    const paramString = http_build_query(data);
    const expectedSignature = crypto.createHmac('sha256', secretKey).update(paramString).digest('hex');

    if (expectedSignature !== signature) {
        return res.status(400).json({ error: 'Invalid signature' });
    }

    await Nonce.create({ value: nonce });

    next();
};

export default verifySignature;
