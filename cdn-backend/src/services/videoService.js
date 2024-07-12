const Video = require('../entities/video');
const Md5 = require('../entities/md5');
const minioClient = require('../config/minio');
const { v4: uuidv4 } = require('uuid');
const path = require('path');
const crypto = require('crypto');

exports.uploadVideo = async (file, params, project_id) => {
    const { originalname, buffer, mimetype, size } = file;

    const extension = path.extname(originalname);
    const hash = crypto.createHash('md5').update(buffer).digest('hex');

    const videoId = uuidv4();
    const urlPath = `${videoId}/${hash}${extension}`;

    try {
        await minioClient.putObject('videos', urlPath, buffer, size, {
            'Content-Type': mimetype
        });
    } catch (error) {
        console.error('Error uploading to Minio:', error);
        throw new Error('Error uploading to storage server');
    }

    return await Video.create({
        id: videoId,
        title: originalname,
        status: 'uploaded-original',
        originalPath: urlPath,
        hash: hash,
        extension: extension,
        size: size,
        projectId: project_id,
        parameters: JSON.parse(params)
    });
};

exports.getVideoUrl = async (videoId) => {
    const video = await Video.findByPk(videoId);
    if (!video) {
        throw new Error('Video not found');
    }

    const objectName = `${video.id}/${video.hash}${video.extension}`;
    const url = await minioClient.presignedUrl('GET', 'videos', objectName, 24 * 60 * 60); // 24 hour

    return url;
};

exports.getVideo = async (id) => {
    const video = await Video.findByPk(id, { include: Md5 });
    if (!video) {
        throw new Error('Video not found');
    }
    return video;
};
