import React, { useState } from "react";
import { Container, Typography, Menu, MenuItem, ListItemIcon } from "@mui/material";
import { DndProvider } from 'react-dnd';
import { HTML5Backend } from 'react-dnd-html5-backend';
import Loading from "../loading/Loading";
import FileExplorer from "./FileExplorer";
import FileManagerActions from "./FileManagerActions";
import FileManagerModals from "./FileManagerModals";
import useFileManagerHandlers from "../../hook/file/useFileManagerHandlers";
import UploadFileIcon from '@mui/icons-material/UploadFile';
import CreateNewFolderIcon from '@mui/icons-material/CreateNewFolder';
import {useTheme} from "styled-components";

const FileManager: React.FC = () => {
    const theme = useTheme();
    const [loading, setLoading] = useState<boolean>(true);

    const {
        folders,
        videos,
        currentFolderId,
        parentFolderId,
        contextMenuAnchor,
        selectedItem,
        dialogOpen,
        newName,
        nameError,
        isEditing,
        editingType,
        deleteDialogOpen,
        deletingType,
        uploadMenuAnchor,
        handleContextMenuOpen,
        handleContextMenuClose,
        handleUploadMenuOpen,
        handleUploadMenuClose,
        handleFolderClick,
        handleBackClick,
        handleVideoDoubleClick,
        handleUploadClick,
        handleCreateFolderClick,
        handleDialogClose,
        handleCreateFolder,
        handleEditFolder,
        handleEditVideo,
        handleDeleteFolder,
        handleDeleteVideo,
        handleDeleteDialogClose,
        handleMoveItem,
        setNewName,
        setNameError
    } = useFileManagerHandlers(null, setLoading);

    if (loading) {
        return <Loading />;
    }

    return (
        <DndProvider backend={HTML5Backend}>
            <Container>
                <Typography variant="h4" gutterBottom>Správa videí</Typography>
                <FileManagerActions
                    handleUploadClick={handleUploadClick}
                    handleCreateFolderClick={handleCreateFolderClick}
                />
                <FileExplorer
                    folders={folders}
                    videos={videos}
                    currentFolderId={currentFolderId}
                    onFolderClick={handleFolderClick}
                    onBackClick={handleBackClick}
                    onVideoDoubleClick={handleVideoDoubleClick}
                    onContextMenuOpen={handleContextMenuOpen}
                    onContextMenuClose={handleContextMenuClose}
                    onEditFolder={handleEditFolder}
                    onEditVideo={handleEditVideo}
                    onDeleteFolder={handleDeleteFolder}
                    onDeleteVideo={handleDeleteVideo}
                    contextMenuAnchor={contextMenuAnchor}
                    selectedItem={selectedItem}
                    moveItem={handleMoveItem}
                    parentFolderId={parentFolderId}
                    onFileExplorerContextMenu={handleUploadMenuOpen}
                />
                <Menu
                    open={uploadMenuAnchor !== null}
                    onClose={handleUploadMenuClose}
                    anchorReference="anchorPosition"
                    anchorPosition={
                        uploadMenuAnchor !== null
                            ? { top: uploadMenuAnchor.mouseY, left: uploadMenuAnchor.mouseX }
                            : undefined
                    }
                >
                    <MenuItem onClick={() => { handleUploadClick(); handleUploadMenuClose(); }}>
                        <ListItemIcon>
                            <UploadFileIcon sx={{ color: theme.textLight }} />
                        </ListItemIcon>
                        Nahrát soubor
                    </MenuItem>
                    <MenuItem onClick={() => { handleCreateFolderClick(); handleUploadMenuClose(); }}>
                        <ListItemIcon>
                            <CreateNewFolderIcon sx={{ color: theme.textLight }} />
                        </ListItemIcon>
                        Vytvořit složku
                    </MenuItem>
                </Menu>
                <FileManagerModals
                    dialogOpen={dialogOpen}
                    handleDialogClose={handleDialogClose}
                    handleCreateFolder={handleCreateFolder}
                    isEditing={isEditing}
                    editingType={editingType}
                    newName={newName}
                    setNewName={setNewName}
                    nameError={nameError}
                    setNameError={setNameError}
                    handleEditFolder={handleEditFolder}
                    handleEditVideo={handleEditVideo}
                    deleteDialogOpen={deleteDialogOpen}
                    handleDeleteDialogClose={handleDeleteDialogClose}
                    handleDeleteFolder={handleDeleteFolder}
                    handleDeleteVideo={handleDeleteVideo}
                    deletingType={deletingType}
                />
            </Container>
        </DndProvider>
    );
};

export default FileManager;
