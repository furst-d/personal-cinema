import React from 'react';
import ThemeProvider from "./providers/ThemeProvider";
import { ToastProvider } from "./providers/ToastProvider";
import { ApplicationStyle } from "../styles/layout/Application";
import { BrowserRouter } from "react-router-dom";
import { AuthProvider } from "./providers/AuthProvider";
import RouterProvider from "./providers/RouterProvider";

const App: React.FC = () => {
    return (
        <ThemeProvider>
            <ToastProvider>
                <BrowserRouter>
                    <AuthProvider>
                        <ApplicationStyle>
                            <RouterProvider />
                        </ApplicationStyle>
                    </AuthProvider>
                </BrowserRouter>
            </ToastProvider>
        </ThemeProvider>
    );
};

export default App;
