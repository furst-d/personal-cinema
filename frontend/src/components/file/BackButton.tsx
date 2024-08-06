import React from 'react';
import { IconButton } from "@mui/material";
import ArrowBackIcon from '@mui/icons-material/ArrowBack';
import { useDrop } from 'react-dnd';
import { ItemTypes } from "../../types/file";
import {useTheme} from "styled-components";

interface BackButtonProps {
    currentFolderId: string | null;
    onBackClick: () => void;
    moveItem: (item: any, targetFolderId: string | null) => void;
    parentFolderId: string | null;
}

const BackButton: React.FC<BackButtonProps> = ({ currentFolderId, onBackClick, moveItem, parentFolderId }) => {
    const theme = useTheme();

    const [, dropBack] = useDrop({
        accept: [ItemTypes.FOLDER, ItemTypes.VIDEO],
        drop: (item) => {
            moveItem(item, parentFolderId);
        },
    });

    return (
        currentFolderId && (
            <IconButton ref={dropBack} onClick={onBackClick} sx={{ marginBottom: '10px', color: theme.textLight }}>
                <ArrowBackIcon />
            </IconButton>
        )
    );
};

export default BackButton;
