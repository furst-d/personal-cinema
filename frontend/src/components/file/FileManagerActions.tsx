import React from 'react';
import { Box, Button } from "@mui/material";
import UploadFileIcon from '@mui/icons-material/UploadFile';
import CreateNewFolderIcon from '@mui/icons-material/CreateNewFolder';

interface FileManagerActionsProps {
    handleUploadClick: () => void;
    handleCreateFolderClick: () => void;
}

const FileManagerActions: React.FC<FileManagerActionsProps> = ({ handleUploadClick, handleCreateFolderClick }) => (
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
);

export default FileManagerActions;
