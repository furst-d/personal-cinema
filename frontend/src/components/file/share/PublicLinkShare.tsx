import React, { useEffect, useState } from 'react';
import { Typography, CircularProgress, List, ListItem, ListItemText, Button, Box, Tooltip } from '@mui/material';
import { fetchPublicVideoShare, generatePublicVideoShare } from "../../../service/fileManagerService";
import { toast } from "react-toastify";
import { useTheme } from 'styled-components';

interface PublicLinkShareProps {
    onClose: () => void;
    videoId: string;
}

const PublicLinkShare: React.FC<PublicLinkShareProps> = ({ onClose, videoId }) => {
    const theme = useTheme();

    const [publicShares, setPublicShares] = useState<{ hash: string; createdAt: string; expiredAt: string; viewCount: number }[]>([]);
    const [maxViews, setMaxViews] = useState<number>(0);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        fetchPublicVideoShare(videoId)
            .then(data => {
                const { maxViews, shares } = data;
                setMaxViews(maxViews);
                const sortedData = shares.sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime());
                setPublicShares(sortedData);
            })
            .catch(error => {
                console.error('Error fetching public video shares:', error);
            })
            .finally(() => {
                setLoading(false);
            });
    }, [videoId]);

    const handleCopyLink = (hash: string) => {
        const link = `${window.location.origin}/share/${hash}`;
        navigator.clipboard.writeText(link)
            .then(() => {
                toast.success('Odkaz zkopírován do schránky');
            })
            .catch(error => {
                toast.error('Nepodařilo se zkopírovat odkaz');
                console.error('Error copying link:', error);
            });
    };

    const handleGenerateNewLink = () => {
        generatePublicVideoShare(videoId)
            .then(newShare => {
                setPublicShares(prevShares => [
                    newShare,
                    ...prevShares.sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime())
                ]);
                toast.success('Nový veřejný odkaz byl úspěšně vygenerován');
            })
            .catch(error => {
                toast.error('Nepodařilo se vygenerovat nový odkaz');
                console.error('Error generating new public link:', error);
            });
    };

    if (loading) {
        return <CircularProgress />;
    }

    const hasUnexpiredLink = publicShares.some(share => {
        const isExpired = new Date(share.expiredAt).getTime() < Date.now();
        return !isExpired;
    });

    return (
        <>
            {publicShares.length > 0 && (
                <>
                    <Typography variant="subtitle1" gutterBottom>
                        Vygenerované odkazy
                    </Typography>
                    <List>
                        {publicShares.map((share) => {
                            const isExpired = new Date(share.expiredAt).getTime() < Date.now();
                            const isMaxViewsReached = share.viewCount >= maxViews;
                            const isActive = !isExpired && !isMaxViewsReached;

                            return (
                                <ListItem
                                    key={share.hash}
                                    sx={{
                                        backgroundColor: isActive ? 'rgba(0,128,0,0.1)' : 'rgba(255,0,0,0.1)',
                                        borderLeft: isActive ? '4px solid green' : '4px solid red',
                                        marginBottom: '8px',
                                        borderRadius: '4px',
                                        display: 'flex',
                                        justifyContent: 'space-between',
                                        alignItems: 'center',
                                        color: isActive ? 'green' : 'red',
                                    }}
                                >
                                    <ListItemText
                                        primary={`Vytvořeno: ${new Date(share.createdAt).toLocaleString()}`}
                                        secondary={`Platnost do: ${new Date(share.expiredAt).toLocaleString()}`}
                                        sx={{ flex: '1 1 auto', color: isActive ? 'green' : 'red' }}
                                    />
                                    <Box sx={{ display: 'flex', flexDirection: 'column', alignItems: 'flex-end' }}>
                                        <Tooltip title={`Počet zhlédnutí: ${share.viewCount}/${maxViews}`}>
                                            <Typography sx={{ marginBottom: '4px', fontWeight: 'bold', color: isActive ? 'green' : 'red' }}>
                                                {`${share.viewCount}/${maxViews}`}
                                            </Typography>
                                        </Tooltip>
                                        {isActive && (
                                            <Button
                                                variant="contained"
                                                onClick={() => handleCopyLink(share.hash)}
                                                sx={{
                                                    fontSize: '0.875rem',
                                                    padding: '4px 8px',
                                                    minWidth: 'auto',
                                                    backgroundColor: 'green',
                                                    color: theme.textLight,
                                                    '&:hover': {
                                                        backgroundColor: 'darkgreen',
                                                    },
                                                }}
                                            >
                                                Zkopírovat odkaz
                                            </Button>
                                        )}
                                    </Box>
                                </ListItem>
                            );
                        })}
                    </List>
                </>
            )}
            <Box sx={{ display: 'flex', justifyContent: 'flex-end', marginTop: '16px', gap: 1 }}>
                <Button
                    onClick={onClose}
                    variant="contained"
                    color="secondary"
                >
                    Zrušit
                </Button>
                <Button
                    variant="contained"
                    color="primary"
                    onClick={handleGenerateNewLink}
                    disabled={publicShares.length > 0 && hasUnexpiredLink}
                >
                    Vygenerovat nový odkaz
                </Button>
            </Box>
        </>
    );
};

export default PublicLinkShare;
