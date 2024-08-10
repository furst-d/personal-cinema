import axiosPrivate from "../api/axiosPrivate";

interface Folder {
    id: string;
    name: string;
    updatedAt: string;
}

interface Video {
    id: string;
    name: string;
    hash: string;
    thumbnailUrl?: string;
    path?: string;
    createdAt: string;
}

interface CreateFolderRequest {
    name: string;
    parentId?: string;
}

interface UpdateRequest {
    name: string;
    parentId?: string;
    folderId?: string;
}

export const fetchFolders = async (currentFolderId: string | null): Promise<Folder[]> => {
    const response = await axiosPrivate.get('/v1/personal/folders', {
        params: {
            limit: 1000,
            sortBy: 'name',
            parentId: currentFolderId || 0
        }
    });
    return response.data.payload.data;
};

export const fetchVideos = async (currentFolderId: string | null): Promise<Video[]> => {
    const response = await axiosPrivate.get('/v1/personal/videos', {
        params: {
            limit: 1000,
            sortBy: 'name',
            folderId: currentFolderId || 0
        }
    });
    return response.data.payload.data;
};

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

export const createFolder = async (requestData: CreateFolderRequest): Promise<Folder> => {
    const response = await axiosPrivate.post('/v1/personal/folders', requestData);
    return response.data.payload.data;
};

export const updateFolder = async (folderId: string, requestData: UpdateRequest): Promise<void> => {
    await axiosPrivate.put(`/v1/personal/folders/${folderId}`, requestData);
};

export const updateVideo = async (videoId: string, requestData: UpdateRequest): Promise<void> => {
    await axiosPrivate.put(`/v1/personal/videos/${videoId}`, requestData);
};

export const deleteFolder = async (folderId: string): Promise<void> => {
    await axiosPrivate.delete(`/v1/personal/folders/${folderId}`);
};

export const deleteVideo = async (videoId: string): Promise<void> => {
    await axiosPrivate.delete(`/v1/personal/videos/${videoId}`);
};

export const moveItem = async (item: any, targetFolderId: string | null): Promise<void> => {
    if (item.type === 'folder') {
        await axiosPrivate.put(`/v1/personal/folders/${item.id}`, {
            name: item.name,
            parentId: targetFolderId
        });
    } else {
        await axiosPrivate.put(`/v1/personal/videos/${item.id}`, {
            name: item.name,
            folderId: targetFolderId
        });
    }
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
