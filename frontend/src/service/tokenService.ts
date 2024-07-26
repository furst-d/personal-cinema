export const getToken = (type: 'access_token' | 'refresh_token') => {
    const userData = localStorage.getItem('user_data');
    if (!userData) return null;
    const parsedUserData = JSON.parse(userData);
    return parsedUserData.tokens[type];
};

export const setToken = (type: 'access_token' | 'refresh_token', token: string) => {
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
    delete parsedUserData.tokens.access_token;
    delete parsedUserData.tokens.refresh_token;
    localStorage.setItem('user_data', JSON.stringify(parsedUserData));
};
