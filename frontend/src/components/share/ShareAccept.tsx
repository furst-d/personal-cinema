import React, {useEffect, useState} from "react";
import {useLocation} from "react-router-dom";
import {CenterFormWrapperStyle} from "../../styles/form/Form";
import {Button, Typography} from "@mui/material";
import {CenteredContainerStyle} from "../../styles/layout/Application";

const ShareAccept: React.FC = () => {
    const [message, setMessage] = useState("");
    const [success, setSuccess] = useState(true);
    const location = useLocation();

    const queryParams = new URLSearchParams(location.search);
    const type = queryParams.get("type");
    const token = queryParams.get("token");

    useEffect(() => {

    }, [type, token]);

    return (
        <CenteredContainerStyle>
            <CenterFormWrapperStyle>
                <Typography variant="h5" gutterBottom>
                    Příjem položky
                </Typography>
                <Typography variant="body1" gutterBottom>
                    {message || "Probíhá příjem položky..."}
                </Typography>
                {success && (
                    <Button
                        variant="contained"
                        // onClick={handleLogoutAndNavigate}
                        fullWidth
                        style={{ marginTop: '20px' }}
                    >
                        Pokračovat na hlavní stranu
                    </Button>
                )}
            </CenterFormWrapperStyle>
        </CenteredContainerStyle>
    );
}

export default ShareAccept;