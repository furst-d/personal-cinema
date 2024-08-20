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

        localStorage.setItem('access_token', tokens.access_token);
        localStorage.setItem('refresh_token', tokens.refresh_token);

        return Promise.resolve();
    },
    logout: () => {
        localStorage.removeItem('access_token');
        localStorage.removeItem('refresh_token');
        return Promise.resolve();
    },
    checkError: (error) => {
        const status = error.response ? error.response.status : null;
        if (status === 401 || status === 403) {
            localStorage.removeItem('access_token');
            localStorage.removeItem('refresh_token');
            return Promise.reject();
        }
        return Promise.resolve();
    },
    checkAuth: () => {
        return localStorage.getItem('access_token') ? Promise.resolve() : Promise.reject();
    },
    getPermissions: () => Promise.resolve(),
};

export const refreshAuthToken = async () => {
    const refreshToken = localStorage.getItem('refresh_token');
    if (!refreshToken) {
        return Promise.reject(new Error('No refresh token available'));
    }

    const response = await axios.post(`${apiUrl}/v1/users/refresh-token`, {
        token: refreshToken,
    });

    const { access_token } = response.data.payload.data.tokens;

    localStorage.setItem('access_token', access_token);

    return access_token;
};

export const fetchJsonWithAuth = async (url: string, options: any = {}) => {
    let accessToken = localStorage.getItem('access_token');
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
