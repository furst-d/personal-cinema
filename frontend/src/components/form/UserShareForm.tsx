import React, { useState, useEffect } from 'react';
import { Button, TextField, Box } from '@mui/material';
import { validateShareForm } from '../../utils/validator';

interface UserShareFormProps {
    onShare: (email: string) => void;
    onClose: () => void;
}

const UserShareForm: React.FC<UserShareFormProps> = ({ onShare, onClose }) => {
    const [email, setEmail] = useState<string>('');
    const [errors, setErrors] = useState<{ missing?: boolean; email?: string }>({});

    useEffect(() => {
        setErrors(validateShareForm(email));
    }, [email]);

    const handleSubmit = (event: React.FormEvent) => {
        event.preventDefault();
        const formErrors = validateShareForm(email);
        if (Object.keys(formErrors).length > 0) {
            setErrors(formErrors);
            return;
        }
        onShare(email);
    };

    return (
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
    );
};

export default UserShareForm;
