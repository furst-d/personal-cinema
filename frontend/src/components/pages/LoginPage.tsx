import React from "react";
import { Helmet, HelmetProvider } from "react-helmet-async";
import Login from "../login/Login";

const LoginPage: React.FC = () => {
    return (
        <HelmetProvider>
            <Helmet>
                <title>Přihlásit se</title>
            </Helmet>
            <Login />
        </HelmetProvider>
    );
}

export default LoginPage;
