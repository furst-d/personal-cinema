const videoService = require('../services/videoService');

exports.uploadVideo = async (req, res) => {
    try {
        const file = req.file;
        const params = req.body.params;

        if (!file) {
            return res.status(400).json({ error: 'No file uploaded' });
        }

        const video = await videoService.uploadVideo(file, params);
        res.status(201).json(video);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};
