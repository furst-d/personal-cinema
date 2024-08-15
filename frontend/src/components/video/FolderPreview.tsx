import React from "react";
import { Typography, Box } from "@mui/material";
import FolderIcon from "@mui/icons-material/Folder";
import { styled } from "styled-components";

interface FolderPreviewProps {
    folder: any;
    onClick: (folder: any) => void;
}

const FolderContainer = styled(Box)`
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    &:hover {
        opacity: 0.8;
    }
`;

const FolderPreview: React.FC<FolderPreviewProps> = ({ folder, onClick }) => {
    const handleClick = () => {
        onClick(folder);
    };

    return (
        <FolderContainer onClick={handleClick}>
            <FolderIcon sx={{ fontSize: 80, color: "inherit" }} />
            <Typography variant="h6" align="center">
                {folder.name}
            </Typography>
        </FolderContainer>
    );
};

export default FolderPreview;
