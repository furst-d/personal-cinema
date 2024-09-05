import { Request, Response } from 'express';
import authMiddleware from '../../src/middleware/auth';
import Project from '../../src/entities/project';

jest.mock('../../src/entities/project', () => ({
    findOne: jest.fn(),
}));

describe('authMiddleware', () => {
    let req: Partial<Request>;
    let res: Partial<Response>;
    let next: jest.Mock;

    beforeEach(() => {
        req = {
            headers: {},
        };
        res = {
            status: jest.fn().mockReturnThis(),
            json: jest.fn(),
        };
        next = jest.fn();
    });

    it('should return 401 if authorization header is missing', async () => {
        await authMiddleware(req as Request, res as Response, next);

        expect(res.status).toHaveBeenCalledWith(401);
        expect(res.json).toHaveBeenCalledWith({ error: 'Authorization header is missing' });
        expect(next).not.toHaveBeenCalled();
    });

    it('should return 401 if authorization format is invalid', async () => {
        req.headers!['authorization'] = 'InvalidTokenFormat';

        await authMiddleware(req as Request, res as Response, next);

        expect(res.status).toHaveBeenCalledWith(401);
        expect(res.json).toHaveBeenCalledWith({ error: 'Invalid authorization format' });
        expect(next).not.toHaveBeenCalled();
    });

    it('should return 401 if token is invalid', async () => {
        req.headers!['authorization'] = 'Bearer invalidtoken';
        (Project.findOne as jest.Mock).mockResolvedValue(null);

        await authMiddleware(req as Request, res as Response, next);

        expect(res.status).toHaveBeenCalledWith(401);
        expect(res.json).toHaveBeenCalledWith({ error: 'Invalid token' });
        expect(next).not.toHaveBeenCalled();
    });

    it('should call next if token is valid', async () => {
        const mockProject = { id: 1, apiKey: 'validtoken' };
        req.headers!['authorization'] = 'Bearer validtoken';
        (Project.findOne as jest.Mock).mockResolvedValue(mockProject);

        await authMiddleware(req as Request, res as Response, next);

        expect(Project.findOne).toHaveBeenCalledWith({ where: { apiKey: 'validtoken' } });
        expect(req).toHaveProperty('project', mockProject);
        expect(next).toHaveBeenCalled();
    });
});
