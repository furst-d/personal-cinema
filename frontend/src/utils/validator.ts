export const validateEmail = (email: string) => {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(email);
};

export const validatePassword = (password: string) => {
    return password.length >= 6;
};

export const validateLoginForm = (email: string, password: string) => {
    const errors: { email?: string; password?: string } = {};

    if (!validateEmail(email)) {
        errors.email = "Neplatný formát emailu";
    }

    if (!validatePassword(password)) {
        errors.password = "Heslo musí mít alespoň 6 znaků";
    }

    return errors;
};

export const validateForgottenPasswordForm = (email: string) => {
    const errors: { email?: string } = {};

    if (!validateEmail(email)) {
        errors.email = "Neplatný formát emailu";
    }

    return errors;
};

export const validateRegisterForm = (email: string, password: string, confirmPassword: string) => {
    const errors: { email?: string; password?: string; confirmPassword?: string } = {};

    if (!validateEmail(email)) {
        errors.email = "Neplatný formát emailu";
    }

    if (!validatePassword(password)) {
        errors.password = "Heslo musí mít alespoň 6 znaků";
    }

    if (password !== confirmPassword) {
        errors.confirmPassword = "Hesla se neshodují";
    }

    return errors;
};

export const validatePasswordResetForm = (password: string, confirmPassword: string) => {
    const errors: { password?: string; confirmPassword?: string } = {};

    if (password.length < 6) {
        errors.password = "Heslo musí mít alespoň 6 znaků";
    }

    if (password !== confirmPassword) {
        errors.confirmPassword = "Hesla se neshodují";
    }

    return errors;
};