import React from 'react';
import { FileManagerSeparator } from "../../styles/file/FileManager";
import FolderItem from "./FolderItem";
import {useTheme} from "styled-components";

interface FolderListProps {
    folders: any[];
    onFolderClick: (folderId: string) => void;
    onContextMenuOpen: (event: React.MouseEvent<HTMLElement>, item: any) => void;
    moveItem: (item: any, targetFolderId: string | null) => void;
    hasVideos: boolean;
}

const FolderList: React.FC<FolderListProps> = ({
    folders,
    onFolderClick,
    onContextMenuOpen,
    moveItem,
    hasVideos
}) => {
    const theme = useTheme();

    return (
        <>
            {folders.map((folder, index) => (
                <React.Fragment key={folder.id}>
                    <FolderItem
                        folder={folder}
                        onFolderClick={onFolderClick}
                        onContextMenuOpen={onContextMenuOpen}
                        moveItem={moveItem}
                        theme={theme}
                    />
                    {index < folders.length - 1 && <FileManagerSeparator theme={theme} />}
                </React.Fragment>
            ))}
            {folders.length > 0 && hasVideos && <FileManagerSeparator theme={theme} />}
        </>
    )
};

export default FolderList;
