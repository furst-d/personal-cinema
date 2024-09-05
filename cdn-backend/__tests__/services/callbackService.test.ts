import axios from 'axios';
import { sendNotificationCallback, sendDeleteNotificationCallback, sendThumbnailCallback } from '../../src/services/callbackService';
import { getVideo, prepareVideoData, getSignedThumbnails } from '../../src/services/videoService';
import Project from '../../src/entities/project';
import { callbackLogger } from '../../src/config/logger';

// Mock axios and other dependencies
jest.mock('axios');
jest.mock('../../src/services/videoService');
jest.mock('../../src/entities/project');
jest.mock('../../src/config/logger');

jest.mock('../../src/config/minio', () => ({
    minioClient: {
        presignedUrl: jest.fn().mockResolvedValue('https://mocked-minio-url.com'),
    },
    bucketName: 'mocked-bucket',
}));

describe('Callback Service', () => {
    const mockVideo = { id: 'video1', projectId: 'project1', isDeleted: false };
    const mockCallback = { notificationUrl: 'http://example.com/notify', thumbUrl: 'http://example.com/thumb' };
    const mockProject = { id: 'project1', callback: mockCallback };

    beforeEach(() => {
        jest.useFakeTimers();
    });

    afterEach(() => {
        jest.runAllTimers();
        jest.clearAllTimers();
        jest.clearAllMocks();
        jest.useRealTimers();
    });

    describe('sendNotificationCallback', () => {
        it('should send a notification callback if video exists and is not deleted', async () => {
            (getVideo as jest.Mock).mockResolvedValue(mockVideo);
            (Project.findOne as jest.Mock).mockResolvedValue(mockProject);
            (prepareVideoData as jest.Mock).mockResolvedValue({ id: 'video1', status: 'processed' });
            (axios.post as jest.Mock).mockResolvedValue({ status: 200, data: {} });

            await sendNotificationCallback('video1');

            expect(axios.post).toHaveBeenCalledWith(mockCallback.notificationUrl, { video: { id: 'video1', status: 'processed' } }, { timeout: 5000 });
            expect(callbackLogger.info).toHaveBeenCalled();
        });

        it('should log an error if no callback is found for the project', async () => {
            (getVideo as jest.Mock).mockResolvedValue(mockVideo);
            (Project.findOne as jest.Mock).mockResolvedValue(null);

            await sendNotificationCallback('video1');

            expect(callbackLogger.error).toHaveBeenCalledWith(`No callback found for project ${mockVideo.projectId}`);
        });

        it('should not send a callback if the video is deleted', async () => {
            (getVideo as jest.Mock).mockResolvedValue({ ...mockVideo, isDeleted: true });

            await sendNotificationCallback('video1');

            expect(axios.post).not.toHaveBeenCalled();
        });

        it('should log an error if axios request fails', async () => {
            (getVideo as jest.Mock).mockResolvedValue(mockVideo);
            (Project.findOne as jest.Mock).mockResolvedValue(mockProject);
            (prepareVideoData as jest.Mock).mockResolvedValue({ id: 'video1', status: 'processed' });
            (axios.post as jest.Mock).mockRejectedValue(new Error('Network error'));

            await sendNotificationCallback('video1');

            expect(callbackLogger.error).toHaveBeenCalledWith(expect.stringContaining('Failed to send callback to http://example.com/notify'));
        });
    });

    describe('sendDeleteNotificationCallback', () => {
        it('should send a delete notification callback if video exists', async () => {
            (getVideo as jest.Mock).mockResolvedValue(mockVideo);
            (Project.findOne as jest.Mock).mockResolvedValue(mockProject);
            (axios.post as jest.Mock).mockResolvedValue({ status: 200, data: {} });

            await sendDeleteNotificationCallback('video1');

            expect(axios.post).toHaveBeenCalledWith(mockCallback.notificationUrl, { video: { id: 'video1', deleted: true } }, { timeout: 5000 });
            expect(callbackLogger.info).toHaveBeenCalled();
        });
    });

    describe('sendThumbnailCallback', () => {
        it('should send a thumbnail callback with signed URLs for thumbnails', async () => {
            (getVideo as jest.Mock).mockResolvedValue(mockVideo);
            (Project.findOne as jest.Mock).mockResolvedValue(mockProject);
            (prepareVideoData as jest.Mock).mockResolvedValue({ id: 'video1', status: 'processed' });
            (getSignedThumbnails as jest.Mock).mockResolvedValue(['thumb1.jpg', 'thumb2.jpg']);
            (axios.post as jest.Mock).mockResolvedValue({ status: 200, data: {} });

            await sendThumbnailCallback('video1');

            expect(axios.post).toHaveBeenCalledWith(mockCallback.thumbUrl, { video: { id: 'video1', status: 'processed' }, thumbs: ['thumb1.jpg', 'thumb2.jpg'] }, { timeout: 5000 });
            expect(callbackLogger.info).toHaveBeenCalled();
        });

        it('should log an error if no callback is found for the project', async () => {
            (getVideo as jest.Mock).mockResolvedValue(mockVideo);
            (Project.findOne as jest.Mock).mockResolvedValue(null);

            await sendThumbnailCallback('video1');

            expect(callbackLogger.error).toHaveBeenCalledWith(`No callback found for project ${mockVideo.projectId}`);
        });
    });
});
