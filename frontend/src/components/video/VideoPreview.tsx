import React from 'react';
import { Typography } from "@mui/material";
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
    Thumbnail,
    DefaultThumbnail,
    ProcessingText,
    NotAvailableIconStyled,
} from "../../styles/video/VideoPreview";
import {formatDuration, formatSize} from "../../utils/formatter";
import {Video} from "../../service/fileManagerService";

const VideoPreview = ({ video }: { video: Video }) => {
    const theme = useTheme() as Theme;
    const isHd = video.originalWidth >= 1280 && video.originalHeight >= 720;
    const duration = video.length !== null ? formatDuration(video.length) : null;
    const size = video.size !== null ? formatSize(video.size) : null;

    const isProcessing = !video.thumbnailUrl || video.conversions.length === 0;

    return (
        <VideoLink to={`/videos/${video.hash}`} theme={theme} disabled={isProcessing}>
            <PreviewContainer>
                {video.thumbnailUrl ? (
                    <Thumbnail src={video.thumbnailUrl} />
                ) : (
                    <DefaultThumbnail>
                        <NotAvailableIconStyled />
                    </DefaultThumbnail>
                )}
                {!isProcessing && (
                    <Overlay className="video-overlay">
                        <PlayIcon className="play-icon" />
                    </Overlay>
                )}
                {size && <Size variant="body2">{size}</Size>}
                {duration && <Duration variant="body2">{duration}</Duration>}
                {isHd && (
                    <HdBadge>
                        <HdIcon />
                    </HdBadge>
                )}
                {isProcessing && (
                    <ProcessingText variant="body2">Zpracovává se</ProcessingText>
                )}
            </PreviewContainer>
            <Typography
                variant="body1"
                className="video-title"
                align="center"
                sx={{
                    transition: 'color 0.3s ease',
                    '@media (min-width: 600px)': { textAlign: 'left' },
                }}
            >
                {video.name}
            </Typography>
        </VideoLink>
    );
};

export default VideoPreview;
