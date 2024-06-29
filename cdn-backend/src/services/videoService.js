const Video = require('../entities/video');
const Md5 = require('../entities/md5');
const { v4: uuidv4 } = require('uuid');

exports.uploadVideo = async (data) => {
    let md5Entry = await Md5.findOne({ where: { md5: data.md5 } });
    if (!md5Entry) {
        md5Entry = await Md5.create({
            id: uuidv4(),
            md5: data.md5,
            isBlacklisted: false
        });
    }

    const video = await Video.create({
        id: uuidv4(),
        title: data.title,
        status: 'processing',
        extension: data.extension,
        size: data.size,
        length: data.length,
        resolution: data.resolution,
        parameters: data.parameters,
        md5Id: md5Entry.id
    });

    // Add logic to upload the video to MinIO and process it
    return video;
};

exports.getVideo = async (id) => {
    const video = await Video.findByPk(id, { include: Md5 });
    if (!video) {
        throw new Error('Video not found');
    }
    return video;
};
