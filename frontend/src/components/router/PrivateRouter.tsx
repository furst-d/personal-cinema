import React from "react";
import {Route, Routes} from "react-router-dom";
import HomePage from "../pages/HomePage";
import VideoPage from "../pages/VideoPage";
import DiscPage from "../pages/DiscPage";
import ProfilePage from "../pages/ProfilePage";
import SettingsPage from "../pages/SettingsPage";
import NotFoundPage from "../pages/NotFoundPage";
import Navbar from "../navbar/Navbar";
import {ContainerStyle, ContentStyle, ContentWrapperStyle} from "../../styles/layout/Application";

const PrivateRouter: React.FC = () => (
    <>
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
    </>
);

export default PrivateRouter;