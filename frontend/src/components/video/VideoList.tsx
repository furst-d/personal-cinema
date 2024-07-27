import React, { useEffect, useState } from "react";
import { getVideos } from "../../service/videoService";
import { Typography, CircularProgress, Container, List, ListItem, ListItemText, ListItemAvatar, Avatar } from "@mui/material";

const VideoList: React.FC = () => {
    const [videos, setVideos] = useState<any[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        getVideos()
            .then(response => {
                setVideos(response.data.payload.data);
            })
            .catch(error => {
                setError('Chyba při načítání videí');
                console.error('Error loading videos:', error);
            })
            .finally(() => {
                setLoading(false);
            });
    }, []);

    if (loading) {
        return <CircularProgress />;
    }

    if (error) {
        return <Typography color="error">{error}</Typography>;
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
