import React from "react";
import { Route, Routes } from "react-router-dom";
import HomePage from "../pages/HomePage";
import VideoPage from "../pages/VideoPage";
import DiscPage from "../pages/DiscPage";
import ProfilePage from "../pages/ProfilePage";
import SettingsPage from "../pages/SettingsPage";
import NotFoundPage from "../pages/NotFoundPage";
import Navbar from "../navbar/Navbar";
import { ContainerStyle, ContentStyle, ContentWrapperStyle } from "../../styles/layout/Application";
import VideoDetailPage from "../pages/VideoDetailPage";
import ShareAcceptPage from "../pages/ShareAcceptPage";

const PrivateRouter: React.FC = () => {
    return (
        <>
            <Routes>
                <Route path="/share-accept" element={<ShareAcceptPage />} />
                <Route
                    path="/*"
                    element={
                        <>
                            <Navbar />
                            <ContainerStyle>
                                <ContentWrapperStyle>
                                    <ContentStyle>
                                        <Routes>
                                            <Route path="/" element={<HomePage />} />
                                            <Route path="/videos-management" element={<VideoPage />} />
                                            <Route path="/videos/:hash" element={<VideoDetailPage />} />
                                            <Route path="/disc" element={<DiscPage />} />
                                            <Route path="/profile" element={<ProfilePage />} />
                                            <Route path="/settings" element={<SettingsPage />} />
                                            <Route path="*" element={<NotFoundPage />} />
                                        </Routes>
                                    </ContentStyle>
                                </ContentWrapperStyle>
                            </ContainerStyle>
                        </>
                    }
                />
            </Routes>
        </>
    );
};

export default PrivateRouter;
