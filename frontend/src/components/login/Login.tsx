import React from "react";
import LoginForm from "../form/LoginForm";
import ProjectInfo from "./ProjectInfo";
import Logo from '/public/images/logo.svg?react';
import {
    InfoBoxStyle,
    LoginBoxStyle,
    LoginBoxWrapperStyle,
    LoginContainerStyle,
    LogoWrapperStyle
} from "../../styles/login/Login";

const Login: React.FC = () => {
    return (
        <LoginContainerStyle>
            <LoginBoxWrapperStyle>
                <LoginBoxStyle className="login-box">
                    <LoginForm />
                </LoginBoxStyle>
                <InfoBoxStyle className="info-box">
                    <ProjectInfo />
                    <LogoWrapperStyle>
                        <Logo height="130px" />
                    </LogoWrapperStyle>
                </InfoBoxStyle>
            </LoginBoxWrapperStyle>
        </LoginContainerStyle>
    );
}

export default Login;
