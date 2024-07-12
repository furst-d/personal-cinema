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
    const objectName = `${videoId}/${hash}${extension}`;
    const url = `http://${process.env.MINIO_ENDPOINT}:${process.env.MINIO_PORT}/${objectName}`;

    try {
        await minioClient.putObject('videos', objectName, buffer, size, {
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
        originalUrl: url,
        hash: hash,
        extension: extension,
        size: size,
        projectId: project_id,
        parameters: JSON.parse(params)
    });
};

exports.getVideo = async (id) => {
    const video = await Video.findByPk(id, { include: Md5 });
    if (!video) {
        throw new Error('Video not found');
    }
    return video;
};
