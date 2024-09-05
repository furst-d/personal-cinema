import { getVideo } from '../../src/services/videoService';
import Video from '../../src/entities/video';

jest.mock('../../src/entities/video');
jest.mock('../../src/config/minio'); // Keep this if necessary

describe('getVideo', () => {
    beforeEach(() => {
        jest.clearAllMocks();
    });

    it('should return the video if found', async () => {
        const mockVideo = { id: 'video-id', title: 'test-video.mp4' };
        (Video.findByPk as jest.Mock).mockResolvedValue(mockVideo);

        const video = await getVideo('video-id');
        expect(video).toEqual(mockVideo);
    });

    it('should throw an error if video not found', async () => {
        (Video.findByPk as jest.Mock).mockResolvedValue(null);

        await expect(getVideo('video-id')).rejects.toThrow('Video not found');
    });
});
