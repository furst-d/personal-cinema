import React from "react";
import VideoPlayer from "../player/VideoPlayer";
import {useTheme} from "styled-components";
import {Container, Grid, Typography} from "@mui/material";

export interface ShareVideoPublicProps {
    video: any;
}

const ShareVideoPublic: React.FC<ShareVideoPublicProps> = ({ video }) => {
    const theme = useTheme();

    return (
        <Container>
            <Grid container sx={{ marginTop: '20px' }}>
                <Grid item xs={12} md={12}>
                    <VideoPlayer src={video.videoUrl} />
                    <Typography variant="h5" gutterBottom sx={{ marginTop: '16px', marginBottom: '32px', color: theme.textLight }}>
                        {video.name}
                    </Typography>
                </Grid>
            </Grid>
        </Container>
    );
}

export default ShareVideoPublic;
