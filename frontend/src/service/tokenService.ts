export const getToken = (type: 'accessToken' | 'refreshToken') => {
    const userData = localStorage.getItem('user_data');
    if (!userData) return null;
    const parsedUserData = JSON.parse(userData);
    return parsedUserData.tokens[type];
};

export const setToken = (type: 'accessToken' | 'refreshToken', token: string) => {
    const userData = localStorage.getItem('user_data');
    if (!userData) return;
    const parsedUserData = JSON.parse(userData);
    parsedUserData.tokens[type] = token;
    localStorage.setItem('user_data', JSON.stringify(parsedUserData));
};

export const removeTokens = () => {
    const userData = localStorage.getItem('user_data');
    if (!userData) return;
    const parsedUserData = JSON.parse(userData);
    delete parsedUserData.tokens.accessToken;
    delete parsedUserData.tokens.refreshToken;
    localStorage.setItem('user_data', JSON.stringify(parsedUserData));
};
