const crypto = require('crypto');
const Project = require('../entities/project');
const Nonce = require('../entities/nonce');

/**
 * Function to build a query string from an object
 * @param obj
 * @param prefix
 * @returns {string}
 */
const http_build_query = (obj, prefix) => {
    const str = [];
    for (const p in obj) {
        if (obj.hasOwnProperty(p)) {
            const k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
            str.push((v !== null && typeof v === "object") ?
                http_build_query(v, k) :
                encodeURIComponent(k) + "=" + encodeURIComponent(v));
        }
    }
    return str.join("&");
};

/**
 * Middleware to verify the signature of the request
 * @param req
 * @param res
 * @param next
 * @returns {Promise<*>}
 */
const verifySignature = async (req, res, next) => {
    const { nonce, params, signature, project_id } = req.body;

    if (!nonce || !params || !signature) {
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
    const paramString = http_build_query(data);
    const expectedSignature = crypto.createHmac('sha256', secretKey).update(paramString).digest('hex');

    if (expectedSignature !== signature) {
        return res.status(400).json({ error: 'Invalid signature' });
    }

    await Nonce.create({ value: nonce });

    next();
};

module.exports = verifySignature;
