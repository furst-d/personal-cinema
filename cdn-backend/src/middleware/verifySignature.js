const crypto = require('crypto');
const Project = require('../entities/project');
const Nonce = require('../entities/nonce');
const http_build_query = require('../utils/http_build_query');

/**
 * Middleware to verify the signature of the request
 * @param req
 * @param res
 * @param next
 * @returns {Promise<*>}
 */
const verifySignature = async (req, res, next) => {
    const { nonce, params, signature, project_id } = req.body;

    if (!nonce || !params || !signature || !project_id) {
        return res.status(400).json({ error: 'Missing required parameters' });
    }

    const existingNonce = await Nonce.findOne({ where: { value: nonce } });
    if (existingNonce) {
        return res.status(400).json({ error: 'Nonce already used' });
    }

    const project = await Project.findByPk(project_id);

    if (!project) {
        return res.status(401).json({ error: 'Project not authenticated' });
    }

    const secretKey = project.apiKey;
    const data = { nonce, params, project_id };
    const paramString = http_build_query(data, null);
    const expectedSignature = crypto.createHmac('sha256', secretKey).update(paramString).digest('hex');

    if (expectedSignature !== signature) {
        return res.status(400).json({ error: 'Invalid signature' });
    }

    await Nonce.create({ value: nonce });

    next();
};

module.exports = verifySignature;
