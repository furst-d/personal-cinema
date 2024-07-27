import React from "react";
import {Box, CircularProgress} from "@mui/material";

const Loading: React.FC = () => {
    return (
        <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '100%' }}>
            <CircularProgress size={60} />
        </Box>
    );
}

export default Loading;