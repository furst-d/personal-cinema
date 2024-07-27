import React from "react";
import { useAuth } from "./AuthProvider";
import PrivateRouter from "../router/PrivateRouter";
import PublicRouter from "../router/PublicRouter";
import { Route, Routes } from "react-router-dom";
import NotActivatedPage from "../pages/NotActivatedPage";
import ActivateAccountPage from "../pages/ActivateAccountPage";
import Loading from "../loading/Loading";

const RouterProvider: React.FC = () => {
    const { isAuthenticated, user, loading } = useAuth();

    if (loading) {
        return <Loading />;
    }

    return (
        <Routes>
            <Route path="/activate" element={<ActivateAccountPage />} />
            <Route path="/account-not-activated" element={<NotActivatedPage />} />
            <Route path="/*" element={isAuthenticated ? <PrivateRouter /> : <PublicRouter />} />
        </Routes>
    );
};

export default RouterProvider;
