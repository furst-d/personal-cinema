import React from "react";
import {Typography} from "@mui/material";
import {formatDuration, formatSize} from "../../utils/formatter";
import {VideoDetailProps} from "./VideoDetail";
import {DetailsGridStyle, DetailsItemStyle, VideoDetailsStyle} from "../../styles/video/VideoDetail";

const VideoDetailInfo: React.FC<VideoDetailProps> = ({ video }) => {
    return (
        <VideoDetailsStyle>
            <DetailsGridStyle>
                <DetailsItemStyle>
                    <Typography variant="subtitle1">Délka</Typography>
                    <Typography variant="body2">{formatDuration(video.length)}</Typography>
                </DetailsItemStyle>
                <DetailsItemStyle>
                    <Typography variant="subtitle1">Velikost</Typography>
                    <Typography variant="body2">{formatSize(video.size)}</Typography>
                </DetailsItemStyle>
                <DetailsItemStyle>
                    <Typography variant="subtitle1">Rozlišení</Typography>
                    <Typography variant="body2">{video.originalWidth}x{video.originalHeight}</Typography>
                </DetailsItemStyle>
                <DetailsItemStyle>
                    <Typography variant="subtitle1">Formát</Typography>
                    <Typography variant="body2">{video.extension}</Typography>
                </DetailsItemStyle>
                <DetailsItemStyle>
                    <Typography variant="subtitle1">Kodek</Typography>
                    <Typography variant="body2">{video.codec}</Typography>
                </DetailsItemStyle>
                <DetailsItemStyle>
                    <Typography variant="subtitle1">Vytvořeno</Typography>
                    <Typography variant="body2">{new Date(video.createdAt).toLocaleString()}</Typography>
                </DetailsItemStyle>
            </DetailsGridStyle>
        </VideoDetailsStyle>
    );
}

export default VideoDetailInfo;