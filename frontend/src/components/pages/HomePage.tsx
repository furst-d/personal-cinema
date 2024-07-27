import React from "react";
import { Helmet, HelmetProvider } from "react-helmet-async";
import VideoList from "../video/VideoList";

const HomePage: React.FC = () => {
    return (
        <HelmetProvider>
            <Helmet>
                <title>Vaše videa</title>
            </Helmet>
            <VideoList />
        </HelmetProvider>
    );
}

export default HomePage;
