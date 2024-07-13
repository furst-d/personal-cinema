import { Client } from 'minio';

const minioClient = new Client({
    endPoint: process.env.MINIO_ENDPOINT as string,
    port: parseInt(process.env.MINIO_PORT as string, 10),
    useSSL: process.env.MINIO_USE_SSL === 'true',
    accessKey: process.env.MINIO_ROOT_USER as string,
    secretKey: process.env.MINIO_ROOT_PASSWORD as string
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

export default minioClient;
