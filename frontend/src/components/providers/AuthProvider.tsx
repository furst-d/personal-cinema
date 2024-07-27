import React, { createContext, useContext, useEffect, useState } from "react";
import { AuthContextType, User } from "../../types/auth";
import { useNavigate } from "react-router-dom";
import { toast } from "react-toastify";
import Loading from "../loading/Loading";

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC = ({ children }) => {
    const [isAuthenticated, setIsAuthenticated] = useState<boolean>(false);
    const [user, setUser] = useState<User | null>(null);
    const [loading, setLoading] = useState<boolean>(true);
    const navigate = useNavigate();

    useEffect(() => {
        setIsAuthenticated(false);
        const userData = localStorage.getItem("user_data");
        if (userData) {
            const parsedUserData = JSON.parse(userData);
            setIsAuthenticated(true);
            setUser(parsedUserData.user);
            if (!parsedUserData.user.isActive && window.location.pathname !== "/activate") {
                navigate("/account-not-activated");
            }
        }
        setLoading(false);
    }, [navigate]);

    const login = (userData: any) => {
        setIsAuthenticated(false);
        setLoading(true);
        localStorage.setItem("user_data", JSON.stringify(userData));
        setIsAuthenticated(true);
        setUser(userData.user);
        if (!userData.user.isActive) {
            navigate("/account-not-activated");
        } else {
            navigate("/");
            toast.success("Úspěšně přihlášen");
        }
    };

    const logout = () => {
        localStorage.removeItem("user_data");
        setIsAuthenticated(false);
        setUser(null);
        navigate("/login");
        toast.info("Úspěšně odhlášen");
    };

    if (loading) {
        return <Loading />;
    }

    return (
        <AuthContext.Provider value={{ isAuthenticated, user, loading, login, logout }}>
            {children}
        </AuthContext.Provider>
    );
};

export const useAuth = (): AuthContextType => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error("useAuth must be used within an AuthProvider");
    }
    return context;
};
