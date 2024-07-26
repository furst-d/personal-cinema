import axios from 'axios';
import { getToken, setToken, removeTokens } from '../service/tokenService';
import { useAuth } from '../components/providers/AuthProvider';

const axiosPrivate = axios.create({
    baseURL: import.meta.env.VITE_API_URL
});

axiosPrivate.interceptors.request.use(
    (config) => {
        const token = getToken('access_token');
        if (token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

axiosPrivate.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;
        if (error.response.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;
            const refreshToken = getToken('refresh_token');
            if (!refreshToken) {
                useAuth().logout();
                return Promise.reject(error);
            }
            try {
                const response = await axios.post(`${process.env.VITE_API_URL}/v1/users/refresh-token`, { token: refreshToken });
                if (response.status === 200) {
                    const newToken = response.data.payload.data.tokens.access_token;
                    setToken('access_token', newToken);
                    originalRequest.headers['Authorization'] = `Bearer ${newToken}`;
                    return axiosPrivate(originalRequest);
                }
            } catch (refreshError) {
                removeTokens();
                useAuth().logout();
                return Promise.reject(refreshError);
            }
        }
        return Promise.reject(error);
    }
);

export default axiosPrivate;
