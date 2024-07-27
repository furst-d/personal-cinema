import React from 'react';
import { Box, Typography } from "@mui/material";
import HdIcon from '@mui/icons-material/Hd';
import { useTheme } from 'styled-components';
import { Theme } from '../providers/ThemeProvider';
import {
    VideoLink,
    Duration,
    HdBadge,
    Overlay,
    PlayIcon,
    PreviewContainer,
    Size,
    Thumbnail
} from "../../styles/video/VideoPreview";

const formatSize = (size: number) => {
    if (size < 1024) return `${size} B`;
    if (size < 1024 * 1024) return `${(size / 1024).toFixed(2)} KB`;
    if (size < 1024 * 1024 * 1024) return `${(size / (1024 * 1024)).toFixed(2)} MB`;
    return `${(size / (1024 * 1024 * 1024)).toFixed(2)} GB`;
};

const formatDuration = (duration: number) => {
    const seconds = Math.floor(duration % 60);
    const minutes = Math.floor((duration / 60) % 60);
    const hours = Math.floor(duration / 3600);
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
};

const VideoPreview = ({ video }: { video: any }) => {
    const theme = useTheme() as Theme;
    const isHd = video.originalWidth >= 1280 && video.originalHeight >= 720;
    const duration = formatDuration(video.length);
    const size = formatSize(video.size);

    return (
        <VideoLink to="/">
            <PreviewContainer theme={theme}>
                <Thumbnail src={`data:image/jpeg;base64,${video.thumbnail}`} />
                <Overlay className="video-overlay">
                    <PlayIcon className="play-icon" />
                </Overlay>
                <Size variant="body2">{size}</Size>
                <Duration variant="body2">{duration}</Duration>
                {isHd && (
                    <HdBadge>
                        <HdIcon />
                    </HdBadge>
                )}
            </PreviewContainer>
            <Typography variant="body1" className="video-title" sx={{ transition: 'color 0.3s ease' }}>
                {video.name}
            </Typography>
        </VideoLink>
    );
};

export default VideoPreview;
