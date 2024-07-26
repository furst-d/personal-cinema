import React from "react";
import { Helmet, HelmetProvider } from "react-helmet-async";
import ForgottenPasswordForm from "../form/ForgottenPasswordForm";

const ForgottenPasswordPage: React.FC = () => {
    return (
        <HelmetProvider>
            <Helmet>
                <title>Obnova hesla</title>
            </Helmet>
            <ForgottenPasswordForm />
        </HelmetProvider>
    );
}

export default ForgottenPasswordPage;
