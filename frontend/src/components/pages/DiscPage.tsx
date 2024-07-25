import React from "react";
import {Helmet, HelmetProvider} from "react-helmet-async";

const DiscPage: React.FC = () => {
    return (
        <HelmetProvider>
            <Helmet>
                <title>Správa disku</title>
            </Helmet>
            <h1>Správa disku</h1>
        </HelmetProvider>
    );
}

export default DiscPage;