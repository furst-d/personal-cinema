import React from 'react';
import { Box, Typography } from "@mui/material";
import { useTheme } from "styled-components";

interface VideoUploadProcessProps {
    videos: { name: string; file: File }[];
    progress: number[];
    speed: number;
    totalRemainingTime: number;
    completedUploads: Set<number>;
}

const VideoUploadProcess: React.FC<VideoUploadProcessProps> = ({ videos, progress, speed, totalRemainingTime, completedUploads }) => {
    const theme = useTheme();

    return (
        <Box sx={{ marginTop: 2, marginBottom: 4 }}>
            {videos.map((video, index) => (
                <Box key={index} sx={{ marginBottom: 2, position: 'relative' }}>
                    <Typography variant="body1" sx={{ position: 'absolute', left: 10, zIndex: 1, color: '#fff', display: 'flex', alignItems: 'center', height: '100%' }}>{video.name}</Typography>
                    <Box sx={{ height: 40, borderRadius: 4, border: `2px solid ${completedUploads.has(index) ? 'green' : theme.primary}`, overflow: 'hidden' }}>
                        <Box sx={{
                            height: '100%',
                            width: `${progress[index] || 0}%`,
                            backgroundColor: completedUploads.has(index) ? 'green' : theme.primary,
                            transition: 'width 0.5s ease-in-out',
                            display: 'flex',
                            alignItems: 'center',
                        }}>
                        </Box>
                    </Box>
                </Box>
            ))}
            <Typography variant="body2">Rychlost: {isFinite(speed) ? `${Math.round(speed / 1024)} kB/s` : 'Výpočet...'}</Typography>
            <Typography variant="body2">Zbývající čas: {isFinite(totalRemainingTime) ? `${Math.round(totalRemainingTime)} s` : 'Výpočet...'}</Typography>
        </Box>
    );
};

export default VideoUploadProcess;
