import React from 'react';
import { Grid, Typography } from "@mui/material";
import FolderOpenIcon from '@mui/icons-material/FolderOpen';
import { FileManagerEmptyFolderStyle } from "../../styles/file/FileManager";
import FolderList from "./FolderList";
import VideoList from "./VideoList";

interface FileListProps {
    folders: any[];
    videos: any[];
    onFolderClick: (folderId: string) => void;
    onContextMenuOpen: (event: React.MouseEvent<HTMLElement>, item: any) => void;
    moveItem: (item: any, targetFolderId: string | null) => void;
    theme: any;
    onVideoDoubleClick: (hash: string) => void;
}

const FileList: React.FC<FileListProps> = ({
  folders,
  videos,
  onFolderClick,
  onContextMenuOpen,
  moveItem,
  theme,
  onVideoDoubleClick
}) => (
    <Grid container>
        {folders.length === 0 && videos.length === 0 ? (
            <FileManagerEmptyFolderStyle>
                <FolderOpenIcon sx={{ fontSize: 60, color: theme.textLight }} />
                <Typography variant="body1" sx={{ color: theme.textLight }}>
                    Složka je prázdná.
                </Typography>
            </FileManagerEmptyFolderStyle>
        ) : (
            <>
                <FolderList
                    folders={folders}
                    onFolderClick={onFolderClick}
                    onContextMenuOpen={onContextMenuOpen}
                    moveItem={moveItem}
                    hasVideos={videos.length > 0}
                />
                <VideoList
                    videos={videos}
                    onVideoDoubleClick={onVideoDoubleClick}
                    onContextMenuOpen={onContextMenuOpen}
                />
            </>
        )}
    </Grid>
);

export default FileList;
