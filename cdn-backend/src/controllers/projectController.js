const projectService = require('../services/projectService');

exports.createProject = async (req, res) => {
    try {
        const project = await projectService.createProject(req.body);
        res.status(201).json(project);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

exports.updateProject = async (req, res) => {
    try {
        const project = await projectService.updateProject(req.params.id, req.body);
        res.status(200).json(project);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};
