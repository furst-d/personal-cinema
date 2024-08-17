import React from "react";
import {Box, Container, Typography} from "@mui/material";
import ProfileStats from "./ProfileStats";
import DeleteProfile from "./DeleteProfile";

const ProfileInfo: React.FC = () => {
    return (
        <Container>
            <Typography variant="h4" gutterBottom>
                Profil
            </Typography>
            <ProfileStats />
            <Box mt={5}>
                <DeleteProfile />
            </Box>

        </Container>
    )
}

export default ProfileInfo;