import React from "react";
import {Helmet, HelmetProvider} from "react-helmet-async";

const VideoPage: React.FC = () => {
    return (
        <HelmetProvider>
            <Helmet>
                <title>Správa videí</title>
            </Helmet>
            <h1>Správa videí</h1>
        </HelmetProvider>
    );
}

export default VideoPage;