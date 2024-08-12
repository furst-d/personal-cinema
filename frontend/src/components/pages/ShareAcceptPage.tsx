import React from "react";
import { Helmet, HelmetProvider } from "react-helmet-async";
import ShareAccept from "../share/ShareAccept";

const ForgottenPasswordPage: React.FC = () => {
    return (
        <HelmetProvider>
            <Helmet>
                <title>Příjem položky</title>
            </Helmet>
            <ShareAccept />
        </HelmetProvider>
    );
}

export default ForgottenPasswordPage;
