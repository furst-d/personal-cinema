import React from "react";
import {Container, Typography} from "@mui/material";
import ProfileStats from "./ProfileStats";

const ProfileInfo: React.FC = () => {
    return (
        <Container>
            <Typography variant="h4" gutterBottom>
                Profil
            </Typography>
            <ProfileStats />
        </Container>
    )
}

export default ProfileInfo;