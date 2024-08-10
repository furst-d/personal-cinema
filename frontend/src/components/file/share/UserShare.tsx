import React from 'react';
import { TextField } from '@mui/material';

interface UserShareProps {
    email: string;
    setEmail: (email: string) => void;
}

const UserShare: React.FC<UserShareProps> = ({ email, setEmail }) => {
    return (
        <TextField
            autoFocus
            margin="dense"
            label="Email uživatele"
            type="email"
            fullWidth
            value={email}
            onChange={(e) => setEmail(e.target.value)}
        />
    );
};

export default UserShare;
