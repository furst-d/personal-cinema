import { Button, TextField, Typography } from "@mui/material";
import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { CenteredContainerStyle } from "../../styles/layout/Application";
import {CenterFormWrapperStyle, StyledLink} from "../../styles/form/Form";

const ForgottenPasswordForm = () => {
    const [email, setEmail] = useState("");
    const navigate = useNavigate();

    const handleForgotPassword = (event: React.FormEvent) => {
        event.preventDefault();
        // Add forgot password logic here
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
                    />
                    <Button
                        type="submit"
                        variant="contained"
                        fullWidth
                        style={{ margin: '20px 0' }}
                    >
                        Odeslat žádost
                    </Button>
                </form>
                <StyledLink variant="body2" onClick={() => navigate("/login")}>
                    Zpět na přihlášení
                </StyledLink>
            </CenterFormWrapperStyle>
        </CenteredContainerStyle>
    );
}

export default ForgottenPasswordForm;
