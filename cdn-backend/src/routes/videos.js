const express = require('express');
const videoController = require('../controllers/videoController');
const authMiddleware = require('../middleware/auth');

const router = express.Router();

router.get('/:id', authMiddleware, videoController.getVideo);

module.exports = router;
