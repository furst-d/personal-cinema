import { Button, Link, TextField, Typography } from "@mui/material";
import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { CenteredContainerStyle } from "../../styles/layout/Application";
import {CenterFormWrapperStyle, StyledLink} from "../../styles/form/Form";

const RegisterForm = () => {
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [confirmPassword, setConfirmPassword] = useState("");
    const navigate = useNavigate();

    const handleRegister = (event: React.FormEvent) => {
        event.preventDefault();
        // Add registration logic here
    };

    return (
        <CenteredContainerStyle>
            <CenterFormWrapperStyle>
                <Typography variant="h5" gutterBottom>
                    Registrace
                </Typography>
                <form onSubmit={handleRegister}>
                    <TextField
                        label="Email"
                        variant="outlined"
                        margin="normal"
                        fullWidth
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                    />
                    <TextField
                        label="Heslo"
                        type="password"
                        variant="outlined"
                        margin="normal"
                        fullWidth
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                    />
                    <TextField
                        label="Kontrola hesla"
                        type="password"
                        variant="outlined"
                        margin="normal"
                        fullWidth
                        value={confirmPassword}
                        onChange={(e) => setConfirmPassword(e.target.value)}
                    />
                    <Button
                        type="submit"
                        variant="contained"
                        fullWidth
                        style={{ margin: '20px 0' }}
                    >
                        Registrovat se
                    </Button>
                </form>
                <StyledLink variant="body2" onClick={() => navigate("/login")}>
                    Už máte účet? Přihlaste se zde
                </StyledLink>
            </CenterFormWrapperStyle>
        </CenteredContainerStyle>
    );
}

export default RegisterForm;
