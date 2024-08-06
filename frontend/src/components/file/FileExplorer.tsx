import React from "react";
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
import { useTheme } from "styled-components";
import {
    FileManagerContainerStyle,
    FileManagerEmptyFolderStyle,
    FileManagerListItemStyle, FileManagerSeparator
} from "../../styles/file/FileManager";
import { formatDate } from "../../utils/formatter";
import { useDrag, useDrop } from 'react-dnd';

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
    moveItem: (item: any, targetFolderId: string | null) => void;
}

const ItemTypes = {
    FOLDER: 'folder',
    VIDEO: 'video',
};

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
                                                       selectedItem,
                                                       moveItem
                                                   }) => {
    const theme = useTheme();

    return (
        <FileManagerContainerStyle theme={theme}>
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
                                <Folder
                                    folder={folder}
                                    onFolderClick={onFolderClick}
                                    onContextMenuOpen={onContextMenuOpen}
                                    moveItem={moveItem}
                                    theme={theme}
                                />
                                {index < folders.length - 1 && <FileManagerSeparator theme={theme} />}
                            </React.Fragment>
                        ))}
                        {folders.length > 0 && videos.length > 0 && <FileManagerSeparator theme={theme} />}
                        {videos.map((video, index) => {
                            const isProcessing = !video.thumbnailUrl || !video.path;
                            return (
                                <React.Fragment key={video.id}>
                                    <Video
                                        video={video}
                                        onVideoDoubleClick={onVideoDoubleClick}
                                        onContextMenuOpen={onContextMenuOpen}
                                        isProcessing={isProcessing}
                                        theme={theme}
                                    />
                                    {index < videos.length - 1 && <FileManagerSeparator theme={theme} />}
                                </React.Fragment>
                            );
                        })}
                    </>
                )}
            </Grid>
            <Menu
                anchorEl={contextMenuAnchor}
                keepMounted
                open={Boolean(contextMenuAnchor)}
                onClose={onContextMenuClose}
            >
                {selectedItem && selectedItem.hash ? (
                    [
                        <MenuItem key="edit" onClick={() => onEditVideo(selectedItem)}>Upravit</MenuItem>,
                        <MenuItem key="share" onClick={onContextMenuClose}>Sdílet</MenuItem>,
                        <MenuItem key="delete" onClick={() => onDeleteVideo(selectedItem)}>Smazat</MenuItem>
                    ]
                ) : (
                    [
                        <MenuItem key="edit" onClick={() => onEditFolder(selectedItem)}>Upravit</MenuItem>,
                        <MenuItem key="share" onClick={onContextMenuClose}>Sdílet</MenuItem>,
                        <MenuItem key="delete" onClick={() => onDeleteFolder(selectedItem)}>Smazat</MenuItem>
                    ]
                )}
            </Menu>
        </FileManagerContainerStyle>
    );
};

const Folder: React.FC<{ folder: any, onFolderClick: any, onContextMenuOpen: any, moveItem: any, theme: any }> = ({ folder, onFolderClick, onContextMenuOpen, moveItem, theme }) => {
    const [, drag] = useDrag({
        type: ItemTypes.FOLDER,
        item: { ...folder, type: ItemTypes.FOLDER },
    });

    const [, drop] = useDrop({
        accept: [ItemTypes.FOLDER, ItemTypes.VIDEO],
        drop: (item: any) => {
            moveItem(item, folder.id);
        },
    });

    return (
        <Grid item xs={12} ref={node => drag(drop(node))} onDoubleClick={() => onFolderClick(folder.id)}>
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
    );
};

const Video: React.FC<{ video: any, onVideoDoubleClick: any, onContextMenuOpen: any, isProcessing: boolean, theme: any }> = ({ video, onVideoDoubleClick, onContextMenuOpen, isProcessing, theme }) => {
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
                        <Typography variant="body2" className="date">{formatDate(video.createdAt)}</Typography>
                    </Box>
                </Box>
                <IconButton onClick={(e) => onContextMenuOpen(e, video)} sx={{ color: theme.textLight }}>
                    <MoreVertIcon />
                </IconButton>
            </FileManagerListItemStyle>
        </Grid>
    );
};

export default FileExplorer;
