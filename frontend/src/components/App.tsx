import React from 'react';
import ThemeProvider from "./styles/provider/ThemeProvider";
import {ToastProvider} from "./toast/ToastProvider";
import {ApplicationStyle, ContainerStyle, ContentStyle, ContentWrapperStyle} from "../styles/layout/Application";
import Navbar from "./navbar/Navbar";
import {BrowserRouter, Route, Routes} from "react-router-dom";
import VideoPage from "./pages/VideoPage";
import HomePage from "./pages/HomePage";
import DiscPage from "./pages/DiscPage";
import ProfilePage from "./pages/ProfilePage";
import SettingsPage from "./pages/SettingsPage";
import NotFoundPage from "./pages/NotFoundPage";

const App: React.FC = () => {
    return (
        <ThemeProvider>
            <ToastProvider>
                <BrowserRouter>
                    <ApplicationStyle>
                        <Navbar />
                        <ContainerStyle>
                            <ContentWrapperStyle>
                                <ContentStyle>
                                    <Routes>
                                        <Route path="/" element={<HomePage />} />
                                        <Route path="/videos" element={<VideoPage />} />
                                        <Route path="/disc" element={<DiscPage />} />
                                        <Route path="/profile" element={<ProfilePage />} />
                                        <Route path="/settings" element={<SettingsPage />} />
                                        <Route path="*" element={<NotFoundPage />} />
                                    </Routes>
                                </ContentStyle>
                            </ContentWrapperStyle>
                        </ContainerStyle>
                    </ApplicationStyle>
                </BrowserRouter>
            </ToastProvider>
        </ThemeProvider>
    );
}

export default App;