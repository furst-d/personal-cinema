import React, { useEffect, useState, useCallback } from "react";
import Loading from "../loading/Loading";
import MediaPanel from "./MediaPanel";
import { fetchFolders, fetchSharedFolders, fetchSharedVideos, fetchVideos } from "../../service/fileManagerService";
import {Box, Typography, Button, Pagination} from "@mui/material";
import ArrowBackIcon from "@mui/icons-material/ArrowBack";
import { useTheme } from "styled-components";

const MediaDashboard: React.FC = () => {
    const [currentFolder, setCurrentFolder] = useState<any | null>(null);
    const [isCurrentFolderShared, setIsCurrentFolderShared] = useState<boolean>(false);
    const [videos, setVideos] = useState<any[]>([]);
    const [folders, setFolders] = useState<any[]>([]);
    const [sharedVideos, setSharedVideos] = useState<any[]>([]);
    const [sharedFolders, setSharedFolders] = useState<any[]>([]);
    const [loading, setLoading] = useState<boolean>(true);

    const [page, setPage] = useState<number>(1);
    const [maxPages, setMaxPages] = useState<number>(1);

    const theme = useTheme();

    const videoLimit = 16;
    const folderLimit = 12;
    const sortBy = 'update_date';

    const calculateMaxPages = (
        videoCount: number | undefined | null,
        folderCount: number | undefined | null,
        sharedVideoCount: number | undefined | null,
        sharedFolderCount: number | undefined | null
    ) => {
        const videoPages = videoCount ? Math.ceil(videoCount / videoLimit) : 0;
        const folderPages = folderCount ? Math.ceil(folderCount / folderLimit) : 0;
        const sharedVideoPages = sharedVideoCount ? Math.ceil(sharedVideoCount / videoLimit) : 0;
        const sharedFolderPages = sharedFolderCount ? Math.ceil(sharedFolderCount / folderLimit) : 0;

        return Math.max(videoPages, folderPages, sharedVideoPages, sharedFolderPages, 1);
    };

    const fetchMedia = useCallback(() => {
        setLoading(true);
        const folderId = currentFolder ? currentFolder.id : null;
        const videoOffset = (page - 1) * videoLimit;
        const folderOffset = (page - 1) * folderLimit;

        const ownDataPromises = [
            fetchVideos(videoLimit, videoOffset, sortBy, folderId),
            fetchFolders(folderLimit, folderOffset, sortBy, folderId)
        ];

        const sharedDataPromises = currentFolder
            ? []
            : [
                fetchSharedVideos(videoLimit, videoOffset, sortBy),
                fetchSharedFolders(folderLimit, folderOffset, sortBy)
            ];

        Promise.all([
            ...ownDataPromises,
            ...sharedDataPromises
        ])
            .then((results) => {
                const [videosData, foldersData, sharedVideosData, sharedFoldersData] = results;

                setVideos(videosData.data);
                setFolders(foldersData.data);

                if (!currentFolder) {
                    setSharedVideos(sharedVideosData.data);
                    setSharedFolders(sharedFoldersData.data);

                    const maxPages = calculateMaxPages(
                        videosData.totalCount,
                        foldersData.totalCount,
                        sharedVideosData.totalCount,
                        sharedFoldersData.totalCount
                    );
                    setMaxPages(maxPages);
                } else {
                    setSharedVideos([]);
                    setSharedFolders([]);

                    const maxPages = calculateMaxPages(
                        videosData.totalCount,
                        foldersData.totalCount,
                        null,
                        null
                    );
                    setMaxPages(maxPages);
                }
            })
            .catch((error) => {
                console.error('Error while fetching videos and folders', error);
            })
            .finally(() => {
                setLoading(false);
            });

    }, [currentFolder, page]);

    useEffect(() => {
        fetchMedia();
    }, [fetchMedia]);

    const handleFolderClick = (folder: any, shared: boolean) => {
        setCurrentFolder(folder);
        setIsCurrentFolderShared(shared);
        setPage(1);
    };

    const handleBackClick = () => {
        setCurrentFolder(!isCurrentFolderShared && currentFolder?.parentId ? { id: currentFolder.parentId } : null);
        setPage(1);
    };

    const handlePageChange = (event: React.ChangeEvent<unknown>, value: number) => {
        setPage(value);
    };

    if (loading) {
        return <Loading />;
    }

    return (
        <>
            {currentFolder && (
                <Button
                    onClick={handleBackClick}
                    startIcon={<ArrowBackIcon />}
                    sx={{
                        color: theme.textLight,
                        backgroundColor: theme.secondary,
                        '&:hover': {
                            backgroundColor: theme.primaryDarker,
                        },
                        mb: 2,
                    }}
                >
                    Zpět
                </Button>
            )}
            <Box mb={5}>
                <MediaPanel videos={videos} folders={folders} onFolderClick={handleFolderClick} shared={false} />
            </Box>
            {(sharedVideos.length > 0 || sharedFolders.length > 0) && (
                <Box mt={5} mb={5}>
                    <Typography variant="h5" gutterBottom>
                        Sdíleno s Vámi
                    </Typography>
                    <MediaPanel videos={sharedVideos} folders={sharedFolders} onFolderClick={handleFolderClick} shared={true} />
                </Box>
            )}
            {maxPages > 1 && (
                <Box mt={5} mb={5} display="flex" justifyContent="center">
                    <Pagination
                        count={maxPages}
                        page={page}
                        onChange={handlePageChange}
                        color="primary"
                    />
                </Box>
            )}
        </>
    );
};

export default MediaDashboard;
