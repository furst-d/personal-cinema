import React, { useState } from "react";
import { Typography, Container, Box } from "@mui/material";
import VideoSearch from "./VideoSearch";
import MediaDashboard from "./MediaDashboard";
import {SearchTextFieldStyle} from "../../styles/form/Form";

const VideoList: React.FC = () => {
    const [searchPhrase, setSearchPhrase] = useState<string>("");

    const handleSearchChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        setSearchPhrase(event.target.value);
    };

    return (
        <Container>
            <Typography variant="h4" gutterBottom>
                Vaše videa
            </Typography>
            <Box mb={3}>
                <SearchTextFieldStyle
                    variant="outlined"
                    placeholder="Vyhledat obsah..."
                    value={searchPhrase}
                    onChange={handleSearchChange}
                />
            </Box>
            {searchPhrase ? (
                <VideoSearch phrase={searchPhrase} />
            ) : (
                <MediaDashboard />
            )}
        </Container>
    );
};

export default VideoList;
