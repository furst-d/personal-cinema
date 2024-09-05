// @ts-ignore
import request from 'supertest';
// @ts-ignore
import express, { Request, Response } from 'express';
import { createProject, updateProject } from '../../src/services/projectService';
import { createProjectRoute, updateProjectRoute } from '../../src/controllers/projectController';

jest.mock('../../src/services/projectService', () => ({
    createProject: jest.fn(),
    updateProject: jest.fn(),
}));

const app = express();
app.use(express.json());

app.post('/projects', createProjectRoute);
app.put('/projects/:id', updateProjectRoute);

describe('Project Controller', () => {
    beforeEach(() => {
        jest.clearAllMocks();
    });

    describe('POST /projects', () => {
        it('should create a project and return 201 status', async () => {
            const mockProject = { id: 'project-uuid', name: 'New Project' };
            (createProject as jest.Mock).mockResolvedValue(mockProject);

            const res = await request(app)
                .post('/projects')
                .send({ name: 'New Project', notificationUrl: 'http://example.com/notify', thumbUrl: 'http://example.com/thumb' });

            expect(res.status).toBe(201);
            expect(res.body).toEqual(mockProject);
            expect(createProject).toHaveBeenCalledWith({
                name: 'New Project',
                notificationUrl: 'http://example.com/notify',
                thumbUrl: 'http://example.com/thumb',
            });
        });

        it('should return 500 if an error occurs while creating the project', async () => {
            (createProject as jest.Mock).mockRejectedValue(new Error('Database error'));

            const res = await request(app)
                .post('/projects')
                .send({ name: 'New Project' });

            expect(res.status).toBe(500);
            expect(res.body).toEqual({ error: 'Database error' });
        });
    });

    describe('PUT /projects/:id', () => {
        it('should update a project and return 200 status', async () => {
            const mockProject = { id: 'project-uuid', name: 'Updated Project' };
            (updateProject as jest.Mock).mockResolvedValue(mockProject);

            const res = await request(app)
                .put('/projects/project-uuid')
                .send({ name: 'Updated Project' });

            expect(res.status).toBe(200);
            expect(res.body).toEqual(mockProject);
            expect(updateProject).toHaveBeenCalledWith('project-uuid', { name: 'Updated Project' });
        });

        it('should return 500 if an error occurs while updating the project', async () => {
            (updateProject as jest.Mock).mockRejectedValue(new Error('Database error'));

            const res = await request(app)
                .put('/projects/project-uuid')
                .send({ name: 'Updated Project' });

            expect(res.status).toBe(500);
            expect(res.body).toEqual({ error: 'Database error' });
        });
    });
});
