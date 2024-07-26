export interface User {
    id: number;
    email: string;
    createdAt: string;
    isActive: boolean;
    roles: {
        key: string;
        name: string;
    }[];
}

export interface AuthContextType {
    isAuthenticated: boolean;
    user: User | null;
    login: (userData: any) => void;
    logout: () => void;
}