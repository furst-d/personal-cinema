import React, { useState } from 'react';
import { useLogin, useNotify, Notification, useTranslate } from 'react-admin';
import { Card, CardActions, Button, TextField, Typography, CircularProgress, Container } from '@mui/material';

const LoginPage = () => {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(false);
    const login = useLogin();
    const notify = useNotify();
    const translate = useTranslate();

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        login({ username, password }).catch(() => {
            setLoading(false);
            notify(translate('ra.notification.not_authorized'), { type: 'warning' });
        });
    };

    return (
        <Container
            sx={{
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                height: '100vh',
                backgroundColor: '#313131',
            }}
        >
            <form onSubmit={submit} noValidate>
                <Card sx={{ padding: '2em', maxWidth: '400px', width: '100%' }}>
                    <Typography variant="h4" align="center" gutterBottom>
                        Admin Login
                    </Typography>
                    <TextField
                        label="Email"
                        name="username"
                        type="email"
                        value={username}
                        onChange={e => setUsername(e.target.value)}
                        fullWidth
                        margin="normal"
                    />
                    <TextField
                        label="Password"
                        name="password"
                        type="password"
                        value={password}
                        onChange={e => setPassword(e.target.value)}
                        fullWidth
                        margin="normal"
                    />
                    <CardActions sx={{ justifyContent: 'center' }}>
                        <Button
                            type="submit"
                            variant="contained"
                            color="primary"
                            disabled={loading}
                        >
                            {loading ? <CircularProgress size={24} /> : 'Login'}
                        </Button>
                    </CardActions>
                </Card>
                <Notification />
            </form>
        </Container>
    );
};

export default LoginPage;
