import React from 'react';
import EditDialog from "./dialog/EditDialog";
import DeleteDialog from "./dialog/DeleteDialog";

interface FileManagerModalsProps {
    dialogOpen: boolean;
    handleDialogClose: () => void;
    handleCreateFolder: () => void;
    isEditing: boolean;
    editingType: "folder" | "video" | null;
    newName: string;
    setNewName: (name: string) => void;
    nameError: string;
    setNameError: (error: string) => void;
    handleEditFolder: (item: any) => void;
    handleEditVideo: (item: any) => void;
    deleteDialogOpen: boolean;
    handleDeleteDialogClose: () => void;
    handleDeleteFolder: () => void;
    handleDeleteVideo: () => void;
    deletingType: "folder" | "video" | null;
}

const FileManagerModals: React.FC<FileManagerModalsProps> = ({
     dialogOpen,
     handleDialogClose,
     handleCreateFolder,
     isEditing,
     editingType,
     newName,
     setNewName,
     nameError,
     setNameError,
     handleEditFolder,
     handleEditVideo,
     deleteDialogOpen,
     handleDeleteDialogClose,
     handleDeleteFolder,
     handleDeleteVideo,
     deletingType
 }) => {
    return (
        <>
            <EditDialog
                open={dialogOpen}
                onClose={handleDialogClose}
                onSave={isEditing ? (editingType === "folder" ? handleEditFolder : handleEditVideo) : handleCreateFolder}
                isEditing={isEditing}
                editingType={editingType}
                newName={newName}
                setNewName={setNewName}
                nameError={nameError}
                setNameError={setNameError}
            />
            <DeleteDialog
                open={deleteDialogOpen}
                onClose={handleDeleteDialogClose}
                onDelete={deletingType === "folder" ? handleDeleteFolder : handleDeleteVideo}
                deletingType={deletingType}
            />
        </>
    );
};

export default FileManagerModals;
