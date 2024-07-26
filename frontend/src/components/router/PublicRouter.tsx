import React from "react";
import {Navigate, Route, Routes} from "react-router-dom";
import LoginPage from "../pages/LoginPage";
import RegisterPage from "../pages/RegisterPage";
import ForgottenPasswordPage from "../pages/ForgottenPasswordPage";

const PublicRouter: React.FC = () => (
    <Routes>
        <Route path="/login" element={<LoginPage />} />
        <Route path="/register" element={<RegisterPage />} />
        <Route path="/forgotten-password" element={<ForgottenPasswordPage />} />
        <Route path="*" element={<Navigate to="/login" />} />
    </Routes>
);

export default PublicRouter;

