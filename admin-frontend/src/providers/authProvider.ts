import axios from 'axios';
import { AuthProvider } from 'react-admin';

const apiUrl = import.meta.env.VITE_API_URL;

const authProvider: AuthProvider = {
    login: async ({ username, password }) => {
        const response = await axios.post(`${apiUrl}/v1/users/login/admin`, {
            email: username,
            password: password,
        });

        const { tokens, user } = response.data.payload.data;

        if (!user.roles.some((role: any) => role.key === 'ROLE_ADMIN')) {
            throw new Error('Nemáte dostatečná oprávnění pro přístup k administraci.');
        }

        localStorage.setItem('accessToken', tokens.accessToken);
        localStorage.setItem('refreshToken', tokens.refreshToken);

        return Promise.resolve();
    },
    logout: () => {
        localStorage.removeItem('accessToken');
        localStorage.removeItem('refreshToken');
        return Promise.resolve();
    },
    checkError: (error) => {
        const status = error.response ? error.response.status : null;
        if (status === 401 || status === 403) {
            localStorage.removeItem('accessToken');
            localStorage.removeItem('refreshToken');
            return Promise.reject();
        }
        return Promise.resolve();
    },
    checkAuth: () => {
        return localStorage.getItem('accessToken') ? Promise.resolve() : Promise.reject();
    },
    getPermissions: () => Promise.resolve(),
};

export const refreshAuthToken = async () => {
    const refreshToken = localStorage.getItem('refreshToken');
    if (!refreshToken) {
        return Promise.reject(new Error('No refresh token available'));
    }

    const response = await axios.post(`${apiUrl}/v1/users/refresh-token`, {
        token: refreshToken,
    });

    const { accessToken } = response.data.payload.data.tokens;

    localStorage.setItem('accessToken', accessToken);

    return accessToken;
};

export const fetchJsonWithAuth = async (url: string, options: any = {}) => {
    let accessToken = localStorage.getItem('accessToken');
    options.headers = {
        'Content-Type': 'application/json',
        ...options.headers,
        Authorization: `Bearer ${accessToken}`,
    };

    try {
        return await axios(url, options);
    } catch (error: any) {
        const status = error.response ? error.response.status : null;
        if (status === 401 || status === 403) {
            try {
                accessToken = await refreshAuthToken();
                options.headers.Authorization = `Bearer ${accessToken}`;
                return await axios(url, options);
            } catch (refreshError) {
                console.error("Error refreshing token:", refreshError);
                throw error;
            }
        }
        console.error("Fetch error:", error);
        throw error;
    }
};

export default authProvider;
