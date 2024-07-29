import { Button, TextField, Typography } from "@mui/material";
import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { CenteredContainerStyle } from "../../styles/layout/Application";
import { CenterFormWrapperStyle, StyledLink } from "../../styles/form/Form";
import { register } from "../../service/authService";
import { toast } from "react-toastify";
import {validateRegisterForm} from "../../utils/validator";

const RegisterForm = () => {
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [confirmPassword, setConfirmPassword] = useState("");
    const [errors, setErrors] = useState<{ missing?: boolean; email?: string; password?: string; confirmPassword?: string }>({});
    const [errorMessage, setErrorMessage] = useState("");
    const navigate = useNavigate();

    useEffect(() => {
        setErrors(validateRegisterForm(email, password, confirmPassword));
    }, [email, password, confirmPassword]);

    const handleRegister = async (event: React.FormEvent) => {
        event.preventDefault();
        setErrorMessage("");
        const formErrors = validateRegisterForm(email, password, confirmPassword);
        if (Object.keys(formErrors).length > 0) {
            setErrors(formErrors);
            return;
        }
        const result = await register(email, password);
        if (result.success) {
            toast.success(result.message);
            navigate("/login");
        } else {
            setErrorMessage(result.message);
            toast.error(result.message);
        }
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
                        value={email || null}
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
                        value={password || null}
                        onChange={(e) => setPassword(e.target.value)}
                        error={Boolean(errors.password)}
                        helperText={errors.password}
                    />
                    <TextField
                        label="Kontrola hesla"
                        type="password"
                        variant="outlined"
                        margin="normal"
                        fullWidth
                        value={confirmPassword || null}
                        onChange={(e) => setConfirmPassword(e.target.value)}
                        error={Boolean(errors.confirmPassword)}
                        helperText={errors.confirmPassword}
                    />
                    <Button
                        type="submit"
                        variant="contained"
                        fullWidth
                        style={{ margin: '20px 0' }}
                        disabled={errors.missing || Boolean(errors.email) || Boolean(errors.password) || Boolean(errors.confirmPassword)}
                    >
                        Registrovat se
                    </Button>
                </form>
                {errorMessage && <Typography color="error" sx={{ marginBottom: '15px' }}>{errorMessage}</Typography>}
                <StyledLink variant="body2" onClick={() => navigate("/login")}>
                    Už máte účet? Přihlaste se zde
                </StyledLink>
            </CenterFormWrapperStyle>
        </CenteredContainerStyle>
    );
}

export default RegisterForm;
