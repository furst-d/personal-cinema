import React, {useState} from 'react';
import { Menu, MenuItem, ListItemIcon, ListItemText } from '@mui/material';
import EditIcon from '@mui/icons-material/Edit';
import DownloadIcon from '@mui/icons-material/Download';
import ShareIcon from '@mui/icons-material/Share';
import DeleteIcon from '@mui/icons-material/Delete';
import {useTheme} from "styled-components";
import ShareDialog from "./share/ShareDialog";

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
    const [shareDialogOpen, setShareDialogOpen] = useState<boolean>(false);

    const handleShare = () => {
        setShareDialogOpen(true);
        onContextMenuClose();
    };

    return (
        <>
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
                    <MenuItem key="share" onClick={handleShare}>
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
                    <MenuItem key="share" onClick={handleShare}>
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

            <ShareDialog
                open={shareDialogOpen}
                onClose={() => setShareDialogOpen(false)}
                selectedItem={selectedItem}
            />
        </>
    );
};

export default FileMenu;
