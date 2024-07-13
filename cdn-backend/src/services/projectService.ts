import { v4 as uuidv4 } from 'uuid';
import Project from '../entities/project';
import Callback from '../entities/callback';

interface ProjectData {
    name: string;
    notificationUrl: string;
    thumbUrl: string;
}

interface UpdateProjectData {
    name?: string;
    notificationUrl?: string;
    thumbUrl?: string;
}

export const createProject = async (data: ProjectData) => {
    const apiKey = uuidv4();
    const callback = await Callback.create({
        id: uuidv4(),
        notificationUrl: data.notificationUrl,
        thumbUrl: data.thumbUrl,
    });

    return await Project.create({
        id: uuidv4(),
        name: data.name,
        apiKey: apiKey,
        callbackId: callback.id,
    });
};

export const getProjectByApiKey = async (apiKey: string) => {
    return await Project.findOne({
        where: { apiKey },
        include: [Callback],
    });
};

export const updateProject = async (id: string, data: UpdateProjectData) => {
    const project = await Project.findByPk(id);
    if (!project) {
        throw new Error('Project not found');
    }

    if (data.notificationUrl || data.thumbUrl) {
        const callback = await Callback.findByPk(project.callbackId);
        if (callback) {
            callback.notificationUrl = data.notificationUrl || callback.notificationUrl;
            callback.thumbUrl = data.thumbUrl || callback.thumbUrl;
            await callback.save();
        }
    }

    project.name = data.name || project.name;
    await project.save();
    return project;
};
