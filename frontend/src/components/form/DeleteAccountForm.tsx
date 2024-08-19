import React, { useState, useEffect } from "react";
import {FormWrapperStyle} from "../../styles/form/Form";
import { Typography, TextField, Button } from "@mui/material";
import { toast } from "react-toastify";
import {validateDeleteAccountForm} from "../../utils/validator";
import {deleteAccount} from "../../service/authService";
import {useAuth} from "../providers/AuthProvider";

const DeleteAccountForm: React.FC = () => {
    const { logout } = useAuth();

    const [password, setPassword] = useState("");
    const [errors, setErrors] = useState<{ missing?: boolean; password?: string }>({});
    const [errorMessage, setErrorMessage] = useState("");

    useEffect(() => {
        setErrors(validateDeleteAccountForm(password));
    }, [password]);

    const handleDeleteAccount = async (event: React.FormEvent) => {
        event.preventDefault();
        setErrorMessage("");
        const newErrors = validateDeleteAccountForm(password);
        if (Object.keys(newErrors).length > 0) {
            setErrors(newErrors);
            return;
        }

        const result: any = await deleteAccount(password);
        if (result.success) {
            toast.success(result.message);
            logout();
        } else {
            setErrorMessage(result.message);
        }
    };

    return (
        <FormWrapperStyle>
            <form onSubmit={handleDeleteAccount}>
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
                    autoComplete="new-password"
                />
                <Button
                    type="submit"
                    variant="contained"
                    fullWidth
                    style={{ margin: '20px 0' }}
                    disabled={errors.missing || Boolean(errors.password)}
                >
                    Smazat účet
                </Button>
            </form>
            {errorMessage && <Typography color="error" sx={{ marginBottom: '15px' }}>{errorMessage}</Typography>}
        </FormWrapperStyle>
    );
};

export default DeleteAccountForm;
