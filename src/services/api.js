import axios from 'axios';
 
const API_URL = process.env.REACT_APP_API_URL || 'http://localhost:5000/api';
 
const api = axios.create({
  baseURL: API_URL,
  headers: { 'Content-Type': 'application/json' },
});
 
api.interceptors.request.use(
  (config) => {
    const user = JSON.parse(localStorage.getItem('user'));
    if (user?.token) {
      config.headers.Authorization = `Bearer ${user.token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);
 
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('user');
      if (!window.location.pathname.includes('/login') &&
          !window.location.pathname.includes('/register')) {
        window.location.href = '/login';
      }
    }
    return Promise.reject(error);
  }
);
 
export default api;
 
