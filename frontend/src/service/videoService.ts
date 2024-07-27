import privateAxios from '../api/axiosPrivate';

export const getVideos = () => {
    return privateAxios.get('/v1/personal/videos');
};
