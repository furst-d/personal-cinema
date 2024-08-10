import React, { useState } from 'react';
import { Dialog, DialogTitle, DialogContent, Select, MenuItem, InputLabel, FormControl } from '@mui/material';
import PublicLinkShare from './PublicLinkShare';
import UserShare from "./UserShare";

interface ShareDialogProps {
    open: boolean;
    onClose: () => void;
    selectedItem: any;
}

const ShareDialog: React.FC<ShareDialogProps> = ({ open, onClose, selectedItem }) => {
    const [shareType, setShareType] = useState<string>('user');

    if (!selectedItem) {
        return null;
    }

    const isVideo = Boolean(selectedItem?.hash);

    return (
        <Dialog open={open} onClose={onClose} fullWidth>
            <DialogTitle>{isVideo ? 'Sdílet soubor' : 'Sdílet složku'}</DialogTitle>
            <DialogContent>
                {isVideo ? (
                    <FormControl fullWidth margin="dense">
                        <InputLabel>Typ sdílení</InputLabel>
                        <Select
                            value={shareType}
                            onChange={(e) => setShareType(e.target.value)}
                            label={'Typ sdílení'}
                            sx={{ marginBottom: '16px' }}
                        >
                            <MenuItem value="user">Sdílet s uživatelem</MenuItem>
                            <MenuItem value="public">Veřejný odkaz</MenuItem>
                        </Select>
                        {shareType === 'user' ? (
                            <UserShare onClose={onClose} isVideo={isVideo} selectedItem={selectedItem} />
                        ) : (
                            <PublicLinkShare />
                        )}
                    </FormControl>
                ) : (
                    <UserShare onClose={onClose} isVideo={isVideo} selectedItem={selectedItem} />
                )}
            </DialogContent>
        </Dialog>
    );
};

export default ShareDialog;
