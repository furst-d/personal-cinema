export const removeFileExtension = (fileName: string): string => {
    return fileName.replace(/\.[^/.]+$/, "");
};