import axios from 'axios';

const axiosCdn = axios.create({
    baseURL: import.meta.env.VITE_API_URL
});

export default axiosCdn;