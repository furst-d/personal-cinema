import axios from 'axios';

const axiosPublic = axios.create({
    baseURL: import.meta.env.VITE_CDN_URL
});

export default axiosPublic;