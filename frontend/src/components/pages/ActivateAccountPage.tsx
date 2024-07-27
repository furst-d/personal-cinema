import React, { useEffect, useState } from "react";
import { useNavigate, useSearchParams } from "react-router-dom";
import { CenteredContainerStyle } from "../../styles/layout/Application";
import { CenterFormWrapperStyle } from "../../styles/form/Form";
import { activateAccount } from "../../service/authService";
import { Typography, Button } from "@mui/material";
import {useAuth} from "../providers/AuthProvider";

const ActivateAccountPage: React.FC = () => {
    const navigate = useNavigate();
    const { logout } = useAuth();

    const [searchParams] = useSearchParams();
    const token = searchParams.get("token");
    const [message, setMessage] = useState("");

    const handleLogoutAndNavigate = () => {
        logout();
        navigate("/");
    };

    useEffect(() => {
        const activate = async () => {
            if (!token) {
                setMessage("Při pokusu o aktivaci účtu došlo k chybě.");
                return;
            }

            const response = await activateAccount(token);
            setMessage(response.message);
        };
        activate();
    }, [token]);

    return (
        <CenteredContainerStyle>
            <CenterFormWrapperStyle>
                <Typography variant="h5" gutterBottom>
                    Aktivace účtu
                </Typography>
                <Typography variant="body1" gutterBottom>
                    {message || "Probíhá aktivace účtu..."}
                </Typography>
                <Button
                    variant="contained"
                    onClick={handleLogoutAndNavigate}
                    fullWidth
                    style={{ marginTop: '20px' }}
                >
                    Zpět na přihlášení
                </Button>
            </CenterFormWrapperStyle>
        </CenteredContainerStyle>
    );
}

export default ActivateAccountPage;
