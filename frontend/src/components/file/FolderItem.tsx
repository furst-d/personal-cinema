import React from "react";
import {useDrag, useDrop} from "react-dnd";
import {Box, Grid, IconButton, ListItemIcon, ListItemText, Typography} from "@mui/material";
import {FileManagerListItemStyle} from "../../styles/file/FileManager";
import FolderIcon from "@mui/icons-material/Folder";
import {formatDate} from "../../utils/formatter";
import MoreVertIcon from "@mui/icons-material/MoreVert";
import {ItemTypes} from "../../types/file";

interface FolderItemProps {
    folder: any;
    onFolderClick: (folderId: string) => void;
    onContextMenuOpen: (event: React.MouseEvent<HTMLElement>, item: any) => void;
    moveItem: (item: any, targetFolderId: string | null) => void;
    theme: any;
}

const FolderItem: React.FC<FolderItemProps> = ({ folder, onFolderClick, onContextMenuOpen, moveItem, theme }) => {
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

export default FolderItem;
