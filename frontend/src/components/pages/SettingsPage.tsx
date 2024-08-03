import React from "react";
import {Helmet, HelmetProvider} from "react-helmet-async";
import ChangePasswordForm from "../form/ChangePasswordForm";
import Setting from "../setting/Setting";

const SettingsPage: React.FC = () => {
    return (
        <HelmetProvider>
            <Helmet>
                <title>Nastavení</title>
            </Helmet>
            <Setting />
        </HelmetProvider>
    );
}

export default SettingsPage;