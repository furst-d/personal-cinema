import axios from 'axios';

const axiosPublic = axios.create({
    baseURL: import.meta.env.VITE_API_URL
});

export default axiosPublic;