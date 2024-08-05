import React from "react";
import {Helmet, HelmetProvider} from "react-helmet-async";
import FileManager from "../file/FileManager";

const VideoPage: React.FC = () => {
    return (
        <HelmetProvider>
            <Helmet>
                <title>Správa videí</title>
            </Helmet>
            <FileManager />
        </HelmetProvider>
    );
}

export default VideoPage;