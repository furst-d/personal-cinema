import React from "react";
import {Helmet, HelmetProvider} from "react-helmet-async";

const HomePage: React.FC = () => {
    return (
        <HelmetProvider>
            <Helmet>
                <title>Vaše videa</title>
            </Helmet>
            <h1>Vaše videa</h1>
        </HelmetProvider>
    );
}

export default HomePage;