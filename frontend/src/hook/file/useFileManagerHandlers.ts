import React, { useState, useEffect, useCallback } from "react";
import { toast } from "react-toastify";
import {
    fetchFolders,
    fetchVideos,
    createFolder,
    updateFolder,
    updateVideo,
    deleteFolder,
    deleteVideo,
    moveItem
} from "../../service/fileManagerService";
import { uploadVideoMetadata, uploadVideoToCdn } from "../../service/uploadService";
import { removeFileExtension } from "../../utils/namer";

const useFileManagerHandlers = (initialFolderId: string | null, setLoading: (loading: boolean) => void) => {
    const [folders, setFolders] = useState<any[]>([]);
    const [videos, setVideos] = useState<any[]>([]);
    const [currentFolderId, setCurrentFolderId] = useState<string | null>(initialFolderId);
    const [parentFolderId, setParentFolderId] = useState<string | null>(null);
    const [contextMenuAnchor, setContextMenuAnchor] = useState<null | HTMLElement>(null);
    const [selectedItem, setSelectedItem] = useState<any>(null);
    const [dialogOpen, setDialogOpen] = useState<boolean>(false);
    const [newName, setNewName] = useState<string>("");
    const [nameError, setNameError] = useState<string>("");
    const [editingType, setEditingType] = useState<"folder" | "video" | null>(null);
    const [deleteDialogOpen, setDeleteDialogOpen] = useState<boolean>(false);
    const [deletingType, setDeletingType] = useState<"folder" | "video" | null>(null);
    const [uploadMenuAnchor, setUploadMenuAnchor] = useState<null | { mouseX: number, mouseY: number }>(null);
    const [uploadingVideos, setUploadingVideos] = useState<{ name: string; file: File }[]>([]);

    useEffect(() => {
        setLoading(true);
        Promise.all([fetchFolders(currentFolderId), fetchVideos(currentFolderId)])
            .then(([foldersData, videosData]) => {
                setFolders(foldersData);
                setVideos(videosData);
            })
            .finally(() => setLoading(false));
    }, [currentFolderId]);

    const handleContextMenuOpen = (event: React.MouseEvent<HTMLElement>, item: any) => {
        setContextMenuAnchor(event.currentTarget);
        setSelectedItem(item);
    };

    const handleUploadMenuOpen = (event: React.MouseEvent<HTMLElement>) => {
        event.preventDefault();
        setUploadMenuAnchor({
            mouseX: event.clientX - 2,
            mouseY: event.clientY - 4,
        });
    };

    const handleContextMenuClose = () => {
        setContextMenuAnchor(null);
    };

    const handleUploadMenuClose = () => {
        setUploadMenuAnchor(null);
    };

    const handleFolderClick = (folderId: string) => {
        setParentFolderId(currentFolderId);
        setCurrentFolderId(folderId);
    };

    const handleBackClick = () => {
        setCurrentFolderId(parentFolderId);
        setParentFolderId(null);
    };

    const handleVideoDoubleClick = (hash: string) => {
        window.open(`/videos/${hash}`, "_blank");
    };

    const handleUploadClick = () => {
        console.log("Aktuální složka pro nahrání souboru:", currentFolderId);
    };

    const handleCreateFolderClick = () => {
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

            const newFolder = await createFolder(requestData);
            setFolders([...folders, newFolder]);
            handleDialogClose();
            toast.success("Složka byla úspěšně vytvořena");
        } catch (error) {
            console.error("Error creating folder", error);
        }
    };

    const handleEditFolderClick = (item: any) => {
        setNewName(item.name);
        setSelectedItem(item);
        setEditingType("folder");
        setDialogOpen(true);
    };

    const handleEditFolder = async () => {
        if (!newName.trim()) {
            setNameError("Název složky musí být vyplněn");
            return;
        }

        try {
            const requestData = { name: newName };
            await updateFolder(selectedItem.id, requestData);

            const updatedFolders = folders.map(folder =>
                folder.id === selectedItem.id ? { ...folder, name: newName } : folder
            );

            setFolders(updatedFolders);
            handleDialogClose();
            toast.success("Složka byla úspěšně upravena");
        } catch (error) {
            console.error("Error editing folder", error);
        }

        setSelectedItem(null);
    };

    const handleEditVideoClick = (item: any) => {
        setNewName(item.name);
        setSelectedItem(item);
        setEditingType("video");
        setDialogOpen(true);
    };

    const handleEditVideo = async () => {
        if (!newName.trim()) {
            setNameError("Název videa musí být vyplněn");
            return;
        }

        try {
            const requestData: any = { name: newName, folderId: currentFolderId };
            await updateVideo(selectedItem.id, requestData);

            const updatedVideos = videos.map(video =>
                video.id === selectedItem.id ? { ...video, name: newName } : video
            );

            setVideos(updatedVideos);
            handleDialogClose();
            toast.success("Video bylo úspěšně upraveno");
        } catch (error) {
            console.error("Error editing video", error);
        }

        setSelectedItem(null);
    };

    const handleDeleteFolder = async (item?: any) => {
        if (!deletingType) {
            setDeletingType("folder");
            setSelectedItem(item);
            setDeleteDialogOpen(true);
            return;
        }

        if (!selectedItem) {
            console.error("No item selected for deletion");
            return;
        }

        try {
            await deleteFolder(selectedItem.id);
            const updatedFolders = folders.filter(folder => folder.id !== selectedItem.id);
            setFolders(updatedFolders);
            handleDeleteDialogClose();
            toast.success("Složka byla úspěšně smazána");
        } catch (error) {
            console.error("Error deleting folder", error);
        }
    };

    const handleDeleteVideo = async (item?: any) => {
        if (!deletingType) {
            setDeletingType("video");
            setSelectedItem(item);
            setDeleteDialogOpen(true);
            return;
        }

        if (!selectedItem) {
            console.error("No item selected for deletion");
            return;
        }

        try {
            await deleteVideo(selectedItem.id);
            const updatedVideos = videos.filter(video => video.id !== selectedItem.id);
            setVideos(updatedVideos);
            handleDeleteDialogClose();
            toast.success("Video bylo úspěšně smazáno");
        } catch (error) {
            console.error("Error deleting video", error);
        }
    };

    const handleDeleteDialogClose = () => {
        setDeleteDialogOpen(false);
        setDeletingType(null);
    };

    const handleMoveItem = useCallback(async (item: any, targetFolderId: string | null) => {
        try {
            await moveItem(item, targetFolderId);

            if (item.type === 'folder') {
                const updatedFolders = folders.filter(folder => folder.id !== item.id);
                setFolders(updatedFolders);
                toast.success("Složka byla úspěšně přesunuta");
            } else {
                const updatedVideos = videos.filter(video => video.id !== item.id);
                setVideos(updatedVideos);
                toast.success("Soubor byl úspěšně přesunut");
            }
        } catch (error) {
            console.error("Error moving item", error);
        }
    }, [folders, videos]);

    // Upload Handlers
    const handleFileChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        if (event.target.files) {
            const newVideos = Array.from(event.target.files).map(file => ({
                name: removeFileExtension(file.name),
                file
            }));
            setUploadingVideos(prevVideos => [...prevVideos, ...newVideos]);
        }
    };

    const handleNameChange = (index: number, newName: string) => {
        setUploadingVideos(prevVideos => {
            const updatedVideos = [...prevVideos];
            updatedVideos[index].name = removeFileExtension(newName);
            return updatedVideos;
        });
    };

    const handleSingleUploadCompleted = (uploadedVideo: { name: string; file: File }) => {
        const videoWithDate = {
            ...uploadedVideo,
            createdAt: new Date().toISOString()
        };
        setVideos(prevVideos => [...prevVideos, videoWithDate]);
        toast.success(`${uploadedVideo.name} byl úspěšně nahrán.`);
    };

    const handleAllUploadsComplete = () => {
        setUploadingVideos([]);
        toast.success("Všechna videa byla úspěšně nahrána.");
    };

    return {
        folders,
        videos,
        currentFolderId,
        parentFolderId,
        contextMenuAnchor,
        selectedItem,
        dialogOpen,
        newName,
        nameError,
        editingType,
        deleteDialogOpen,
        deletingType,
        uploadMenuAnchor,
        uploadingVideos,
        handleFileChange,
        handleNameChange,
        handleContextMenuOpen,
        handleUploadMenuOpen,
        handleContextMenuClose,
        handleUploadMenuClose,
        handleFolderClick,
        handleBackClick,
        handleVideoDoubleClick,
        handleUploadClick,
        handleCreateFolderClick,
        handleDialogClose,
        handleCreateFolder,
        handleEditFolderClick,
        handleEditFolder,
        handleEditVideoClick,
        handleEditVideo,
        handleDeleteFolder,
        handleDeleteVideo,
        handleDeleteDialogClose,
        handleMoveItem,
        handleSingleUploadCompleted,
        handleAllUploadsComplete,
        setNewName,
        setNameError,
        setCurrentFolderId,
        setParentFolderId,
    };
};

export default useFileManagerHandlers;
