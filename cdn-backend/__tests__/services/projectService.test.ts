import { getProjectByApiKey, updateProject } from '../../src/services/projectService';
import Project from '../../src/entities/project';
import Callback from '../../src/entities/callback';

jest.mock('uuid', () => ({
    v4: jest.fn(),
}));
jest.mock('../../src/entities/project');
jest.mock('../../src/entities/callback');

describe('Project Service', () => {
    const mockProject = { id: 'project1', name: 'Test Project', apiKey: 'apikey123', callbackId: 'callback1', save: jest.fn() };
    const mockCallback = { id: 'callback1', notificationUrl: 'http://example.com/notify', thumbUrl: 'http://example.com/thumb', save: jest.fn() };

    beforeEach(() => {
        jest.clearAllMocks();
    });

    describe('getProjectByApiKey', () => {
        it('should return project by API key', async () => {
            (Project.findOne as jest.Mock).mockResolvedValue(mockProject);

            const result = await getProjectByApiKey('apikey123');

            expect(Project.findOne).toHaveBeenCalledWith({
                where: { apiKey: 'apikey123' },
                include: [Callback],
            });
            expect(result).toEqual(mockProject);
        });

        it('should return null if project is not found', async () => {
            (Project.findOne as jest.Mock).mockResolvedValue(null);

            const result = await getProjectByApiKey('nonexistent-key');

            expect(result).toBeNull();
        });
    });

    describe('updateProject', () => {
        it('should update the project and associated callback', async () => {
            (Project.findByPk as jest.Mock).mockResolvedValue(mockProject);
            (Callback.findByPk as jest.Mock).mockResolvedValue(mockCallback);

            const updateData = {
                name: 'Updated Project',
                notificationUrl: 'http://example.com/new-notify',
            };

            const result = await updateProject('project1', updateData);

            expect(Callback.findByPk).toHaveBeenCalledWith(mockProject.callbackId);
            expect(mockCallback.notificationUrl).toBe('http://example.com/new-notify');
            expect(mockCallback.thumbUrl).toBe(mockCallback.thumbUrl); // thumbUrl remains unchanged
            expect(mockCallback.save).toHaveBeenCalled();

            expect(mockProject.name).toBe('Updated Project');
            expect(mockProject.save).toHaveBeenCalled();
            expect(result).toEqual(mockProject);
        });

        it('should throw an error if project is not found', async () => {
            (Project.findByPk as jest.Mock).mockResolvedValue(null);

            await expect(updateProject('invalid-id', { name: 'Invalid' })).rejects.toThrow('Project not found');
        });
    });
});
