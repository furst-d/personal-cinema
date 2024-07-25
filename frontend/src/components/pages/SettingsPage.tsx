import React from "react";
import {Helmet, HelmetProvider} from "react-helmet-async";

const SettingsPage: React.FC = () => {
    return (
        <HelmetProvider>
            <Helmet>
                <title>Nastavení</title>
            </Helmet>
            <h1>Nastavení</h1>
        </HelmetProvider>
    );
}

export default SettingsPage;