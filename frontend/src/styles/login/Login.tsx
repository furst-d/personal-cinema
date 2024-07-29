import {styled} from "styled-components";

export const LoginContainerStyle = styled.div`
    display: flex;
    justify-content: center;
    align-items: flex-start;
    height: auto;
    background-size: cover;
    background-position: center;

    @media (min-width: 769px) {
        background-image: url("/images/background.png");
        align-items: center;
        height: 100vh;
    }
`;

export const LoginBoxWrapperStyle = styled.div`
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    width: 100%;
    max-width: 900px;
    gap: 20px;

    @media (min-width: 769px) {
        flex-direction: row;
        justify-content: center;
        align-items: stretch;
        gap: 0;
    }
`;

export const LoginBoxStyle = styled.div`
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100%;
    width: 100%;
    box-sizing: border-box;

    @media (min-width: 769px) {
        margin-right: 20px;
        min-width: 300px;
        max-width: 400px;

        div {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
    }
`;

export const InfoBoxStyle = styled(LoginBoxStyle)`
    background-color: ${(props) => props.theme.primary};
    color: ${(props) => props.theme.text_light};
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    padding: 20px;
    position: relative;
    justify-content: flex-start;
    align-items: flex-start;
    height: auto;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;

    @media (min-width: 769px) {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-top-right-radius: 10px;
        min-width: 250px;
        max-width: 250px;
    }
`;

export const LogoWrapperStyle = styled.div`
    position: absolute;
    bottom: 20px;
    right: 20px;
    opacity: 0.1;
`;