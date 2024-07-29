export const validateEmail = (email: string) => {
    if (!email) {
        return true;
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(email);
};

export const validatePassword = (password: string) => {
    if (!password) {
        return true;
    }

    return password.length >= 6;
};

export const validateLoginForm = (email: string, password: string) => {
    const errors: { missing: boolean, email?: string; password?: string } = {};

    if (!email || !password) {
        errors.missing = true;
    }

    if (!validateEmail(email)) {
        errors.email = "Neplatný formát emailu";
    }

    if (!validatePassword(password)) {
        errors.password = "Heslo musí mít alespoň 6 znaků";
    }

    return errors;
};

export const validateForgottenPasswordForm = (email: string) => {
    const errors: { missing: boolean, email?: string } = {};

    if (!email) {
        errors.missing = true;
    }

    if (!validateEmail(email)) {
        errors.email = "Neplatný formát emailu";
    }

    return errors;
};

export const validateRegisterForm = (email: string, password: string, confirmPassword: string) => {
    const errors: { missing: boolean, email?: string; password?: string; confirmPassword?: string } = {};

    if (!email || !password || !confirmPassword) {
        errors.missing = true;
    }

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
    const errors: { missing: boolean, password?: string; confirmPassword?: string } = {};

    if (!password || !confirmPassword) {
        errors.missing = true;
    }

    if (password.length < 6) {
        errors.password = "Heslo musí mít alespoň 6 znaků";
    }

    if (password !== confirmPassword) {
        errors.confirmPassword = "Hesla se neshodují";
    }

    return errors;
};