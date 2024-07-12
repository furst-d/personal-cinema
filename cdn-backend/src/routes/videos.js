const express = require('express');
const videoController = require('../controllers/videoController');
const authMiddleware = require('../middleware/auth');

const router = express.Router();

router.get('/:id', authMiddleware, videoController.getVideo);
router.get('/:id/url', authMiddleware, videoController.getVideoUrl);

module.exports = router;
