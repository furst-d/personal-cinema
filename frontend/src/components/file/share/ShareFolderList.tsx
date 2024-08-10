import React, { useEffect, useState } from 'react';
import { CircularProgress } from '@mui/material';
import { fetchFolderShare, deleteFolderShare } from "../../../service/fileManagerService";
import { toast } from "react-toastify";
import ShareList from "./ShareList";

interface ShareFolderListProps {
    folderId: string;
}

const ShareFolderList: React.FC<ShareFolderListProps> = ({ folderId }) => {
    const [shared, setShared] = useState<{ id: string; email: string; createdAt: string }[]>([]);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        fetchFolderShare(folderId)
            .then(data => {
                setShared(data);
            })
            .catch(error => {
                console.error('Error loading shared users:', error);
            })
            .finally(() => {
                setLoading(false);
            });
    }, [folderId]);

    const handleUnShare = (shareId: string) => {
        deleteFolderShare(shareId)
            .then(() => {
                setShared(prevShared => prevShared.filter(share => share.id !== shareId));
                toast.success('Sdílení zrušeno');
            })
            .catch(error => {
                console.error(`Failed to unshare folder with id ${shareId}`, error);
                toast.error('Nepodařilo se zrušit sdílení');
            });
    };

    if (loading) {
        return <CircularProgress />;
    }

    return <ShareList shared={shared} onUnShare={handleUnShare} />
};

export default ShareFolderList;
