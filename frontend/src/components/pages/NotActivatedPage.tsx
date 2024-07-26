import React from "react";
import { Helmet, HelmetProvider } from "react-helmet-async";
import { Button, Typography } from "@mui/material";
import { useNavigate } from "react-router-dom";
import { CenteredContainerStyle } from "../../styles/layout/Application";
import { CenterFormWrapperStyle, StyledLink } from "../../styles/form/Form";
import { useAuth } from "../providers/AuthProvider";
import {resendActivationEmail} from "../../service/authService";

const NotActivatedPage: React.FC = () => {
    const navigate = useNavigate();
    const { user, logout } = useAuth();

    const handleResendEmail = () => {
        if (user && user.email) {
            resendActivationEmail(user.email);
        }
    };

    const handleLogoutAndNavigate = () => {
        logout();
        navigate("/login");
    };

    return (
        <HelmetProvider>
            <Helmet>
                <title>Účet není aktivovaný</title>
            </Helmet>
            <CenteredContainerStyle>
                <CenterFormWrapperStyle>
                    <Typography variant="h4" gutterBottom>
                        Účet není aktivovaný
                    </Typography>
                    <Typography variant="body1" gutterBottom>
                        Váš účet není aktivovaný. Zkontrolujte svůj email a klikněte na aktivační odkaz.
                    </Typography>
                    <Button
                        variant="contained"
                        fullWidth
                        style={{ margin: '20px 0' }}
                        onClick={handleResendEmail}
                    >
                        Znovu odeslat aktivační email
                    </Button>
                    <StyledLink variant="body2" onClick={handleLogoutAndNavigate} style={{ cursor: "pointer" }}>
                        Už jste aktivovali svůj účet? Pokračujte zde
                    </StyledLink>
                </CenterFormWrapperStyle>
            </CenteredContainerStyle>
        </HelmetProvider>
    );
}

export default NotActivatedPage;
