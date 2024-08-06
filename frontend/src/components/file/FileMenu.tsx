import React from 'react';
import { Menu, MenuItem } from '@mui/material';

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
}) => (
    <Menu
        anchorEl={contextMenuAnchor}
        keepMounted
        open={Boolean(contextMenuAnchor)}
        onClose={onContextMenuClose}
    >
        {selectedItem && selectedItem.hash ? [
            <MenuItem key="edit" onClick={() => { onEditVideo(selectedItem); onContextMenuClose(); }}>Upravit</MenuItem>,
            <MenuItem key="share" onClick={onContextMenuClose}>Sdílet</MenuItem>,
            <MenuItem key="delete" onClick={() => { onDeleteVideo(selectedItem); onContextMenuClose(); }}>Smazat</MenuItem>
        ] : [
            <MenuItem key="edit" onClick={() => { onEditFolder(selectedItem); onContextMenuClose(); }}>Upravit</MenuItem>,
            <MenuItem key="share" onClick={onContextMenuClose}>Sdílet</MenuItem>,
            <MenuItem key="delete" onClick={() => { onDeleteFolder(selectedItem); onContextMenuClose(); }}>Smazat</MenuItem>
        ]}
    </Menu>
);

export default FileMenu;
