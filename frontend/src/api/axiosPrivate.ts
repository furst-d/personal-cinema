import axios from 'axios';
import { getToken, setToken, removeTokens } from '../service/tokenService';
import { logoutUser } from '../service/authService';

const axiosPrivate = axios.create({
    baseURL: import.meta.env.VITE_API_URL
});

axiosPrivate.interceptors.request.use(
    (config) => {
        const token = getToken('accessToken');
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
            const refreshToken = getToken('refreshToken');
            if (!refreshToken) {
                logoutUser();
                return Promise.reject(error);
            }
            try {
                const response = await axios.post(`${import.meta.env.VITE_API_URL}/v1/users/refresh-token`, { token: refreshToken });
                if (response.status === 200) {
                    const newToken = response.data.payload.data.tokens.accessToken;
                    setToken('accessToken', newToken);
                    originalRequest.headers['Authorization'] = `Bearer ${newToken}`;
                    return axiosPrivate(originalRequest);
                }
            } catch (refreshError) {
                removeTokens();
                logoutUser();
                return Promise.reject(refreshError);
            }
        }
        return Promise.reject(error);
    }
);

export default axiosPrivate;
