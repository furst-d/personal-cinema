import React from 'react';
import { FileManagerSeparator } from "../../styles/file/FileManager";
import VideoItem from "./VideoItem";
import {useTheme} from "styled-components";

interface VideoListProps {
    videos: any[];
    onVideoDoubleClick: (hash: string) => void;
    onContextMenuOpen: (event: React.MouseEvent<HTMLElement>, item: any) => void;
}

const VideoList: React.FC<VideoListProps> = ({ videos, onVideoDoubleClick, onContextMenuOpen }) => {
    const theme = useTheme();

    return (
        videos.map((video, index) => {
            const isProcessing = !video.thumbnailUrl || !video.path;
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
        })
    )
};

export default VideoList;
