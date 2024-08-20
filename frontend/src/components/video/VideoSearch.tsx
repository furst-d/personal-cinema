import React, { useState, useEffect, useCallback } from "react";
import { Box, Typography, Button, Pagination } from "@mui/material";
import { searchVideos, searchFolders, fetchVideos, fetchFolders } from "../../service/fileManagerService";
import MediaPanel from "./MediaPanel";
import debounce from "lodash.debounce";
import Loading from "../loading/Loading";
import ArrowBackIcon from "@mui/icons-material/ArrowBack";
import { useTheme } from "styled-components";

interface VideoSearchProps {
    phrase: string;
}

const videoLimit = 16;
const folderLimit = 12;

const VideoSearch: React.FC<VideoSearchProps> = ({ phrase }) => {
    const [videos, setVideos] = useState<any[]>([]);
    const [folders, setFolders] = useState<any[]>([]);
    const [currentFolder, setCurrentFolder] = useState<any | null>(null);
    const [loading, setLoading] = useState<boolean>(true);

    const [page, setPage] = useState<number>(1);
    const [maxPages, setMaxPages] = useState<number>(1);

    const theme = useTheme();

    const calculateMaxPages = (
        videoCount: number | undefined | null,
        folderCount: number | undefined | null
    ) => {
        const videoPages = videoCount ? Math.ceil(videoCount / videoLimit) : 0;
        const folderPages = folderCount ? Math.ceil(folderCount / folderLimit) : 0;

        return Math.max(videoPages, folderPages, 1);
    };

    const performSearch = useCallback(
        debounce((phrase: string) => {
            setLoading(true);
            const videoOffset = (page - 1) * videoLimit;
            const folderOffset = (page - 1) * folderLimit;

            if (currentFolder) {
                Promise.all([
                    fetchVideos(videoLimit, videoOffset, "updatedAt", "DESC", currentFolder.id),
                    fetchFolders(folderLimit, folderOffset, "updatedAt", "DESC", currentFolder.id)
                ])
                    .then(([videoResults, folderResults]) => {
                        setVideos(videoResults.data);
                        setFolders(folderResults.data);

                        const maxPages = calculateMaxPages(
                            videoResults.totalCount,
                            folderResults.totalCount
                        );
                        setMaxPages(maxPages);
                    })
                    .catch((error) => {
                        console.error("Error while fetching folder contents", error);
                    })
                    .finally(() => {
                        setLoading(false);
                    });
            } else {
                Promise.all([
                    searchVideos(phrase, videoLimit, videoOffset),
                    searchFolders(phrase, folderLimit, folderOffset)
                ])
                    .then(([videoResults, folderResults]) => {
                        setVideos(videoResults.data);
                        setFolders(folderResults.data);

                        const maxPages = calculateMaxPages(
                            videoResults.totalCount,
                            folderResults.totalCount
                        );
                        setMaxPages(maxPages);
                    })
                    .catch((error) => {
                        console.error("Error while searching", error);
                    })
                    .finally(() => {
                        setLoading(false);
                    });
            }
        }, 200),
        [currentFolder, page]
    );

    useEffect(() => {
        if (phrase.trim() || currentFolder) {
            performSearch(phrase);
        } else {
            setVideos([]);
            setFolders([]);
        }
    }, [phrase, currentFolder, performSearch]);

    const handleFolderClick = (folder: any) => {
        setCurrentFolder(folder);
        setPage(1);
    };

    const handleBackClick = () => {
        setCurrentFolder(currentFolder?.parentId ? { id: currentFolder.parentId } : null);
        setPage(1);
    };

    const handlePageChange = (event: React.ChangeEvent<unknown>, value: number) => {
        setPage(value);
    };

    if (loading) {
        return <Loading />;
    }

    return (
        <Box>
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
            {(videos.length > 0 || folders.length > 0 || currentFolder) ? (
                <>
                    <MediaPanel
                        videos={videos}
                        folders={folders}
                        onFolderClick={handleFolderClick}
                        shared={false}
                    />
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
            ) : (
                <Typography variant="body1">Nebyly nalezeny žádné výsledky.</Typography>
            )}
        </Box>
    );
};

export default VideoSearch;
