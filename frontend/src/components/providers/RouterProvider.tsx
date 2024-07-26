import React from "react";
import { useAuth } from "./AuthProvider";
import PrivateRouter from "../router/PrivateRouter";
import PublicRouter from "../router/PublicRouter";
import { Route, Routes, Navigate } from "react-router-dom";
import NotActivatedPage from "../pages/NotActivatedPage";

const RouterProvider: React.FC = () => {
    const { isAuthenticated} = useAuth();

    return (
        <Routes>
            <Route path="/account-not-activated" element={<NotActivatedPage />} />
            <Route path="/*" element={isAuthenticated ? <PrivateRouter /> : <PublicRouter />} />
        </Routes>
    );
};

export default RouterProvider;
