import { Request, Response, NextFunction } from 'express';
import Project from '../entities/project';

interface AuthenticatedRequest extends Request {
    project?: Project;
}

const authMiddleware = async (req: AuthenticatedRequest, res: Response, next: NextFunction) => {
    const authHeader = req.headers['authorization'];

    if (!authHeader) {
        return res.status(401).json({ error: 'Authorization header is missing' });
    }

    const [type, token] = authHeader.split(' ');

    if (type !== 'Bearer' || !token) {
        return res.status(401).json({ error: 'Invalid authorization format' });
    }

    const project = await Project.findOne({ where: { apiKey: token } });

    if (!project) {
        return res.status(401).json({ error: 'Invalid token' });
    }

    req.project = project;
    next();
};

export default authMiddleware;
