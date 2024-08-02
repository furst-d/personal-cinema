import React from "react";
import {Helmet, HelmetProvider} from "react-helmet-async";
import {useTheme} from "styled-components";
import SentimentDissatisfiedIcon from '@mui/icons-material/SentimentDissatisfied';
import {ContentWrapperStyle, NotFoundHeaderStyle, NotFoundMessageStyle} from "../../styles/layout/NotFound";

const NotFoundPage: React.FC = () => {
    const theme = useTheme();

    return (
        <HelmetProvider>
            <Helmet>
                <title>Stránka nenalezena</title>
            </Helmet>
            <ContentWrapperStyle>
                <SentimentDissatisfiedIcon
                    style={{
                    fontSize: '9rem',
                    color: theme.primary,
                }} />
                <NotFoundHeaderStyle>Stránka nenalezena!</NotFoundHeaderStyle>
                <NotFoundMessageStyle>
                    Je nám líto, ale stránka, kterou hledáte, nebyla nalezena.
                </NotFoundMessageStyle>
            </ContentWrapperStyle>
        </HelmetProvider>
    );
}

export default NotFoundPage;