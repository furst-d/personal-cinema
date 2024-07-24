import React from 'react';
import ThemeProvider from "./styles/provider/ThemeProvider";
import {ToastProvider} from "./toast/ToastProvider";
import {ApplicationStyle, ContainerStyle, ContentStyle, ContentWrapperStyle} from "../styles/layout/Application";
import Navbar from "./navbar/Navbar";

const App: React.FC = () => {
    return (
        <ThemeProvider>
            <ToastProvider>
                <ApplicationStyle>
                    <Navbar />
                    <ContainerStyle>
                        <ContentWrapperStyle>
                            <ContentStyle>
                                Test
                            </ContentStyle>
                        </ContentWrapperStyle>
                    </ContainerStyle>
                </ApplicationStyle>
            </ToastProvider>
        </ThemeProvider>
    );
}

export default App;

