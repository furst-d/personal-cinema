import { Button, TextField, Typography } from "@mui/material";
import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { CenteredContainerStyle } from "../../styles/layout/Application";
import { CenterFormWrapperStyle, StyledLink } from "../../styles/form/Form";
import {validateForgottenPasswordForm} from "../../utils/validator";
import {resendResetPasswordEmail} from "../../service/authService";

const ForgottenPasswordForm = () => {
    const [email, setEmail] = useState("");
    const [errors, setErrors] = useState<{ missing?: boolean; email?: string }>({});
    const [errorMessage, setErrorMessage] = useState("");
    const navigate = useNavigate();

    useEffect(() => {
        setErrors(validateForgottenPasswordForm(email));
    }, [email]);

    const handleForgotPassword = async (event: React.FormEvent) => {
        event.preventDefault();
        setErrorMessage("");
        const formErrors = validateForgottenPasswordForm(email);
        if (Object.keys(formErrors).length > 0) {
            setErrors(formErrors);
            return;
        }
        await resendResetPasswordEmail(email);
    };

    return (
        <CenteredContainerStyle>
            <CenterFormWrapperStyle>
                <Typography variant="h5" gutterBottom>
                    Obnova hesla
                </Typography>
                <form onSubmit={handleForgotPassword}>
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
                    <Button
                        type="submit"
                        variant="contained"
                        fullWidth
                        style={{ margin: '20px 0' }}
                        disabled={errors.missing || Boolean(errors.email)}
                    >
                        Odeslat žádost
                    </Button>
                </form>
                {errorMessage && <Typography color="error" sx={{ marginBottom: '15px' }}>{errorMessage}</Typography>}
                <StyledLink variant="body2" onClick={() => navigate("/login")}>
                    Zpět na přihlášení
                </StyledLink>
            </CenterFormWrapperStyle>
        </CenteredContainerStyle>
    );
}

export default ForgottenPasswordForm;
