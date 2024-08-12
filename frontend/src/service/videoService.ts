import privateAxios from '../api/axiosPrivate';
import axiosPrivate from "../api/axiosPrivate";

interface DownloadVideoRequest {
    downloadLink: string;
}

export const getVideos = () => {
    return privateAxios.get('/v1/personal/videos');
};

export const getDownloadVideoLink = async (videoId: string): Promise<DownloadVideoRequest> => {
    const response = await axiosPrivate.get(`/v1/personal/videos/${videoId}/download`);
    return response.data.payload.data;
};
