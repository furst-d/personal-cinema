import React from "react";
import {Helmet, HelmetProvider} from "react-helmet-async";
import ProfileInfo from "../profile/ProfileInfo";

const ProfilePage: React.FC = () => {
    return (
        <HelmetProvider>
            <Helmet>
                <title>Profil</title>
            </Helmet>
            <ProfileInfo />
        </HelmetProvider>
    );
}

export default ProfilePage;