import React, { useState, MouseEvent } from "react";
import {
    Grid,
    Typography,
    IconButton,
    Box,
    ListItemText,
    ListItemIcon,
    Menu,
    MenuItem
} from "@mui/material";
import MoreVertIcon from '@mui/icons-material/MoreVert';
import FolderIcon from '@mui/icons-material/Folder';
import VideoFileIcon from '@mui/icons-material/VideoFile';
import ArrowBackIcon from '@mui/icons-material/ArrowBack';
import FolderOpenIcon from '@mui/icons-material/FolderOpen';
import UploadFileIcon from '@mui/icons-material/UploadFile';
import CreateNewFolderIcon from '@mui/icons-material/CreateNewFolder';
import { useTheme } from "styled-components";
import {
    FileManagerContainerStyle,
    FileManagerEmptyFolderStyle,
    FileManagerListItemStyle, FileManagerSeparator
} from "../../styles/file/FileManager";
import { formatDate } from "../../utils/formatter";

interface FileExplorerProps {
    folders: any[];
    videos: any[];
    currentFolderId: string | null;
    onFolderClick: (folderId: string) => void;
    onBackClick: () => void;
    onVideoDoubleClick: (hash: string) => void;
    onContextMenuOpen: (event: React.MouseEvent<HTMLElement>, item: any) => void;
    onContextMenuClose: () => void;
    contextMenuAnchor: HTMLElement | null;
}

const FileExplorer: React.FC<FileExplorerProps> = ({
   folders,
   videos,
   currentFolderId,
   onFolderClick,
   onBackClick,
   onVideoDoubleClick,
   onContextMenuOpen,
   onContextMenuClose,
   contextMenuAnchor
}) => {
    const theme = useTheme();
    const [contextMenuPosition, setContextMenuPosition] = useState<{ mouseX: number; mouseY: number } | null>(null);

    const handleContextMenu = (event: MouseEvent) => {
        event.preventDefault();
        setContextMenuPosition({
            mouseX: event.clientX,
            mouseY: event.clientY,
        });
    };

    const handleCloseContextMenu = () => {
        setContextMenuPosition(null);
    };

    const handleUploadClick = () => {
        console.log("Nahrát video do složky:", currentFolderId);
        handleCloseContextMenu();
    };

    const handleCreateFolderClick = () => {
        console.log("Vytvořit složku ve složce:", currentFolderId);
        handleCloseContextMenu();
    };

    return (
        <FileManagerContainerStyle theme={theme} onContextMenu={handleContextMenu}>
            {currentFolderId && (
                <IconButton onClick={onBackClick} sx={{ marginBottom: '10px', color: theme.text_light }}>
                    <ArrowBackIcon />
                </IconButton>
            )}
            <Grid container>
                {folders.length === 0 && videos.length === 0 ? (
                    <FileManagerEmptyFolderStyle>
                        <FolderOpenIcon sx={{ fontSize: 60, color: theme.text_light }} />
                        <Typography variant="body1" sx={{ color: theme.text_light }}>
                            Složka je prázdná.
                        </Typography>
                    </FileManagerEmptyFolderStyle>
                ) : (
                    <>
                        {folders.map((folder, index) => (
                            <React.Fragment key={folder.id}>
                                <Grid item xs={12} onDoubleClick={() => onFolderClick(folder.id)}>
                                    <FileManagerListItemStyle theme={theme}>
                                        <ListItemIcon>
                                            <FolderIcon sx={{ color: theme.text_light }} />
                                        </ListItemIcon>
                                        <Box sx={{ display: 'flex', flexDirection: 'column', flexGrow: 1, marginRight: '5px' }}>
                                            <Box sx={{ display: 'flex', flexDirection: { xs: 'column', sm: 'row' }, justifyContent: 'space-between', alignItems: { xs: 'flex-start', sm: 'center' } }}>
                                                <ListItemText primary={folder.name} />
                                                <Typography variant="body2" className="date">{formatDate(folder.updatedAt)}</Typography>
                                            </Box>
                                        </Box>
                                        <IconButton onClick={(e) => onContextMenuOpen(e, folder)} sx={{ color: theme.text_light }}>
                                            <MoreVertIcon />
                                        </IconButton>
                                    </FileManagerListItemStyle>
                                </Grid>
                                {index < folders.length - 1 && <FileManagerSeparator theme={theme} />}
                            </React.Fragment>
                        ))}
                        {folders.length > 0 && videos.length > 0 && <FileManagerSeparator theme={theme} />}
                        {videos.map((video, index) => (
                            <React.Fragment key={video.id}>
                                <Grid item xs={12} onDoubleClick={() => onVideoDoubleClick(video.hash)}>
                                    <FileManagerListItemStyle theme={theme}>
                                        <ListItemIcon>
                                            <VideoFileIcon sx={{ color: theme.text_light }} />
                                        </ListItemIcon>
                                        <Box sx={{ display: 'flex', flexDirection: 'column', flexGrow: 1, marginRight: '5px' }}>
                                            <Box sx={{ display: 'flex', flexDirection: { xs: 'column', sm: 'row' }, justifyContent: 'space-between', alignItems: { xs: 'flex-start', sm: 'center' } }}>
                                                <ListItemText primary={video.name} />
                                                <Typography variant="body2" className="date">{formatDate(video.createdAt)}</Typography>
                                            </Box>
                                        </Box>
                                        <IconButton onClick={(e) => onContextMenuOpen(e, video)} sx={{ color: theme.text_light }}>
                                            <MoreVertIcon />
                                        </IconButton>
                                    </FileManagerListItemStyle>
                                </Grid>
                                {index < videos.length - 1 && <FileManagerSeparator theme={theme} />}
                            </React.Fragment>
                        ))}
                    </>
                )}
            </Grid>
            <Menu
                anchorReference="anchorPosition"
                anchorPosition={
                    contextMenuPosition !== null
                        ? { top: contextMenuPosition.mouseY, left: contextMenuPosition.mouseX }
                        : undefined
                }
                keepMounted
                open={contextMenuPosition !== null}
                onClose={handleCloseContextMenu}
            >
                <MenuItem onClick={handleUploadClick}>
                    <ListItemIcon>
                        <UploadFileIcon sx={{ color: theme.text_light }} fontSize="small" />
                    </ListItemIcon>
                    <Typography variant="inherit">Nahrát video</Typography>
                </MenuItem>
                <MenuItem onClick={handleCreateFolderClick}>
                    <ListItemIcon>
                        <CreateNewFolderIcon sx={{ color: theme.text_light }} fontSize="small" />
                    </ListItemIcon>
                    <Typography variant="inherit">Vytvořit složku</Typography>
                </MenuItem>
            </Menu>
            <Menu
                anchorEl={contextMenuAnchor}
                keepMounted
                open={Boolean(contextMenuAnchor)}
                onClose={onContextMenuClose}
            >
                <MenuItem onClick={onContextMenuClose}>Sdílet</MenuItem>
                <MenuItem onClick={onContextMenuClose}>Upravit</MenuItem>
                <MenuItem onClick={onContextMenuClose}>Smazat</MenuItem>
            </Menu>
        </FileManagerContainerStyle>
    );
};

export default FileExplorer;
