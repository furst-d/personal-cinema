import React from "react";
import { Helmet, HelmetProvider } from "react-helmet-async";
import LoginForm from "../form/LoginForm";

const LoginPage: React.FC = () => {
    return (
        <HelmetProvider>
            <Helmet>
                <title>Přihlásit se</title>
            </Helmet>
            <LoginForm />
        </HelmetProvider>
    );
}

export default LoginPage;
