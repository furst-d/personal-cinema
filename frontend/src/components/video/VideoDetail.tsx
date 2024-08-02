import React from "react";
import { Container, Grid, Typography, Box } from "@mui/material";
import VideoPlayer from "../player/VideoPlayer";
import VideoRecommendations from "./VideoRecommendations";
import VideoDetailInfo from "./VideoDetailInfo";
import {useTheme} from "styled-components";

export interface VideoDetailProps {
    video: any;
}

const VideoDetail: React.FC<VideoDetailProps> = ({ video }) => {
    const theme = useTheme();

    return (
        <Container>
            <Grid container spacing={4} sx={{ justifyContent: { lg: 'space-between' } }}>
                <Grid item xs={12} md={8}>
                    <VideoPlayer src={video.videoUrl} />
                    <Typography variant="h5" gutterBottom sx={{ marginTop: '16px', marginBottom: '32px', color: theme.text_light }}>
                        {video.name}
                    </Typography>
                    <VideoDetailInfo video={video} />
                </Grid>
                <Grid item xs={12} md={3}>
                    <VideoRecommendations videoId={video.id} />
                </Grid>
            </Grid>
        </Container>
    );
};

export default VideoDetail;
