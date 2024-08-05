import React, { useState, useEffect } from "react";
import {
    Container,
    Typography,
    Box,
    Button
} from "@mui/material";
import UploadFileIcon from '@mui/icons-material/UploadFile';
import CreateNewFolderIcon from '@mui/icons-material/CreateNewFolder';
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
        console.log("Aktuální složka pro vytvoření nové složky:", currentFolderId);
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
                contextMenuAnchor={contextMenuAnchor}
            />
        </Container>
    );
};

export default FileManager;
