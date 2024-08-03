import { Button, TextField, Typography } from "@mui/material";
import React, { useState, useEffect } from "react";
import { changePassword } from "../../service/authService";
import { validatePasswordChangeForm } from "../../utils/validator";
import {FormWrapperStyle} from "../../styles/form/Form";

const ChangePasswordForm = () => {
    const [oldPassword, setOldPassword] = useState("");
    const [newPassword, setNewPassword] = useState("");
    const [confirmPassword, setConfirmPassword] = useState("");
    const [errors, setErrors] = useState<{ missing?: boolean; oldPassword?: string; newPassword?: string; confirmPassword?: string }>({});

    useEffect(() => {
        setErrors(validatePasswordChangeForm(oldPassword, newPassword, confirmPassword));
    }, [oldPassword, newPassword, confirmPassword]);

    const handleSubmit = async (event: React.FormEvent) => {
        event.preventDefault();
        const formErrors = validatePasswordChangeForm(oldPassword, newPassword, confirmPassword);
        if (Object.keys(formErrors).length > 0) {
            setErrors(formErrors);
            return;
        }
        const result: any = await changePassword(oldPassword, newPassword);
        if (result.success) {
            setOldPassword("");
            setNewPassword("");
            setConfirmPassword("");
        }
    };

    return (
        <FormWrapperStyle>
            <Typography variant="h5" gutterBottom>
                Změna hesla
            </Typography>
            <form onSubmit={handleSubmit}>
                <TextField
                    label="Staré heslo"
                    type="password"
                    variant="outlined"
                    margin="normal"
                    fullWidth
                    value={oldPassword}
                    onChange={(e) => setOldPassword(e.target.value)}
                    error={Boolean(errors.oldPassword)}
                    helperText={errors.oldPassword}
                />
                <TextField
                    label="Nové heslo"
                    type="password"
                    variant="outlined"
                    margin="normal"
                    fullWidth
                    value={newPassword}
                    onChange={(e) => setNewPassword(e.target.value)}
                    error={Boolean(errors.newPassword)}
                    helperText={errors.newPassword}
                />
                <TextField
                    label="Potvrzení nového hesla"
                    type="password"
                    variant="outlined"
                    margin="normal"
                    fullWidth
                    value={confirmPassword}
                    onChange={(e) => setConfirmPassword(e.target.value)}
                    error={Boolean(errors.confirmPassword)}
                    helperText={errors.confirmPassword}
                />
                <Button
                    type="submit"
                    variant="contained"
                    fullWidth
                    style={{ margin: '20px 0' }}
                    disabled={errors.missing || Boolean(errors.oldPassword) || Boolean(errors.newPassword) || Boolean(errors.confirmPassword)}
                >
                    Změnit heslo
                </Button>
            </form>
        </FormWrapperStyle>
    );
};

export default ChangePasswordForm;
