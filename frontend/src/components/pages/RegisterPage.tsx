import React from "react";
import { Helmet, HelmetProvider } from "react-helmet-async";
import RegisterForm from "../form/RegisterForm";

const RegisterPage: React.FC = () => {
    return (
        <HelmetProvider>
            <Helmet>
                <title>Registrace</title>
            </Helmet>
            <RegisterForm />
        </HelmetProvider>
    );
}

export default RegisterPage;
