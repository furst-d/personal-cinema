import React from "react";
import { Grid, Box, Typography } from "@mui/material";
import VideoPreview from "./VideoPreview";
import FolderPreview from "./FolderPreview";
import {styled, useTheme} from "styled-components";
import FolderOpenIcon from "@mui/icons-material/FolderOpen";
import {FileManagerEmptyFolderStyle} from "../../styles/file/FileManager";

interface MediaPanelProps {
    videos: any[];
    folders: any[];
    onFolderClick: (folderId: number, shared: boolean) => void;
    shared: boolean;
}

const MediaPanelContainer = styled(Box)`
    background-color: ${({ theme }) => theme.background};
    color: ${({ theme }) => theme.textLight};
`;

const MediaPanel: React.FC<MediaPanelProps> = ({ videos, folders, onFolderClick, shared }) => {
    const theme = useTheme();
    const isEmpty = videos.length === 0 && folders.length === 0;

    return (
        <MediaPanelContainer>
            {isEmpty ? (
                    <FileManagerEmptyFolderStyle>
                        <FolderOpenIcon sx={{ fontSize: 90, color: theme.textLight }} />
                        <Typography variant="body1" sx={{ color: theme.textLight }}>
                            Složka je prázdná.
                        </Typography>
                    </FileManagerEmptyFolderStyle>
            ) : (
                <>
                    <Grid container spacing={4}>
                        {videos.map((video) => (
                            <Grid item xs={12} sm={6} md={4} lg={3} key={video.id}>
                                <VideoPreview video={video} />
                            </Grid>
                        ))}
                    </Grid>
                    <Box mt={6} />
                    <Grid container spacing={2}>
                        {folders.map((folder) => (
                            <Grid item xs={6} sm={4} md={3} lg={2} key={folder.id}>
                                <FolderPreview folder={folder} onClick={() => onFolderClick(folder, shared)} />
                            </Grid>
                        ))}
                    </Grid>
                </>
            )}
        </MediaPanelContainer>
    );
};

export default MediaPanel;
