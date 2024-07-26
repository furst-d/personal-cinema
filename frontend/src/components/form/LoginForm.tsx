import { Button, TextField, Typography, Link } from "@mui/material";
import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { CenteredContainerStyle } from "../../styles/layout/Application";
import { CenterFormWrapperStyle, StyledLink } from "../../styles/form/Form";
import { login } from "../../service/authService";
import { useAuth } from "../providers/AuthProvider";

const LoginForm = () => {
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [errorMessage, setErrorMessage] = useState("");
    const navigate = useNavigate();
    const { login: authLogin } = useAuth();

    const handleSubmit = async (event: React.FormEvent) => {
        event.preventDefault();
        setErrorMessage("");
        const result = await login(email, password);
        if (result.success) {
            authLogin(result.data);
            navigate("/");
        } else {
            setErrorMessage(result.message);
        }
    };

    return (
        <CenteredContainerStyle>
            <CenterFormWrapperStyle>
                <Typography variant="h5" gutterBottom>
                    Přihlásit se
                </Typography>
                {errorMessage && <Typography color="error">{errorMessage}</Typography>}
                <form onSubmit={handleSubmit}>
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
                    <Button
                        type="submit"
                        variant="contained"
                        fullWidth
                        style={{ margin: '20px 0' }}
                    >
                        Přihlásit se
                    </Button>
                </form>
                <StyledLink variant="body2" onClick={() => navigate("/register")}>
                    Nemáte účet? Registrujte se zde
                </StyledLink>
                <StyledLink variant="body2" style={{ marginTop: '10px' }} onClick={() => navigate("/forgot-password")}>
                    Zapomněli jste heslo?
                </StyledLink>
            </CenterFormWrapperStyle>
        </CenteredContainerStyle>
    );
}

export default LoginForm;
