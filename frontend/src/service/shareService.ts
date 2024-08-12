import axiosPrivate from "../api/axiosPrivate";

export const fetchVideoShare = async (videoId: string): Promise<{ id: string; email: string; createdAt: string }[]> => {
    const response = await axiosPrivate.get(`/v1/personal/videos/${videoId}/share`);
    return response.data.payload.data.map((share: any) => ({
        id: share.id,
        email: share.account.email,
        createdAt: share.createdAt,
    }));
};

export const fetchFolderShare = async (folderId: string): Promise<{ id: string; email: string; createdAt: string }[]> => {
    const response = await axiosPrivate.get(`/v1/personal/folders/${folderId}/share`);
    return response.data.payload.data.map((share: any) => ({
        id: share.id,
        email: share.account.email,
        createdAt: share.createdAt,
    }));
};

export const fetchPublicVideoShare = async (videoId: string): Promise<{ maxViews: number; shares: { hash: string; createdAt: string; expiredAt: string; viewCount: number }[] }> => {
    const response = await axiosPrivate.get(`/v1/personal/videos/${videoId}/share/public`);
    return response.data.payload.data;
};

export const deleteVideoShare = async (shareId: string): Promise<void> => {
    await axiosPrivate.delete(`/v1/personal/videos/share/${shareId}`);
};

export const deleteFolderShare = async (shareId: string): Promise<void> => {
    await axiosPrivate.delete(`/v1/personal/folders/share/${shareId}`);
};

export const shareVideo = async (videoId: string, email: string): Promise<void> => {
    await axiosPrivate.post('/v1/personal/videos/share', { videoId, email });
};

export const shareFolder = async (folderId: string, email: string): Promise<void> => {
    await axiosPrivate.post('/v1/personal/folders/share', { folderId, email });
};

export const generatePublicVideoShare = async (videoId: string): Promise<{ hash: string; createdAt: string; expiredAt: string; viewCount: number }> => {
    const response = await axiosPrivate.post('/v1/personal/videos/share/public', { videoId });
    return response.data.payload.data;
};