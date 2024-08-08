import React, {useRef, useState} from 'react';
import VideoUploadChoice from './VideoUploadChoice';
import VideoUploadProcess from './VideoUploadProcess';
import { removeFileExtension } from "../../../utils/namer";
import { uploadVideoMetadata, uploadVideoToCdn } from "../../../service/uploadService";
import { toast } from 'react-toastify';

interface VideoUploadProps {
    currentFolderId: string | null;
    handleSingleUploadCompleted: () => void;
}

const VideoUpload: React.FC<VideoUploadProps> = ({ currentFolderId, handleSingleUploadCompleted }) => {
    const [videos, setUploadVideos] = useState<{ name: string; file: File }[]>([]);
    const [isUploading, setIsUploading] = useState<boolean>(false);
    const [showUploadChoice, setShowUploadChoice] = useState<boolean>(false);
    const [progress, setProgress] = useState<number[]>([]);
    const [speed, setSpeed] = useState<number>(0);
    const [totalRemainingTime, setTotalRemainingTime] = useState<number>(0);
    const inputRef = useRef<HTMLInputElement | null>(null);

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
        uploadFiles(videos).then(() => {
            handleUploadAllComplete();
        });
    };

    const handleUploadAllComplete = () => {
        setIsUploading(false);
        setShowUploadChoice(false);
        setUploadVideos([]);
        if (inputRef.current) {
            inputRef.current.value = '';
        }
        toast.success('Upload videí byl dokončen');
    };

    const uploadFiles = async (videos: { name: string; file: File }[]) => {
        const totalSize = videos.reduce((acc, video) => acc + video.file.size, 0);
        let uploadedSize = 0;

        for (let i = 0; i < videos.length; i++) {
            await uploadCurrentFile(videos[i].file, i, totalSize, uploadedSize, videos[i].name);
            uploadedSize += videos[i].file.size;
        }
    };

    const updateProgress = (fileIndex: number, progress: number) => {
        return new Promise<void>((resolve) => {
            setProgress(prevProgress => {
                const newProgress = [...prevProgress];
                newProgress[fileIndex] = progress;
                resolve();
                return newProgress;
            });
        });
    };

    const uploadCurrentFile = async (file: File, fileIndex: number, totalSize: number, uploadedSize: number, name: string) => {
        let hasError = false;
        let currentProgress = 0;

        try {
            const startTime = Date.now();
            const fileSize = file.size;
            const metadataResponse = await uploadVideoMetadata(name, currentFolderId, fileSize);

            const formData = new FormData();
            formData.append('video', file);
            formData.append('params', metadataResponse.params);
            formData.append('signature', metadataResponse.signature);
            formData.append('nonce', metadataResponse.nonce);
            formData.append('project_id', metadataResponse.project_id);

            await uploadVideoToCdn(formData, async (progressEvent) => {
                if (progressEvent.lengthComputable && progressEvent.total && !hasError) {
                    const currentSpeed = progressEvent.loaded / ((Date.now() - startTime) / 1000);
                    setSpeed(currentSpeed);

                    const remainingBytes = totalSize - (uploadedSize + progressEvent.loaded);
                    setTotalRemainingTime(remainingBytes / currentSpeed);

                    currentProgress = (progressEvent.loaded / progressEvent.total) * 100;
                    await updateProgress(fileIndex, currentProgress);
                }
            });
            setSpeed(0);
            currentProgress = 100;

            handleSingleUploadCompleted();
        } catch (error: any) {
            hasError = true;
            setSpeed(0);
            currentProgress = 0;

            if (error.response && error.response.status === 413) {
                const maxSize = error.response.data.payload.details.maxFileSize;
                toast.error(`Video ${name} je příliš velké, maximální velikost souboru je ${maxSize}`);
            } else {
                toast.error(`Při nahrávání videa ${name} došlo k chybě`);
            }
        } finally {
            await updateProgress(fileIndex, currentProgress);
        }
    };

    return (
        <>
            <input
                accept=".mp4, .mov, .avi, .mkv, .flv, .wmv, .webm, .mpeg"
                style={{ display: 'none' }}
                id="upload-video-choice"
                type="file"
                multiple
                onChange={handleFileChange}
                disabled={isUploading}
                ref={inputRef}
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
