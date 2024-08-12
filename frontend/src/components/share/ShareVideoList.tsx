import React, { useEffect, useState } from 'react';
import { CircularProgress } from '@mui/material';
import {toast} from "react-toastify";
import ShareList from "./ShareList";
import {deleteVideoShare, fetchVideoShare} from "../../service/shareService";

interface ShareVideoListProps {
    videoId: string;
}

const ShareVideoList: React.FC<ShareVideoListProps> = ({ videoId }) => {
    const [shared, setShared] = useState<{ id: string; email: string; createdAt: string }[]>([]);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        fetchVideoShare(videoId)
            .then(data => {
                setShared(data);
            })
            .catch(error => {
                console.error('Error loading shared users:', error);
            })
            .finally(() => {
                setLoading(false);
            });
    }, [videoId]);

    const handleUnShare = (shareId: string) => {
        deleteVideoShare(shareId)
            .then(() => {
                setShared(prevShared => prevShared.filter(share => share.id !== shareId));
                toast.success('Sdílení zrušeno');
            })
            .catch(error => {
                console.error(`Failed to unshare video with id ${shareId}`, error);
                toast.error('Nepodařilo se zrušit sdílení');
            });
    };

    if (loading) {
        return <CircularProgress />;
    }

    return <ShareList shared={shared} onUnShare={handleUnShare} />
};

export default ShareVideoList;
