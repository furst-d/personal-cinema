const videoService = require('../services/videoService');

exports.uploadVideo = async (req, res) => {
    try {
        const file = req.file;

        if (!file) {
            return res.status(400).json({ error: 'No file uploaded' });
        }

        const video = await videoService.uploadVideo(file, req.body.params, req.body.project_id );
        res.status(201).json(video);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};
