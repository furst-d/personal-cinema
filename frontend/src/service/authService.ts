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

export const register = async (email: string, password: string) => {
    try {
        const response = await axiosPublic.post('/v1/users/register', { email, password });
        if (response.status === 201) {
            return { success: true, message: "Registrace byla úspěšná. Zkontrolujte svůj email pro potvrzení účtu." };
        }
    } catch (error: any) {
        if (error.response && error.response.status === 409) {
            return { success: false, message: "Účet s tímto emailem již existuje" };
        } else {
            return { success: false, message: "Při zpracování došlo k chybě" };
        }
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

export const resetPassword = async (email: string) => {
    try {
        const response = await axiosPublic.post('/v1/users/password-reset', { email });
        if (response.status !== 500) {
            toast.success("Žádost o obnovení hesla byla odeslána");
        }
    } catch (error) {
        if (error.response && error.response.status === 500) {
            toast.error("Při odesílání žádosti došlo k chybě");
        } else {
            toast.success("Žádost o obnovení hesla byla odeslána");
        }
    }
};

export const activateAccount = async (token: string) => {
    try {
        const response = await axiosPublic.post('/v1/users/activate', { token });
        if (response.status === 200) {
            return { success: true, message: "Účet byl úspěšně aktivován." };
        } else {
            return { success: false, message: "Při aktivaci účtu došlo k chybě." };
        }
    } catch (error: any) {
        return { success: false, message: "Při aktivaci účtu došlo k chybě." };
    }
};

export const resetPasswordWithToken = async (token: string, password: string) => {
    try {
        const response = await axiosPublic.post('/v1/users/password-reset', { token, password });
        if (response.status === 200) {
            return { success: true, message: "Heslo bylo úspěšně obnoveno." };
        } else {
            return { success: false, message: "Při obnovování hesla došlo k chybě." };
        }
    } catch (error: any) {
        return { success: false, message: "Při obnovování hesla došlo k chybě." };
    }
};