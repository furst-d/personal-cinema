import React, { useState, useEffect } from 'react';
import {Button, TextField, Box, Typography} from '@mui/material';
import { validateShareForm } from '../../utils/validator';
import { shareVideo, shareFolder } from '../../service/fileManagerService';
import { toast } from 'react-toastify';

interface UserShareFormProps {
    onClose: () => void;
    isVideo: boolean;
    selectedItem: any;
}

const UserShareForm: React.FC<UserShareFormProps> = ({ onClose, isVideo, selectedItem }) => {
    const [email, setEmail] = useState<string>('');
    const [errors, setErrors] = useState<{ missing?: boolean; email?: string }>({});
    const [errorMessage, setErrorMessage] = useState("");

    useEffect(() => {
        setErrors(validateShareForm(email));
    }, [email]);

    const handleSubmit = async (event: React.FormEvent) => {
        event.preventDefault();
        setErrorMessage("");
        const formErrors = validateShareForm(email);
        if (Object.keys(formErrors).length > 0) {
            setErrors(formErrors);
            return;
        }

        if (isVideo) {
            await handleShareVideo(email);
        } else {
            await handleShareFolder(email)
        }
    };

    const handleShareVideo = async (email: string) => {
        try {
            await shareVideo(selectedItem.id, email);
            toast.success('Žádost o sdílení videa byla odeslána');
            setEmail('');
        } catch (error: any) {
            if (error.response?.status === 403) {
                setErrorMessage("Nelze sdílet video sám se sebou");
            } else {
                console.error('Error sharing video:', error);
                toast.error('Nepodařilo se odeslat žádost o sdílení videa');
            }
        }
    }

    const handleShareFolder = async (email: string) => {
        try {
            await shareFolder(selectedItem.id, email);
            toast.success('Žádost o sdílení složky byla odeslána');
            setEmail('');
        } catch (error: any) {
            if (error.response?.status === 403) {
                setErrorMessage("Nelze sdílet složku sám se sebou");
            } else {
                console.error('Error sharing folder:', error);
                toast.error('Nepodařilo se odeslat žádost o sdílení složky');
            }
        }
    }

    return (
        <>
            <form onSubmit={handleSubmit}>
                <TextField
                    label="Email uživatele"
                    variant="outlined"
                    margin="dense"
                    fullWidth
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    error={Boolean(errors.email)}
                    helperText={errors.email}
                />
                {errorMessage && <Typography color="error" sx={{marginTop: '15px', marginBottom: '15px'}}>{errorMessage}</Typography>}
                <Box sx={{ display: 'flex', justifyContent: 'flex-end', gap: 1, marginTop: '16px', marginBottom: '16px' }}>
                    <Button
                        onClick={onClose}
                        variant="contained"
                        color="secondary"
                    >
                        Zrušit
                    </Button>
                    <Button
                        type="submit"
                        variant="contained"
                        color="primary"
                        disabled={errors.missing || Boolean(errors.email)}
                    >
                        Sdílet
                    </Button>
                </Box>
            </form>
        </>
    );
};

export default UserShareForm;
