const videoService = require('../services/videoService');

exports.uploadVideo = async (req, res) => {
    try {
        const video = await videoService.uploadVideo(req.body);
        res.status(201).json(video);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

exports.getVideo = async (req, res) => {
    try {
        const video = await videoService.getVideo(req.params.id);
        res.status(200).json(video);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};
