import { Request, Response } from 'express';
import verifySignature from '../../src/middleware/verifySignature';
import Project from '../../src/entities/project';
import Nonce from '../../src/entities/nonce';
// @ts-ignore
import crypto from 'crypto';

jest.mock('../../src/entities/project');
jest.mock('../../src/entities/nonce');

describe('verifySignature middleware', () => {
    let req: Partial<Request>;
    let res: Partial<Response>;
    let next: jest.Mock;

    beforeEach(() => {
        req = {
            body: {},
        };
        res = {
            status: jest.fn().mockReturnThis(),
            json: jest.fn(),
        };
        next = jest.fn();
        jest.clearAllMocks();
    });

    it('should return 400 if required parameters are missing', async () => {
        req.body = {
            nonce: 'abc123',
            signature: 'testsignature',
        };

        await verifySignature(req as Request, res as Response, next);

        expect(res.status).toHaveBeenCalledWith(400);
        expect(res.json).toHaveBeenCalledWith({ error: 'Missing required parameters' });
        expect(next).not.toHaveBeenCalled();
    });

    it('should return 400 if nonce is already used', async () => {
        req.body = {
            nonce: 'abc123',
            params: 'someparams',
            signature: 'testsignature',
            projectId: '1',
        };

        (Nonce.findOne as jest.Mock).mockResolvedValue({ value: 'abc123' }); // Simulate used nonce

        await verifySignature(req as Request, res as Response, next);

        expect(res.status).toHaveBeenCalledWith(400);
        expect(res.json).toHaveBeenCalledWith({ error: 'Nonce already used' });
        expect(next).not.toHaveBeenCalled();
    });

    it('should return 401 if project is not authenticated', async () => {
        req.body = {
            nonce: 'abc123',
            params: 'someparams',
            signature: 'testsignature',
            projectId: '1',
        };

        (Nonce.findOne as jest.Mock).mockResolvedValue(null); // Nonce not used
        (Project.findByPk as jest.Mock).mockResolvedValue(null); // Project not found

        await verifySignature(req as Request, res as Response, next);

        expect(res.status).toHaveBeenCalledWith(401);
        expect(res.json).toHaveBeenCalledWith({ error: 'Project not authenticated' });
        expect(next).not.toHaveBeenCalled();
    });

    it('should call next if all checks pass', async () => {
        req.body = {
            nonce: 'abc123',
            params: 'someparams',
            signature: 'validsignature',
            projectId: '1',
        };

        const mockProject = { apiKey: 'secretkey' };
        (Nonce.findOne as jest.Mock).mockResolvedValue(null); // Nonce not used
        (Project.findByPk as jest.Mock).mockResolvedValue(mockProject); // Valid project

        const paramString = `nonce=abc123&params=someparams&projectId=1`; // Real query string
        const validSignature = crypto.createHmac('sha256', mockProject.apiKey).update(paramString).digest('hex');

        req.body.signature = validSignature;

        await verifySignature(req as Request, res as Response, next);

        expect(Nonce.create).toHaveBeenCalledWith({ value: 'abc123' });
        expect(next).toHaveBeenCalled();
    });
});
