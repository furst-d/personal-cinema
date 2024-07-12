const videoService = require('../services/videoService');

exports.getVideo = async (req, res) => {
    try {
        const video = await videoService.getVideo(req.params.id);
        res.status(200).json(video);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

exports.getVideoUrl = async (req, res) => {
    try {
        const videoId = req.params.id;
        const url = await videoService.getVideoUrl(videoId);
        res.status(200).json({ url });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};
