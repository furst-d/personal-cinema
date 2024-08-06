import React from "react";
import { useTheme } from "styled-components";
import { FileManagerContainerStyle } from "../../styles/file/FileManager";
import FileMenu from "./FileMenu";
import FileList from "./FileList";
import BackButton from "./BackButton";

interface FileExplorerProps {
    folders: any[];
    videos: any[];
    currentFolderId: string | null;
    onFolderClick: (folderId: string) => void;
    onBackClick: () => void;
    onVideoDoubleClick: (hash: string) => void;
    onContextMenuOpen: (event: React.MouseEvent<HTMLElement>, item: any) => void;
    onContextMenuClose: () => void;
    onEditFolder: (item: any) => void;
    onEditVideo: (item: any) => void;
    onDeleteFolder: (item: any) => void;
    onDeleteVideo: (item: any) => void;
    contextMenuAnchor: HTMLElement | null;
    selectedItem: any;
    moveItem: (item: any, targetFolderId: string | null) => void;
    parentFolderId: string | null;
}

const FileExplorer: React.FC<FileExplorerProps> = ({
                                                       folders,
                                                       videos,
                                                       currentFolderId,
                                                       onFolderClick,
                                                       onBackClick,
                                                       onVideoDoubleClick,
                                                       onContextMenuOpen,
                                                       onContextMenuClose,
                                                       onEditFolder,
                                                       onEditVideo,
                                                       onDeleteFolder,
                                                       onDeleteVideo,
                                                       contextMenuAnchor,
                                                       selectedItem,
                                                       moveItem,
                                                       parentFolderId
                                                   }) => {
    const theme = useTheme();

    return (
        <FileManagerContainerStyle theme={theme}>
            <BackButton
                currentFolderId={currentFolderId}
                onBackClick={onBackClick}
                moveItem={moveItem}
                parentFolderId={parentFolderId}
            />
            <FileList
                folders={folders}
                videos={videos}
                onFolderClick={onFolderClick}
                onContextMenuOpen={onContextMenuOpen}
                moveItem={moveItem}
                theme={theme}
                onVideoDoubleClick={onVideoDoubleClick}
            />
            <FileMenu
                contextMenuAnchor={contextMenuAnchor}
                selectedItem={selectedItem}
                onContextMenuClose={onContextMenuClose}
                onEditFolder={onEditFolder}
                onEditVideo={onEditVideo}
                onDeleteFolder={onDeleteFolder}
                onDeleteVideo={onDeleteVideo}
            />
        </FileManagerContainerStyle>
    );
};

export default FileExplorer;
