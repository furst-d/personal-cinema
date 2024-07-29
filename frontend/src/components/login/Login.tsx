import React from "react";
import LoginForm from "../form/LoginForm";
import ProjectInfo from "./ProjectInfo";
import { styled } from "styled-components";
import Logo from '/public/images/logo.svg?react';

const Login: React.FC = () => {
    return (
        <CenteredLoginContainer>
            <LoginBoxWrapper>
                <StyledLoginBox className="login-box">
                    <LoginForm />
                </StyledLoginBox>
                <StyledInfoBox className="info-box">
                    <ProjectInfo />
                    <LogoWrapper>
                        <Logo height="130px" />
                    </LogoWrapper>
                </StyledInfoBox>
            </LoginBoxWrapper>
        </CenteredLoginContainer>
    );
}

export default Login;

const CenteredLoginContainer = styled.div`
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

const LoginBoxWrapper = styled.div`
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

const StyledLoginBox = styled.div`
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

const StyledInfoBox = styled(StyledLoginBox)`
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

const LogoWrapper = styled.div`
    position: absolute;
    bottom: 20px;
    right: 20px;
    opacity: 0.1;
`;
