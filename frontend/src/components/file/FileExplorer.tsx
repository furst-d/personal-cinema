import React, { useState, MouseEvent } from "react";
import {
    Grid,
    Typography,
    IconButton,
    Box,
    ListItemText,
    ListItemIcon,
    Menu,
    MenuItem,
    Tooltip
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
    onEditFolder: (item: any) => void;
    onEditVideo: (item: any) => void;
    onDeleteFolder: (item: any) => void;
    onDeleteVideo: (item: any) => void;
    onCreateFolder: () => void;
    contextMenuAnchor: HTMLElement | null;
    selectedItem: any;
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
   onCreateFolder,
   onEditFolder,
   onEditVideo,
   onDeleteFolder,
   onDeleteVideo,
   contextMenuAnchor,
   selectedItem
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

    return (
        <FileManagerContainerStyle theme={theme} onContextMenu={handleContextMenu}>
            {currentFolderId && (
                <IconButton onClick={onBackClick} sx={{ marginBottom: '10px', color: theme.textLight }}>
                    <ArrowBackIcon />
                </IconButton>
            )}
            <Grid container>
                {folders.length === 0 && videos.length === 0 ? (
                    <FileManagerEmptyFolderStyle>
                        <FolderOpenIcon sx={{ fontSize: 60, color: theme.textLight }} />
                        <Typography variant="body1" sx={{ color: theme.textLight }}>
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
                                            <FolderIcon sx={{ color: theme.textLight }} />
                                        </ListItemIcon>
                                        <Box sx={{ display: 'flex', flexDirection: 'column', flexGrow: 1, marginRight: '5px' }}>
                                            <Box sx={{ display: 'flex', flexDirection: { xs: 'column', sm: 'row' }, justifyContent: 'space-between', alignItems: { xs: 'flex-start', sm: 'center' } }}>
                                                <ListItemText primary={folder.name} />
                                                <Typography variant="body2" className="date">{formatDate(folder.updatedAt)}</Typography>
                                            </Box>
                                        </Box>
                                        <IconButton onClick={(e) => onContextMenuOpen(e, folder)} sx={{ color: theme.textLight }}>
                                            <MoreVertIcon />
                                        </IconButton>
                                    </FileManagerListItemStyle>
                                </Grid>
                                {index < folders.length - 1 && <FileManagerSeparator theme={theme} />}
                            </React.Fragment>
                        ))}
                        {folders.length > 0 && videos.length > 0 && <FileManagerSeparator theme={theme} />}
                        {videos.map((video, index) => {
                            const isProcessing = !video.thumbnailUrl || !video.path;
                            return (
                                <React.Fragment key={video.id}>
                                    <Grid item xs={12} onDoubleClick={() => !isProcessing && onVideoDoubleClick(video.hash)}>
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
                                                    <Typography variant="body2" className="date">{formatDate(video.createdAt)}</Typography>
                                                </Box>
                                            </Box>
                                            <IconButton onClick={(e) => onContextMenuOpen(e, video)} sx={{ color: theme.textLight }}>
                                                <MoreVertIcon />
                                            </IconButton>
                                        </FileManagerListItemStyle>
                                    </Grid>
                                    {index < videos.length - 1 && <FileManagerSeparator theme={theme} />}
                                </React.Fragment>
                            );
                        })}
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
                <MenuItem onClick={onCreateFolder}>
                    <ListItemIcon>
                        <CreateNewFolderIcon sx={{ color: theme.textLight }} fontSize="small" />
                    </ListItemIcon>
                    <Typography variant="inherit">Vytvořit složku</Typography>
                </MenuItem>
                <MenuItem onClick={handleCloseContextMenu}>
                    <ListItemIcon>
                        <UploadFileIcon sx={{ color: theme.textLight }} fontSize="small" />
                    </ListItemIcon>
                    <Typography variant="inherit">Nahrát soubor</Typography>
                </MenuItem>
            </Menu>
            <Menu
                anchorEl={contextMenuAnchor}
                keepMounted
                open={Boolean(contextMenuAnchor)}
                onClose={onContextMenuClose}
            >
                {selectedItem && selectedItem.hash ? (
                    <>
                        <MenuItem onClick={() => onEditVideo(selectedItem)}>Upravit</MenuItem>
                        <MenuItem onClick={onContextMenuClose}>Sdílet</MenuItem>
                        <MenuItem onClick={() => onDeleteVideo(selectedItem)}>Smazat</MenuItem>
                    </>
                ) : (
                    <>
                        <MenuItem onClick={() => onEditFolder(selectedItem)}>Upravit</MenuItem>
                        <MenuItem onClick={onContextMenuClose}>Sdílet</MenuItem>
                        <MenuItem onClick={() => onDeleteFolder(selectedItem)}>Smazat</MenuItem>
                    </>
                )}
            </Menu>
        </FileManagerContainerStyle>
    );
};

export default FileExplorer;
