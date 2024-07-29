import React from 'react';
import { Box, Typography } from '@mui/material';
import CheckCircleIcon from '@mui/icons-material/CheckCircle';
import StorageIcon from '@mui/icons-material/Storage';
import SecurityIcon from '@mui/icons-material/Security';
import ShareIcon from '@mui/icons-material/Share';
import CloudUploadIcon from '@mui/icons-material/CloudUpload';
import DevicesIcon from '@mui/icons-material/Devices';

const ProjectInfo = () => {
    return (
        <Box>
            <Typography variant="h5" gutterBottom sx={{ marginBottom: '1.5em'}}>
                „Video úložiště kdykoliv a kdekoliv“
            </Typography>
            <Box display="flex" alignItems="center" mb={2}>
                <CheckCircleIcon sx={{ mr: 1 }} />
                <Typography>Bezplatná registrace</Typography>
            </Box>
            <Box display="flex" alignItems="center" mb={2}>
                <StorageIcon sx={{ mr: 1 }} />
                <Typography>Úložiště pro vaše videa</Typography>
            </Box>
            <Box display="flex" alignItems="center" mb={2}>
                <CloudUploadIcon sx={{ mr: 1 }} />
                <Typography>Snadný upload</Typography>
            </Box>
            <Box display="flex" alignItems="center" mb={2}>
                <SecurityIcon sx={{ mr: 1 }} />
                <Typography>Plně zabezpečené</Typography>
            </Box>
            <Box display="flex" alignItems="center" mb={2}>
                <ShareIcon sx={{ mr: 1 }} />
                <Typography>Možnost sdílení</Typography>
            </Box>
            <Box display="flex" alignItems="center" mb={2}>
                <DevicesIcon sx={{ mr: 1 }} />
                <Typography>Responzivní design</Typography>
            </Box>
        </Box>
    );
};

export default ProjectInfo;
