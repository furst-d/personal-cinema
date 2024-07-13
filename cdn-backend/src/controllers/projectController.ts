import { Request, Response } from 'express';
import { createProject, updateProject } from '../services/projectService';

export const createProjectRoute = async (req: Request, res: Response): Promise<void> => {
    try {
        const project = await createProject(req.body);
        res.status(201).json(project);
    } catch (error) {
        res.status(500).json({ error: (error as Error).message });
    }
};

export const updateProjectRoute = async (req: Request, res: Response): Promise<void> => {
    try {
        const project = await updateProject(req.params.id, req.body);
        res.status(200).json(project);
    } catch (error) {
        res.status(500).json({ error: (error as Error).message });
    }
};
