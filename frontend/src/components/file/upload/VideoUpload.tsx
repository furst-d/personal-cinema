import React, { useState, useRef, useEffect } from 'react';
import VideoUploadChoice from './VideoUploadChoice';
import VideoUploadProcess from './VideoUploadProcess';
import { removeFileExtension } from "../../../utils/namer";
import { toast } from 'react-toastify';

interface VideoUploadProps {
    handleSingleUploadCompleted: (video: { name: string; file: File }) => void;
    handleAllUploadsCompleted: () => void;
}

const VideoUpload: React.FC<VideoUploadProps> = ({ handleSingleUploadCompleted, handleAllUploadsCompleted }) => {
    const [videos, setVideos] = useState<{ name: string; file: File }[]>([]);
    const [isUploading, setIsUploading] = useState<boolean>(false);
    const [showUploadChoice, setShowUploadChoice] = useState<boolean>(false);
    const [progress, setProgress] = useState<number[]>([]);
    const [speed, setSpeed] = useState<number>(0);
    const [totalRemainingTime, setTotalRemainingTime] = useState<number>(0);
    const [completedUploads, setCompletedUploads] = useState<Set<number>>(new Set());
    const uploadedSizeRef = useRef<number>(0);

    useEffect(() => {
        if (isUploading && completedUploads.size < videos.length) {
            const uploadCurrentFile = async () => {
                const currentIndex = completedUploads.size;
                await uploadFile(videos[currentIndex].file, currentIndex);
                handleUploadComplete(currentIndex);
            };
            uploadCurrentFile();
        } else if (isUploading && completedUploads.size >= videos.length) {
            handleAllUploadsComplete();
        }
    }, [isUploading, completedUploads.size]);

    const handleFileChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        if (event.target.files) {
            const newVideos = Array.from(event.target.files).map(file => ({
                name: removeFileExtension(file.name),
                file
            }));
            setVideos(prevVideos => [...prevVideos, ...newVideos]);
            setProgress(prevProgress => [...prevProgress, ...newVideos.map(() => 0)]);
            setShowUploadChoice(true);
        }
    };

    const handleNameChange = (index: number, newName: string) => {
        setVideos(prevVideos => {
            const updatedVideos = [...prevVideos];
            updatedVideos[index].name = removeFileExtension(newName);
            return updatedVideos;
        });
    };

    const handleConfirmUpload = () => {
        setIsUploading(true);
        setCompletedUploads(new Set());
        uploadedSizeRef.current = 0;
        console.log('Total size to upload:', videos.reduce((acc, video) => acc + video.file.size, 0));
    };

    const handleUploadComplete = (fileIndex: number) => {
        setCompletedUploads(prevCompleted => new Set(prevCompleted).add(fileIndex));
        toast.success(`${videos[fileIndex].name} byl úspěšně nahrán.`);
        handleSingleUploadCompleted(videos[fileIndex]);
    };

    const handleAllUploadsComplete = () => {
        setShowUploadChoice(false);
        setIsUploading(false);
        setVideos([]);
        setProgress([]);
        setSpeed(0);
        setTotalRemainingTime(0);
        setCompletedUploads(new Set());
        uploadedSizeRef.current = 0;
        handleAllUploadsCompleted();
    };

    const uploadFile = async (file: File, fileIndex: number) => {
        const fileSize = file.size;
        let fileUploadedSize = 0;
        const startTime = Date.now();

        return new Promise<void>((resolve) => {
            const updateProgress = () => {
                const elapsedTime = (Date.now() - startTime) / 1000; // in seconds
                const currentSpeed = uploadedSizeRef.current / elapsedTime; // bytes per second
                setSpeed(currentSpeed);

                const remainingBytes = videos.reduce((acc, video) => acc + video.file.size, 0) - uploadedSizeRef.current;
                setTotalRemainingTime(remainingBytes / currentSpeed); // in seconds

                console.log(`Uploaded: ${uploadedSizeRef.current} bytes`);
                console.log(`Current speed: ${currentSpeed} bytes/s`);

                if (fileUploadedSize < fileSize) {
                    fileUploadedSize += fileSize * 0.01; // simulate upload
                    uploadedSizeRef.current += fileSize * 0.01;
                    setProgress(prevProgress => {
                        const newProgress = [...prevProgress];
                        newProgress[fileIndex] = (fileUploadedSize / fileSize) * 100;
                        return newProgress;
                    });
                    setTimeout(updateProgress, 100);
                } else {
                    resolve();
                }
            };

            updateProgress();
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
                            completedUploads={completedUploads}
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
