import React, { useEffect, useState } from "react";
import { getVideos } from "../../service/videoService";
import { Typography, Container, Grid } from "@mui/material";
import Loading from "../loading/Loading";
import VideoPreview from "./VideoPreview";

const VideoList: React.FC = () => {
    const [videos, setVideos] = useState<any[]>([]);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        getVideos()
            .then(response => {
                setVideos(response.data.payload.data);
            })
            .catch(error => {
                console.error('Error loading videos:', error);
            })
            .finally(() => {
                setLoading(false);
            });
    }, []);

    if (loading) {
        return <Loading />;
    }

    return (
        <Container>
            <Typography variant="h4" gutterBottom>
                Vaše videa
            </Typography>
            <Grid container spacing={4}>
                {videos.map((video) => (
                    <Grid item xs={12} sm={6} md={4} lg={3} key={video.id}>
                        <VideoPreview video={video} />
                    </Grid>
                ))}
            </Grid>
        </Container>
    );
};

export default VideoList;
