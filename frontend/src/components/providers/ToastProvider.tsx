import React, {useEffect} from "react";
import {toast} from "react-toastify";
import {ProviderProps} from "../../types/Layout";

export const ToastProvider: React.FC<ProviderProps> = ({ children }) => {
    useEffect(() => {
        const toastSuccessMessage = localStorage.getItem("toast-success");
        if (toastSuccessMessage) {
            toast.success(toastSuccessMessage, {
                onClose: () => localStorage.removeItem("toast-success")
            });
        }

        const toastErrorMessage = localStorage.getItem("toast-error");
        if (toastErrorMessage) {
            toast.error(toastErrorMessage, {
                onClose: () => localStorage.removeItem("toast-error")
            });
        }
    }, []);

    return (
        <>
            {children}
        </>
    )
}