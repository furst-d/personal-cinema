import React, { useState } from "react";
import { Container, Typography } from "@mui/material";
import { DndProvider } from 'react-dnd';
import { HTML5Backend } from 'react-dnd-html5-backend';
import Loading from "../loading/Loading";
import FileExplorer from "./FileExplorer";
import FileManagerActions from "./FileManagerActions";
import FileManagerModals from "./FileManagerModals";
import useFileManagerHandlers from "../../hook/file/useFileManagerHandlers";

const FileManager: React.FC = () => {
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
        handleContextMenuOpen,
        handleContextMenuClose,
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
                    onCreateFolder={handleCreateFolderClick}
                    contextMenuAnchor={contextMenuAnchor}
                    selectedItem={selectedItem}
                    moveItem={handleMoveItem}
                    parentFolderId={parentFolderId}
                />
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
