import React from "react";
import {Helmet, HelmetProvider} from "react-helmet-async";
import styled, {useTheme} from "styled-components";
import SentimentDissatisfiedIcon from '@mui/icons-material/SentimentDissatisfied';

const DiscPage: React.FC = () => {
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

export default DiscPage;

const ContentWrapperStyle = styled.div`
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
`;

const NotFoundHeaderStyle = styled.h1`
    font-size: 3rem;
    margin-top: 2rem;
    text-align: center;
`;

const NotFoundMessageStyle = styled.h2`
    font-size: 1.5rem;
    margin-top: 2rem;
    text-align: center;
`;