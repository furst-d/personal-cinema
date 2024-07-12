const Minio = require('minio');

const minioClient = new Minio.Client({
    endPoint: process.env.MINIO_ENDPOINT,
    port: parseInt(process.env.MINIO_PORT),
    useSSL: process.env.MINIO_USE_SSL === 'true',
    accessKey: process.env.MINIO_ROOT_USER,
    secretKey: process.env.MINIO_ROOT_PASSWORD
});

const bucketName = 'videos';
const region = 'eu-central-1';

const initMinio = async () => {
    try {
        const exists = await minioClient.bucketExists(bucketName);
        if (!exists) {
            await minioClient.makeBucket(bucketName, region);
            console.log(`Bucket ${bucketName} created successfully in region ${region}`);
        }
    } catch (error) {
        console.error(`Error creating bucket ${bucketName}:`, error);
    }
};

initMinio();

module.exports = minioClient;
