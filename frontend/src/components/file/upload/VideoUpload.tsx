import React, { useState } from 'react';
import VideoUploadChoice from './VideoUploadChoice';
import VideoUploadProcess from './VideoUploadProcess';
import { removeFileExtension } from "../../../utils/namer";
import { uploadVideoMetadata, uploadVideoToCdn } from "../../../service/uploadService";
import { fetchVideos } from "../../../service/fileManagerService"; // Ensure this is imported
import { toast } from 'react-toastify';

interface VideoUploadProps {
    currentFolderId: string | null;
    handleAllUploadsComplete: () => void;
    setVideos: (videos: any[]) => void; // Added prop for setting videos
}

const VideoUpload: React.FC<VideoUploadProps> = ({ currentFolderId, handleAllUploadsComplete, setVideos }) => {
    const [videos, setUploadVideos] = useState<{ name: string; file: File }[]>([]);
    const [isUploading, setIsUploading] = useState<boolean>(false);
    const [showUploadChoice, setShowUploadChoice] = useState<boolean>(false);
    const [progress, setProgress] = useState<number[]>([]);
    const [speed, setSpeed] = useState<number>(0);
    const [totalRemainingTime, setTotalRemainingTime] = useState<number>(0);

    const handleFileChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        if (event.target.files) {
            const newVideos = Array.from(event.target.files).map(file => ({
                name: removeFileExtension(file.name),
                file
            }));
            setUploadVideos(prevVideos => [...prevVideos, ...newVideos]);
            setProgress(prevProgress => [...prevProgress, ...Array(newVideos.length).fill(0)]);
            setShowUploadChoice(true);
        }
    };

    const handleNameChange = (index: number, newName: string) => {
        setUploadVideos(prevVideos => {
            const updatedVideos = [...prevVideos];
            updatedVideos[index].name = removeFileExtension(newName);
            return updatedVideos;
        });
    };

    const handleConfirmUpload = () => {
        setIsUploading(true);
        uploadFiles(videos);
    };

    const handleSingleUploadComplete = () => {
        console.log('Single upload complete');
    }

    const handleUploadAllComplete = () => {
        setIsUploading(false);
        setShowUploadChoice(false);
        setUploadVideos([]);
        handleAllUploadsComplete();
    };

    const uploadFiles = async (videos: { name: string; file: File }[]) => {
        const totalSize = videos.reduce((acc, video) => acc + video.file.size, 0);
        let startTime = Date.now();
        let uploadedSize = 0;

        for (let i = 0; i < videos.length; i++) {
            await uploadCurrentFile(videos[i].file, i, totalSize, startTime, uploadedSize, videos[i].name);
            uploadedSize += videos[i].file.size;
        }
        handleUploadAllComplete();
    };

    const uploadCurrentFile = async (file: File, fileIndex: number, totalSize: number, startTime: number, uploadedSize: number, name: string) => {
        const fileSize = file.size;

        const metadataResponse = await uploadVideoMetadata(name, currentFolderId);

        const formData = new FormData();
        formData.append('video', file);
        formData.append('params', metadataResponse.params);
        formData.append('signature', metadataResponse.signature);
        formData.append('nonce', metadataResponse.nonce);
        formData.append('project_id', metadataResponse.project_id);

        await uploadVideoToCdn(formData, (progressEvent) => {
            console.log("Progress event: ", progressEvent);
            if (progressEvent.lengthComputable) {
                const elapsedTime = (Date.now() - startTime) / 1000; // in seconds
                const currentSpeed = uploadedSize / elapsedTime; // bytes per second
                setSpeed(currentSpeed);

                const remainingBytes = totalSize - uploadedSize;
                setTotalRemainingTime(remainingBytes / currentSpeed); // in seconds

                const fileUploadedSize = progressEvent.loaded;
                const currentProgress = (fileUploadedSize / fileSize) * 100;
                setProgress(prevProgress => {
                    const newProgress = [...prevProgress];
                    newProgress[fileIndex] = currentProgress;
                    return newProgress;
                });

                console.log(`File index: ${fileIndex}, progress: ${currentProgress}%`);
            }
        });

        // Ensure the progress is set to 100% when the upload completes
        setProgress(prevProgress => {
            const newProgress = [...prevProgress];
            newProgress[fileIndex] = 100;
            return newProgress;
        });
    };

    return (
        <>
            <input
                accept="video/*"
                style={{ display: 'none' }}
                id="upload-video-choice"
                type="file"
                multiple
                onChange={handleFileChange}
                disabled={isUploading}
            />
            {showUploadChoice && (
                <div>
                    {isUploading ? (
                        <VideoUploadProcess
                            videos={videos}
                            progress={progress}
                            speed={speed}
                            totalRemainingTime={totalRemainingTime}
                        />
                    ) : (
                        <VideoUploadChoice
                            videos={videos}
                            onNameChange={handleNameChange}
                            onConfirmUpload={handleConfirmUpload}
                            handleFileChange={handleFileChange}
                        />
                    )}
                </div>
            )}
        </>
    );
};

export default VideoUpload;
