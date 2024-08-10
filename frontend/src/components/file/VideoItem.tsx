import React from "react";
import { useDrag } from "react-dnd";
import { Box, Grid, IconButton, ListItemIcon, ListItemText, Typography, Tooltip } from "@mui/material";
import { FileManagerListItemStyle } from "../../styles/file/FileManager";
import VideoFileIcon from "@mui/icons-material/VideoFile";
import ShareIcon from "@mui/icons-material/Share";
import { formatDate } from "../../utils/formatter";
import MoreVertIcon from "@mui/icons-material/MoreVert";
import { ItemTypes } from "../../types/file";
import { useTheme } from "styled-components";

interface VideoItemProps {
    video: any;
    onVideoDoubleClick: (hash: string) => void;
    onContextMenuOpen: (event: React.MouseEvent<HTMLElement>, item: any) => void;
    isProcessing: boolean;
}

const VideoItem: React.FC<VideoItemProps> = ({
                                                 video,
                                                 onVideoDoubleClick,
                                                 onContextMenuOpen,
                                                 isProcessing
                                             }) => {
    const theme = useTheme();

    const [, drag] = useDrag({
        type: ItemTypes.VIDEO,
        item: { ...video, type: ItemTypes.VIDEO },
    });

    return (
        <Grid item xs={12} ref={drag} onDoubleClick={() => !isProcessing && onVideoDoubleClick(video.hash)}>
            <FileManagerListItemStyle theme={theme}>
                <ListItemIcon>
                    <VideoFileIcon sx={{ color: theme.textLight }} />
                </ListItemIcon>
                <Box sx={{ display: 'flex', flexDirection: 'column', flexGrow: 1, marginRight: '5px' }}>
                    <Box sx={{ display: 'flex', flexDirection: { xs: 'column', sm: 'row' }, justifyContent: 'space-between', alignItems: { xs: 'flex-start', sm: 'center' } }}>
                        <Box sx={{ display: 'flex', alignItems: 'center' }}>
                            <ListItemText primary={video.name} />
                            {isProcessing && (
                                <Typography variant="body2" sx={{ marginLeft: '10px', backgroundColor: theme.background, padding: '2px 6px', borderRadius: '4px', color: theme.textLight }}>
                                    Zpracovává se
                                </Typography>
                            )}
                        </Box>
                        <Box sx={{ display: 'flex', alignItems: 'center' }}>
                            {video.shared && (
                                <Tooltip title="Video je sdílené">
                                    <ShareIcon
                                        sx={{
                                            color: 'green',
                                            fontSize: '1.2rem',
                                            marginRight: '8px'
                                        }}
                                    />
                                </Tooltip>
                            )}
                            <Typography variant="body2" className="date">{formatDate(video.createdAt)}</Typography>
                        </Box>
                    </Box>
                </Box>
                <IconButton onClick={(e) => onContextMenuOpen(e, video)} sx={{ color: theme.textLight }}>
                    <MoreVertIcon />
                </IconButton>
            </FileManagerListItemStyle>
        </Grid>
    );
};

export default VideoItem;
