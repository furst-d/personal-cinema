import React from "react";
import {Helmet, HelmetProvider} from "react-helmet-async";
import Storage from "../storage/Storage";

const StoragePage: React.FC = () => {
    return (
        <HelmetProvider>
            <Helmet>
                <title>Správa úložiště</title>
            </Helmet>
            <Storage />
        </HelmetProvider>
    );
}

export default StoragePage;