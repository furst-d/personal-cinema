import React from "react";
import { Helmet, HelmetProvider } from "react-helmet-async";
import PasswordResetForm from "../form/PasswordResetForm";

const ResetPasswordPage: React.FC = () => {
    return (
        <HelmetProvider>
            <Helmet>
                <title>Obnovení hesla</title>
            </Helmet>
            <PasswordResetForm />
        </HelmetProvider>
    );
}

export default ResetPasswordPage;
