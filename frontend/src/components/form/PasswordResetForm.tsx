import React, { useState, useEffect } from "react";
import { useNavigate, useSearchParams } from "react-router-dom";
import { CenteredContainerStyle } from "../../styles/layout/Application";
import { CenterFormWrapperStyle, StyledLink } from "../../styles/form/Form";
import { resetPasswordWithToken } from "../../service/authService";
import { Typography, TextField, Button } from "@mui/material";
import { validatePasswordResetForm } from "../../utils/validator";
import { toast } from "react-toastify";

const PasswordResetForm: React.FC = () => {
    const navigate = useNavigate();
    const [searchParams] = useSearchParams();
    const token = searchParams.get("token");
    const [password, setPassword] = useState("");
    const [confirmPassword, setConfirmPassword] = useState("");
    const [errors, setErrors] = useState<{ missing?: boolean; password?: string; confirmPassword?: string }>({});
    const [errorMessage, setErrorMessage] = useState("");

    useEffect(() => {
        if (!token) {
            setErrorMessage("Při pokusu o obnovení hesla došlo k chybě. Parametry jsou nesprávné.");
        }
    }, [token]);

    useEffect(() => {
        setErrors(validatePasswordResetForm(password, confirmPassword));
    }, [password, confirmPassword]);

    const handleResetPassword = async (event: React.FormEvent) => {
        event.preventDefault();
        setErrorMessage("");
        const formErrors = validatePasswordResetForm(password, confirmPassword);
        if (Object.keys(formErrors).length > 0) {
            setErrors(formErrors);
            return;
        }
        const response = await resetPasswordWithToken(token!, password);
        if (response.success) {
            toast.success(response.message);
            navigate("/login");
        } else {
            setErrorMessage(response.message);
        }
    };

    return (
        <CenteredContainerStyle>
            <CenterFormWrapperStyle>
                <Typography variant="h5" gutterBottom>
                    Obnovení hesla
                </Typography>
                {!token ? (
                    <>
                        <Typography variant="body1" gutterBottom>
                            {errorMessage}
                        </Typography>
                        <Button
                            variant="contained"
                            onClick={() => navigate("/login")}
                            fullWidth
                            style={{ marginTop: '20px' }}
                        >
                            Zpět na přihlášení
                        </Button>
                    </>
                ) : (
                    <>
                        <form onSubmit={handleResetPassword}>
                            <TextField
                                label="Nové heslo"
                                type="password"
                                variant="outlined"
                                margin="normal"
                                fullWidth
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                error={Boolean(errors.password)}
                                helperText={errors.password}
                            />
                            <TextField
                                label="Potvrzení hesla"
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
                                disabled={errors.missing || Boolean(errors.password) || Boolean(errors.confirmPassword)}
                            >
                                Obnovit heslo
                            </Button>
                        </form>
                        {errorMessage && <Typography color="error" sx={{ marginBottom: '15px' }}>{errorMessage}</Typography>}
                        <StyledLink variant="body2" onClick={() => navigate("/login")}>
                            Zpět na přihlášení
                        </StyledLink>
                    </>
                )}
            </CenterFormWrapperStyle>
        </CenteredContainerStyle>
    );
};

export default PasswordResetForm;
