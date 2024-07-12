const videoService = require('../services/videoService');

exports.uploadVideo = async (req, res) => {
    try {
        // const video = await videoService.uploadVideo(req.body);
        res.status(201).json({ message: 'Ok' });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};
