import React from 'react';
import { FileManagerSeparator } from "../../styles/file/FileManager";
import VideoItem from "./VideoItem";
import { useTheme } from "styled-components";
import { Video } from "../../service/fileManagerService";

interface VideoListProps {
    videos: Video[];
    onVideoDoubleClick: (hash: string) => void;
    onContextMenuOpen: (event: React.MouseEvent<HTMLElement>, item: any) => void;
}

const VideoList: React.FC<VideoListProps> = ({ videos, onVideoDoubleClick, onContextMenuOpen }) => {
    const theme = useTheme();

    return (
        <React.Fragment>
            {videos.map((video, index) => {
                const isProcessing = !video.thumbnailUrl || video.conversions.length === 0;

                return (
                    <React.Fragment key={video.id}>
                        <VideoItem
                            video={video}
                            onVideoDoubleClick={onVideoDoubleClick}
                            onContextMenuOpen={onContextMenuOpen}
                            isProcessing={isProcessing}
                        />
                        {index < videos.length - 1 && <FileManagerSeparator theme={theme} />}
                    </React.Fragment>
                );
            })}
        </React.Fragment>
    );
};

export default VideoList;
