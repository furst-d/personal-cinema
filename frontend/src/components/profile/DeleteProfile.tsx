import React, { useState } from "react";
import { Box, Button, Dialog, DialogActions, DialogContent, DialogContentText, DialogTitle, Typography } from "@mui/material";
import { styled, useTheme } from "styled-components";
import DeleteAccountForm from "../form/DeleteAccountForm";

const DeleteProfileContainer = styled(Box)`
    padding: 16px;
    border-radius: 8px;
    background-color: ${({ theme }) => theme.background};
    color: ${({ theme }) => theme.textLight};
`;

const DangerZoneFieldset = styled.fieldset`
    border: 2px solid ${({ theme }) => theme.primaryDarker};
    padding: 16px;
    border-radius: 8px;
    margin: 0;
`;

const LegendStyle = styled.legend`
    font-size: 1.2em;
    color: ${({ theme }) => theme.primaryDarker};
    padding: 0 10px;
`;

const DeleteProfile: React.FC = () => {
    const [open, setOpen] = useState<boolean>(false);
    const theme = useTheme();

    const handleClickOpen = () => {
        setOpen(true);
    };

    const handleClose = () => {
        setOpen(false);
    };

    return (
        <DeleteProfileContainer>
            <DangerZoneFieldset>
                <LegendStyle>
                    Nebezpečná zóna
                </LegendStyle>
                <Typography variant="h5" gutterBottom sx={{ color: theme.primaryDarker }}>
                    Smazat účet
                </Typography>
                <Button variant="contained" onClick={handleClickOpen} sx={{ backgroundColor: theme.primaryDarker }}>
                    Smazat účet
                </Button>
            </DangerZoneFieldset>
            <Dialog open={open} onClose={handleClose}>
                <DialogTitle>Opravdu chcete smazat účet?</DialogTitle>
                <DialogContent>
                    <DialogContentText color="text">
                        Pokud chcete smazat svůj účet, zadejte prosím své heslo a potvrďte akci. Tato akce je nevratná.
                    </DialogContentText>
                    <DeleteAccountForm />
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleClose} color="primary">
                        Zrušit
                    </Button>
                </DialogActions>
            </Dialog>
        </DeleteProfileContainer>
    );
};

export default DeleteProfile;
