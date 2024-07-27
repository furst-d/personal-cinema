import React, { useEffect, useState } from "react";
import { getVideos } from "../../service/videoService";
import { Typography, CircularProgress, Container, List, ListItem, ListItemText, ListItemAvatar, Avatar, Box } from "@mui/material";
import Loading from "../loading/Loading";

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
            <List>
                {videos.map((video) => (
                    <ListItem key={video.id}>
                        <ListItemAvatar>
                            <Avatar variant="square" src={`data:image/jpeg;base64,${video.thumbnail}`} />
                        </ListItemAvatar>
                        <ListItemText
                            primary={video.name}
                            secondary={`Velikost: ${video.size} bajtů, Kodek: ${video.codec}, Rozlišení: ${video.originalWidth}x${video.originalHeight}`}
                        />
                    </ListItem>
                ))}
            </List>
        </Container>
    );
};

export default VideoList;
