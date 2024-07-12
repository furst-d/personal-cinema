const Project = require('../entities/project');

const authMiddleware = async (req, res, next) => {
    const apiKey = req.headers['x-api-key'];

    if (!apiKey) {
        return res.status(401).json({ error: 'No API key provided' });
    }

    const project = await Project.findOne({ where: { apiKey } });

    if (!project) {
        return res.status(401).json({ error: 'Invalid API key' });
    }

    req.project = project;
    next();
};

module.exports = authMiddleware;
