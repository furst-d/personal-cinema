import React from 'react';
import UserShareForm from "../form/UserShareForm";
import ShareVideoList from './ShareVideoList';
import ShareFolderList from './ShareFolderList';

interface UserShareProps {
    onClose: () => void;
    isVideo: boolean;
    selectedItem: any;
}

const UserShare: React.FC<UserShareProps> = ({ onClose, isVideo, selectedItem }) => {
    return (
        <>
            <UserShareForm onClose={onClose} isVideo={isVideo} selectedItem={selectedItem} />

            {isVideo ? (
                <ShareVideoList videoId={selectedItem.id} />
            ) : (
                <ShareFolderList folderId={selectedItem.id} />
            )}
        </>
    );
};

export default UserShare;
