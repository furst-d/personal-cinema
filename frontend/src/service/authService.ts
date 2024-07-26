import axiosPublic from '../api/axiosPublic';
import { setToken } from './tokenService';
import {toast} from "react-toastify";

export const login = async (email: string, password: string) => {
    try {
        const response = await axiosPublic.post('/v1/users/login', {
            email,
            password,
        });

        if (response.status === 200) {
            const userData = response.data.payload.data;
            localStorage.setItem('user_data', JSON.stringify(userData));
            setToken('access_token', userData.tokens.access_token);
            setToken('refresh_token', userData.tokens.refresh_token);
            return { success: true, data: userData };
        }
    } catch (error) {
        if (error.response) {
            const status = error.response.status;
            if (status === 400) {
                return { success: false, message: 'Chybný email nebo heslo.' };
            } else if (status === 401) {
                return { success: false, message: 'Uživatel nemá povolení pro vstup.' };
            }
        }
        return { success: false, message: 'Při zpracování došlo k chybě.' };
    }
};

export const resendActivationEmail = async (email: string) => {
    try {
        const response = await axiosPublic.post('/v1/users/activate/send', { email });
        if (response.status === 200) {
            toast.success("Aktivační email byl znovu odeslán");
        }
    } catch (error) {
        toast.error("Při odesílání došlo k chybě");
    }
};
