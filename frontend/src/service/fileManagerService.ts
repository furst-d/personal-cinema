import axiosPrivate from "../api/axiosPrivate";

interface Folder {
    id: string;
    name: string;
    updatedAt: string;
}

interface FolderPayload {
    data: Folder[];
    count: number;
    totalCount?: number;
}

interface Video {
    id: string;
    name: string;
    hash: string;
    thumbnailUrl?: string;
    path?: string;
    createdAt: string;
}

interface VideoPayload {
    data: Video[];
    count: number;
    totalCount?: number;
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

export const fetchFolders = async (limit: number, offset: number, sortBy: string, currentFolderId: string | null): Promise<FolderPayload> => {
    const response = await axiosPrivate.get('/v1/personal/folders', {
        params: {
            limit: limit,
            offset: offset,
            sortBy: sortBy,
            parentId: currentFolderId || 0
        }
    });
    return response.data.payload;
};

export const fetchSharedFolders = async (limit: number, offset: number, sortBy: string): Promise<FolderPayload> => {
    const response = await axiosPrivate.get('/v1/personal/folders/share', {
        params: {
            limit: limit,
            offset: offset,
            sortBy: sortBy,
        }
    });
    return response.data.payload;
};

export const fetchVideos = async (limit: number, offset: number, sortBy: string, currentFolderId: string | null): Promise<VideoPayload> => {
    const response = await axiosPrivate.get('/v1/personal/videos', {
        params: {
            limit: limit,
            offset: offset,
            sortBy: sortBy,
            folderId: currentFolderId || 0
        }
    });
    return response.data.payload;
};

export const fetchSharedVideos = async (limit: number, offset: number, sortBy: string): Promise<VideoPayload> => {
    const response = await axiosPrivate.get('/v1/personal/videos/share', {
        params: {
            limit: limit,
            offset: offset,
            sortBy: sortBy,
        }
    });
    return response.data.payload;
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

