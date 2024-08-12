import React, { useEffect, useState, useRef } from "react";
import {useLocation, useNavigate} from "react-router-dom";
import { CenterFormWrapperStyle } from "../../styles/form/Form";
import { Button, Typography, Box } from "@mui/material";
import { CenteredContainerStyle } from "../../styles/layout/Application";
import { acceptFileShare, acceptFolderShare } from "../../service/shareService";
import VideoFileIcon from "@mui/icons-material/VideoFile";
import FolderIcon from "@mui/icons-material/Folder";
import { useTheme } from "styled-components";

const ShareAccept: React.FC = () => {
    const theme = useTheme();
    const navigation = useNavigate();

    const [message, setMessage] = useState("");
    const [loading, setLoading] = useState(true);
    const [item, setItem] = useState<any | null>(null);
    const location = useLocation();
    const hasFetched = useRef(false); // useRef to track if the effect has already run

    const queryParams = new URLSearchParams(location.search);
    const type = queryParams.get("type");
    const token = queryParams.get("token");

    useEffect(() => {
        if (token && !hasFetched.current) {
            hasFetched.current = true;
            if (type === "file") {
                handleAcceptFileShare(token);
            } else if (type === "folder") {
                handleAcceptFolderShare(token);
            } else {
                setLoading(false);
                setMessage("Neplatný typ sdílení.");
            }
        }
    }, [token, type]);

    const handleAcceptFileShare = (token: string) => {
        acceptFileShare(token)
            .then((data: any) => {
                setItem({ ...data, type: "file" });
                setMessage('Soubor byl úspěšně přijat');
            })
            .catch(error => {
                console.error('Failed to accept video share', error);
                setMessage('Nepodařilo se přijmout soubor');
            })
            .finally(() => {
                setLoading(false);
            });
    };

    const handleAcceptFolderShare = (token: string) => {
        acceptFolderShare(token)
            .then((data: any) => {
                setItem({ ...data, type: "folder" });
                setMessage('Složka byla úspěšně přijata');
            })
            .catch(error => {
                console.error('Failed to accept folder share', error);
                setMessage('Nepodařilo se přijmout složku');
            })
            .finally(() => {
                setLoading(false);
            });
    };

    return (
        <CenteredContainerStyle>
            <CenterFormWrapperStyle>
                <Typography variant="h5" gutterBottom>
                    Příjem položky
                </Typography>
                <Typography variant="body1" gutterBottom>
                    {message || "Probíhá příjem položky..."}
                </Typography>

                {item && (
                    <Box sx={{ display: 'flex', alignItems: 'center', marginTop: '20px' }}>
                        <Box sx={{ marginRight: '10px' }}>
                            {item.type === "file" ? (
                                <VideoFileIcon sx={{ fontSize: 40, color: theme.textLight }} />
                            ) : (
                                <FolderIcon sx={{ fontSize: 40, color: theme.textLight }} />
                            )}
                        </Box>
                        <Box>
                            <Typography variant="body1"><strong>Název:</strong> {item.name}</Typography>
                            <Typography variant="body1"><strong>Autor:</strong> {item.account?.email || item.owner?.email}</Typography>
                            {item.type === "file" && (
                                <>
                                    <Typography variant="body1"><strong>Velikost:</strong> {item.size}</Typography>
                                    <Typography variant="body1"><strong>Délka:</strong> {item.length} sekund</Typography>
                                </>
                            )}
                        </Box>
                    </Box>
                )}

                {!loading && (
                    <Button
                        variant="contained"
                        fullWidth
                        style={{ marginTop: '20px' }}
                        onClick={() => navigation("/")}
                    >
                        Pokračovat na hlavní stranu
                    </Button>
                )}
            </CenterFormWrapperStyle>
        </CenteredContainerStyle>
    );
}

export default ShareAccept;
