const Project = require('../entities/project');
const Callback = require('../entities/callback');
const { v4: uuidv4 } = require('uuid');

exports.createProject = async (data) => {
    const apiKey = uuidv4();
    const callback = await Callback.create({
        notificationUrl: data.notificationUrl,
        thumbUrl: data.thumbUrl,
    });
    const project = await Project.create({
        id: uuidv4(),
        name: data.name,
        apiKey: apiKey,
        callbackId: callback.id,
    });
    return project;
};

exports.getProjectByApiKey = async (apiKey) => {
    const project = await Project.findOne({
        where: { apiKey },
        include: [Callback],
    });
    return project;
};

exports.updateProject = async (id, data) => {
    const project = await Project.findByPk(id);
    if (!project) {
        throw new Error('Project not found');
    }

    if (data.notificationUrl || data.thumbUrl) {
        const callback = await Callback.findByPk(project.callbackId);
        callback.notificationUrl = data.notificationUrl || callback.notificationUrl;
        callback.thumbUrl = data.thumbUrl || callback.thumbUrl;
        await callback.save();
    }

    project.name = data.name || project.name;
    await project.save();
    return project;
};
