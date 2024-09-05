import { getVideoSizeLimit } from '../../src/services/settingsService';
import Settings from '../../src/entities/settings';

jest.mock('../../src/entities/settings');

describe('getVideoSizeLimit', () => {
    beforeEach(() => {
        jest.clearAllMocks();
    });

    it('should return the video size limit in bytes when the setting exists', async () => {
        // Mock the database call to find the setting
        (Settings.findOne as jest.Mock).mockResolvedValue({ key: 'video_size_limit', value: '100MB' });

        const sizeLimit = await getVideoSizeLimit();

        expect(Settings.findOne).toHaveBeenCalledWith({ where: { key: 'video_size_limit' } });
        // Here we directly use convertSizeToBytes, so no mock needed
        expect(sizeLimit).toBe(104857600); // 100MB in bytes
    });

    it('should throw an error if the video size limit setting is not found', async () => {
        // Mock the database call to return null (no setting found)
        (Settings.findOne as jest.Mock).mockResolvedValue(null);

        await expect(getVideoSizeLimit()).rejects.toThrow('Video size limit setting not found');
        expect(Settings.findOne).toHaveBeenCalledWith({ where: { key: 'video_size_limit' } });
    });

    it('should handle invalid size format', async () => {
        // Mock the database call to return an invalid size value
        (Settings.findOne as jest.Mock).mockResolvedValue({ key: 'video_size_limit', value: 'invalid' });

        await expect(getVideoSizeLimit()).rejects.toThrow('Invalid size format');
        expect(Settings.findOne).toHaveBeenCalledWith({ where: { key: 'video_size_limit' } });
    });
});
