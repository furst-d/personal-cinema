import React from 'react';
import { Menu, MenuItem, ListItemIcon, ListItemText } from '@mui/material';
import EditIcon from '@mui/icons-material/Edit';
import DownloadIcon from '@mui/icons-material/Download';
import ShareIcon from '@mui/icons-material/Share';
import DeleteIcon from '@mui/icons-material/Delete';
import {useTheme} from "styled-components";

interface FileMenuProps {
    contextMenuAnchor: HTMLElement | null;
    selectedItem: any;
    onContextMenuClose: () => void;
    onEditFolder: (item: any) => void;
    onEditVideo: (item: any) => void;
    onDeleteFolder: (item: any) => void;
    onDeleteVideo: (item: any) => void;
}

const FileMenu: React.FC<FileMenuProps> = ({
   contextMenuAnchor,
   selectedItem,
   onContextMenuClose,
   onEditFolder,
   onEditVideo,
   onDeleteFolder,
   onDeleteVideo
}) => {
    const theme = useTheme();

    return (
        <Menu
            anchorEl={contextMenuAnchor}
            keepMounted
            open={Boolean(contextMenuAnchor)}
            onClose={onContextMenuClose}
        >
            {selectedItem && selectedItem.hash ? [
                <MenuItem key="edit" onClick={() => { onEditVideo(selectedItem); onContextMenuClose(); }}>
                    <ListItemIcon>
                        <EditIcon style={{ color: theme.textLight }} />
                    </ListItemIcon>
                    <ListItemText primary="Upravit" />
                </MenuItem>,
                <MenuItem key="download" onClick={onContextMenuClose}>
                    <ListItemIcon>
                        <DownloadIcon style={{ color: theme.textLight }} />
                    </ListItemIcon>
                    <ListItemText primary="Stáhnout" />
                </MenuItem>,
                <MenuItem key="share" onClick={onContextMenuClose}>
                    <ListItemIcon>
                        <ShareIcon style={{ color: theme.textLight }} />
                    </ListItemIcon>
                    <ListItemText primary="Sdílet" />
                </MenuItem>,
                <MenuItem key="delete" onClick={() => { onDeleteVideo(selectedItem); onContextMenuClose(); }}>
                    <ListItemIcon>
                        <DeleteIcon style={{ color: theme.textLight }} />
                    </ListItemIcon>
                    <ListItemText primary="Smazat" />
                </MenuItem>
            ] : [
                <MenuItem key="edit" onClick={() => { onEditFolder(selectedItem); onContextMenuClose(); }}>
                    <ListItemIcon>
                        <EditIcon style={{ color: theme.textLight }} />
                    </ListItemIcon>
                    <ListItemText primary="Upravit" />
                </MenuItem>,
                <MenuItem key="share" onClick={onContextMenuClose}>
                    <ListItemIcon>
                        <ShareIcon style={{ color: theme.textLight }} />
                    </ListItemIcon>
                    <ListItemText primary="Sdílet" />
                </MenuItem>,
                <MenuItem key="delete" onClick={() => { onDeleteFolder(selectedItem); onContextMenuClose(); }}>
                    <ListItemIcon>
                        <DeleteIcon style={{ color: theme.textLight }} />
                    </ListItemIcon>
                    <ListItemText primary="Smazat" />
                </MenuItem>
            ]}
        </Menu>
    );
};

export default FileMenu;
