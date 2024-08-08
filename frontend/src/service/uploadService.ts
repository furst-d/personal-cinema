import axiosPrivate from "../api/axiosPrivate";
import axiosCdn from "../api/axiosCdn";

export const uploadVideoMetadata = async (name: string, folderId: string | null, size: number) => {
    const response = await axiosPrivate.post('/v1/personal/videos/upload', { name, size, folderId });
    return response.data.payload.data;
};

export const uploadVideoToCdn = async (formData: FormData, onUploadProgress: (progressEvent: ProgressEvent) => void) => {
    return await axiosCdn.post('/upload', formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
        onUploadProgress
    });
};
