import React, { useState } from 'react';
import { Dialog, DialogTitle, DialogContent, DialogActions, Button, Select, MenuItem, InputLabel, FormControl } from '@mui/material';
import PublicLinkShare from './PublicLinkShare';
import UserShareForm from "../../form/UserShareForm";

interface ShareDialogProps {
    open: boolean;
    onClose: () => void;
    selectedItem: any;
    onShareWithUser: (email: string) => void;
    onGeneratePublicLink: () => void;
}

const ShareDialog: React.FC<ShareDialogProps> = ({ open, onClose, selectedItem, onShareWithUser, onGeneratePublicLink }) => {
    const [shareType, setShareType] = useState<string>('user');

    return (
        <Dialog open={open} onClose={onClose} fullWidth>
            <DialogTitle>{selectedItem?.hash ? 'Sdílet soubor' : 'Sdílet složku'}</DialogTitle>
            <DialogContent>
                {selectedItem?.hash ? (
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
                            <UserShareForm onShare={onShareWithUser} onClose={onClose} />
                        ) : (
                            <PublicLinkShare />
                        )}
                    </FormControl>
                ) : (
                    <UserShareForm onShare={onShareWithUser} onClose={onClose} />
                )}
            </DialogContent>
        </Dialog>
    );
};

export default ShareDialog;
