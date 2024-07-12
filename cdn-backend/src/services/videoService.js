const Video = require('../entities/video');
const Md5 = require('../entities/md5');
const Minio = require('minio');
const { v4: uuidv4 } = require('uuid');
const path = require('path');
const crypto = require('crypto');

const minioClient = new Minio.Client({
    endPoint: process.env.MINIO_ENDPOINT,
    port: parseInt(process.env.MINIO_PORT),
    useSSL: process.env.MINIO_USE_SSL === 'true',
    accessKey: process.env.MINIO_ACCESS_KEY,
    secretKey: process.env.MINIO_SECRET_KEY
});

exports.uploadVideo = async (file, params) => {
    const { originalname, buffer, mimetype, size } = file;

    const extension = path.extname(originalname);
    const hash = crypto.createHash('md5').update(buffer).digest('hex');

    const videoId = uuidv4();
    const objectName = `${videoId}/${hash}${extension}`;
    const url = `http://${process.env.MINIO_ENDPOINT}:${process.env.MINIO_PORT}/${objectName}`;

    await minioClient.putObject('videos', objectName, buffer, size, {
        'Content-Type': mimetype
    });

    return await Video.create({
        id: videoId,
        title: params.name,
        status: 'uploaded-original',
        originalUrl: url,
        hash: hash,
        extension: extension,
        size: size,
        projectId: params.project_id
    });
};

exports.getVideo = async (id) => {
    const video = await Video.findByPk(id, { include: Md5 });
    if (!video) {
        throw new Error('Video not found');
    }
    return video;
};
