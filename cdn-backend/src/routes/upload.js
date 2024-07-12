const express = require('express');
const multer = require('multer');
const uploadController = require("../controllers/uploadController");
const verifySignature = require('../middleware/verifySignature');

const router = express.Router();
const upload = multer();

router.post('/', upload.single('file'), verifySignature, uploadController.uploadVideo);

module.exports = router;
