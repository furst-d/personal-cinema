import React from "react";
import {Helmet, HelmetProvider} from "react-helmet-async";

const ProfilePage: React.FC = () => {
    return (
        <HelmetProvider>
            <Helmet>
                <title>Profil</title>
            </Helmet>
            <h1>Profil</h1>
        </HelmetProvider>
    );
}

export default ProfilePage;