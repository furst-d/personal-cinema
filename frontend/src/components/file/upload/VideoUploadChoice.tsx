import React from 'react';
import { Box, Grid, TextField, Typography, Button } from "@mui/material";

interface VideoUploadChoiceProps {
    videos: { name: string; file: File }[];
    onNameChange: (index: number, newName: string) => void;
    onConfirmUpload: () => void;
}

const VideoUploadChoice: React.FC<VideoUploadChoiceProps> = ({ videos, onNameChange, onConfirmUpload }) => (
    <Box sx={{ marginTop: 2, marginBottom: 4 }}>
        <Typography variant="h6" sx={{ marginTop: 2 }}>Nahraná videa:</Typography>
        {videos.map((video, index) => (
            <Grid container spacing={2} alignItems="center" key={index} sx={{ marginTop: 1 }}>
                <Grid item xs={8}>
                    <TextField
                        fullWidth
                        label="Název videa"
                        value={video.name}
                        onChange={(e) => onNameChange(index, e.target.value)}
                    />
                </Grid>
            </Grid>
        ))}
        <Button
            variant="contained"
            color="primary"
            onClick={onConfirmUpload}
            sx={{ marginTop: 2 }}
        >
            Potvrdit nahrání
        </Button>
    </Box>
);

export default VideoUploadChoice;
