import { convertSizeToBytes } from '../utils/convertSizeToBytes';
import Settings from "../entities/settings";

export const getVideoSizeLimit = async (): Promise<number> => {
    const setting = await Settings.findOne({ where: { key: 'video_size_limit' } });

    if (!setting) {
        throw new Error('Video size limit setting not found');
    }

    return convertSizeToBytes(setting.value);
};
