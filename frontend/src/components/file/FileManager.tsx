import React, { useState, useEffect } from "react";
import {
    Container,
    Typography,
    Box,
    Button,
    Dialog,
    DialogActions,
    DialogContent,
    DialogTitle,
    TextField
} from "@mui/material";
import UploadFileIcon from '@mui/icons-material/UploadFile';
import CreateNewFolderIcon from '@mui/icons-material/CreateNewFolder';
import { toast } from "react-toastify";
import axiosPrivate from "../../api/axiosPrivate";
import Loading from "../loading/Loading";
import FileExplorer from "./FileExplorer";

const FileManager: React.FC = () => {
    const [folders, setFolders] = useState<any[]>([]);
    const [videos, setVideos] = useState<any[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [currentFolderId, setCurrentFolderId] = useState<string | null>(null);
    const [parentFolderId, setParentFolderId] = useState<string | null>(null);
    const [contextMenuAnchor, setContextMenuAnchor] = useState<null | HTMLElement>(null);
    const [selectedItem, setSelectedItem] = useState<any>(null);
    const [dialogOpen, setDialogOpen] = useState<boolean>(false);
    const [newName, setNewName] = useState<string>("");
    const [nameError, setNameError] = useState<string>("");
    const [isEditing, setIsEditing] = useState<boolean>(false);
    const [editingType, setEditingType] = useState<"folder" | "video" | null>(null);
    const [deleteDialogOpen, setDeleteDialogOpen] = useState<boolean>(false);
    const [deletingType, setDeletingType] = useState<"folder" | "video" | null>(null);

    useEffect(() => {
        setLoading(true);
        Promise.all([fetchFolders(), fetchVideos()])
            .finally(() => setLoading(false));
    }, [currentFolderId]);

    const fetchFolders = async () => {
        try {
            const response = await axiosPrivate.get('/v1/personal/folders', {
                params: {
                    limit: 1000,
                    sortBy: 'name',
                    parentId: currentFolderId || 0
                }
            });
            setFolders(response.data.payload.data);
        } catch (error) {
            console.error("Error fetching folders", error);
        }
    };

    const fetchVideos = async () => {
        try {
            const response = await axiosPrivate.get('/v1/personal/videos', {
                params: {
                    limit: 1000,
                    sortBy: 'name',
                    folderId: currentFolderId || 0
                }
            });
            setVideos(response.data.payload.data);
        } catch (error) {
            console.error("Error fetching videos", error);
        }
    };

    const handleContextMenuOpen = (event: React.MouseEvent<HTMLElement>, item: any) => {
        setContextMenuAnchor(event.currentTarget);
        setSelectedItem(item);
    };

    const handleContextMenuClose = () => {
        setContextMenuAnchor(null);
        setSelectedItem(null);
    };

    const handleFolderClick = (folderId: string) => {
        setParentFolderId(currentFolderId);
        setCurrentFolderId(folderId);
    };

    const handleBackClick = () => {
        setCurrentFolderId(parentFolderId);
        setParentFolderId(null); // This resets the parentFolderId. You might need to handle it differently if there's more levels of folders.
    };

    const handleVideoDoubleClick = (hash: string) => {
        window.open(`/videos/${hash}`, "_blank");
    };

    const handleUploadClick = () => {
        console.log("Aktuální složka pro nahrání souboru:", currentFolderId);
    };

    const handleCreateFolderClick = () => {
        setIsEditing(false);
        setNewName("");
        setEditingType("folder");
        setDialogOpen(true);
    };

    const handleDialogClose = () => {
        setDialogOpen(false);
        setNewName("");
        setNameError("");
    };

    const handleCreateFolder = async () => {
        if (!newName.trim()) {
            setNameError("Název složky musí být vyplněn");
            return;
        }

        try {
            const requestData: any = { name: newName };
            if (currentFolderId) {
                requestData.parentId = currentFolderId;
            }

            const response = await axiosPrivate.post('/v1/personal/folders', requestData);
            const newFolder = response.data.payload.data;
            setFolders([...folders, newFolder]); // Přidejte novou složku do stavu
            handleDialogClose();
            toast.success("Složka byla úspěšně vytvořena");
        } catch (error) {
            console.error("Error creating folder", error);
        }
    };

    const handleEditFolder = async () => {
        if (!newName.trim()) {
            setNameError("Název složky musí být vyplněn");
            return;
        }

        try {
            const requestData = { name: newName };
            await axiosPrivate.put(`/v1/personal/folders/${selectedItem.id}`, requestData);

            const updatedFolders = folders.map(folder =>
                folder.id === selectedItem.id ? { ...folder, name: newName } : folder
            );

            setFolders(updatedFolders);
            handleDialogClose();
            toast.success("Složka byla úspěšně upravena");
        } catch (error) {
            console.error("Error editing folder", error);
        }
    };

    const handleEditVideo = async () => {
        if (!newName.trim()) {
            setNameError("Název videa musí být vyplněn");
            return;
        }

        try {
            const requestData = { name: newName, folderId: currentFolderId };
            await axiosPrivate.put(`/v1/personal/videos/${selectedItem.id}`, requestData);

            const updatedVideos = videos.map(video =>
                video.id === selectedItem.id ? { ...video, name: newName } : video
            );

            setVideos(updatedVideos);
            handleDialogClose();
            toast.success("Video bylo úspěšně upraveno");
        } catch (error) {
            console.error("Error editing video", error);
        }
    };

    const handleEditFolderClick = (item: any) => {
        setIsEditing(true);
        setNewName(item.name);
        setSelectedItem(item);
        setEditingType("folder");
        setDialogOpen(true);
    };

    const handleEditVideoClick = (item: any) => {
        setIsEditing(true);
        setNewName(item.name);
        setSelectedItem(item);
        setEditingType("video");
        setDialogOpen(true);
    };

    const handleDeleteFolderClick = (item: any) => {
        setDeletingType("folder");
        setSelectedItem(item);
        setDeleteDialogOpen(true);
    };

    const handleDeleteVideoClick = (item: any) => {
        setDeletingType("video");
        setSelectedItem(item);
        setDeleteDialogOpen(true);
    };

    const handleDeleteDialogClose = () => {
        setDeleteDialogOpen(false);
        setSelectedItem(null);
        setDeletingType(null);
    };

    const handleDeleteFolder = async () => {
        try {
            await axiosPrivate.delete(`/v1/personal/folders/${selectedItem.id}`);
            const updatedFolders = folders.filter(folder => folder.id !== selectedItem.id);
            setFolders(updatedFolders);
            handleDeleteDialogClose();
            toast.success("Složka byla úspěšně smazána");
        } catch (error) {
            console.error("Error deleting folder", error);
        }
    };

    const handleDeleteVideo = async () => {
        try {
            await axiosPrivate.delete(`/v1/personal/videos/${selectedItem.id}`);
            const updatedVideos = videos.filter(video => video.id !== selectedItem.id);
            setVideos(updatedVideos);
            handleDeleteDialogClose();
            toast.success("Video bylo úspěšně smazáno");
        } catch (error) {
            console.error("Error deleting video", error);
        }
    };

    if (loading) {
        return <Loading />;
    }

    return (
        <Container>
            <Typography variant="h4" gutterBottom>Správa videí</Typography>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', marginBottom: '10px' }}>
                <Box sx={{ marginBottom: '10px' }}>
                    <Button
                        variant="contained"
                        color="primary"
                        startIcon={<UploadFileIcon />}
                        onClick={handleUploadClick}
                        sx={{ marginRight: '10px' }}
                    >
                        Nahrát soubor
                    </Button>
                    <Button
                        variant="contained"
                        color="secondary"
                        startIcon={<CreateNewFolderIcon />}
                        onClick={handleCreateFolderClick}
                    >
                        Vytvořit složku
                    </Button>
                </Box>
            </Box>
            <FileExplorer
                folders={folders}
                videos={videos}
                currentFolderId={currentFolderId}
                onFolderClick={handleFolderClick}
                onBackClick={handleBackClick}
                onVideoDoubleClick={handleVideoDoubleClick}
                onContextMenuOpen={handleContextMenuOpen}
                onContextMenuClose={handleContextMenuClose}
                onEditFolder={handleEditFolderClick}
                onEditVideo={handleEditVideoClick}
                onDeleteFolder={handleDeleteFolderClick}
                onDeleteVideo={handleDeleteVideoClick}
                onCreateFolder={handleCreateFolderClick}
                contextMenuAnchor={contextMenuAnchor}
                selectedItem={selectedItem}
            />
            <Dialog open={dialogOpen} onClose={handleDialogClose} fullWidth>
                <DialogTitle>{isEditing ? (editingType === "folder" ? "Upravit složku" : "Upravit video") : "Vytvořit novou složku"}</DialogTitle>
                <DialogContent>
                    <TextField
                        autoFocus
                        margin="dense"
                        label={editingType === "folder" ? "Název složky" : "Název videa"}
                        fullWidth
                        value={newName}
                        onChange={(e) => {
                            setNewName(e.target.value);
                            setNameError("");
                        }}
                        error={Boolean(nameError)}
                        helperText={nameError}
                    />
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleDialogClose} color="primary">
                        Zrušit
                    </Button>
                    <Button onClick={isEditing ? (editingType === "folder" ? handleEditFolder : handleEditVideo) : handleCreateFolder} color="primary">
                        {isEditing ? "Upravit" : "Vytvořit"}
                    </Button>
                </DialogActions>
            </Dialog>
            <Dialog open={deleteDialogOpen} onClose={handleDeleteDialogClose}>
                <DialogTitle>Potvrdit smazání</DialogTitle>
                <DialogContent>
                    <Typography>
                        {deletingType === "folder"
                            ? "Opravdu chcete smazat tuto složku a všechny její podřízené složky a videa?"
                            : "Opravdu chcete smazat toto video? Tato akce je nevratná."}
                    </Typography>
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleDeleteDialogClose} color="primary">
                        Zrušit
                    </Button>
                    <Button onClick={deletingType === "folder" ? handleDeleteFolder : handleDeleteVideo} color="primary">
                        Smazat
                    </Button>
                </DialogActions>
            </Dialog>
        </Container>
    );
};

export default FileManager;
