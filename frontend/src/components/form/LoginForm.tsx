import { Button, TextField, Typography } from "@mui/material";
import React, { useState, useEffect } from "react";
import {useLocation, useNavigate} from "react-router-dom";
import { CenterFormWrapperStyle, StyledLink } from "../../styles/form/Form";
import { login } from "../../service/authService";
import { useAuth } from "../providers/AuthProvider";
import {validateLoginForm} from "../../utils/validator";

const LoginForm = () => {
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [errors, setErrors] = useState<{ missing?: boolean; email?: string; password?: string }>({});
    const [errorMessage, setErrorMessage] = useState("");
    const navigate = useNavigate();
    const location = useLocation()
    const { login: authLogin } = useAuth();

    const from = location.state?.from
        ? location.state.from.pathname + (location.state.from.search || "")
        : "/";

    useEffect(() => {
        setErrors(validateLoginForm(email, password));
    }, [email, password]);

    const handleSubmit = async (event: React.FormEvent) => {
        event.preventDefault();
        setErrorMessage("");
        const formErrors = validateLoginForm(email, password);
        if (Object.keys(formErrors).length > 0) {
            setErrors(formErrors);
            return;
        }
        const result: any = await login(email, password);
        if (result.success) {
            authLogin(result.data, from);
        } else {
            setErrorMessage(result.message);
        }
    };

    return (
        <CenterFormWrapperStyle>
            <Typography variant="h5" gutterBottom sx={{marginBottom: '1em'}}>
                Přihlásit se
            </Typography>
            <form onSubmit={handleSubmit}>
                <TextField
                    label="Email"
                    variant="outlined"
                    margin="normal"
                    fullWidth
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    error={Boolean(errors.email)}
                    helperText={errors.email}
                />
                <TextField
                    label="Heslo"
                    type="password"
                    variant="outlined"
                    margin="normal"
                    fullWidth
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    error={Boolean(errors.password)}
                    helperText={errors.password}
                />
                <Button
                    type="submit"
                    variant="contained"
                    fullWidth
                    style={{ margin: '20px 0' }}
                    disabled={errors.missing || Boolean(errors.email) || Boolean(errors.password)}
                >
                    Přihlásit se
                </Button>
            </form>
            {errorMessage && <Typography color="error" sx={{marginBottom: '15px'}}>{errorMessage}</Typography>}
            <StyledLink variant="body2" onClick={() => navigate("/register")}>
                Nemáte účet? Registrujte se zde
            </StyledLink>
            <StyledLink variant="body2" style={{ marginTop: '10px' }} onClick={() => navigate("/forgotten-password")}>
                Zapomněli jste heslo?
            </StyledLink>
        </CenterFormWrapperStyle>
    );
}

export default LoginForm;
