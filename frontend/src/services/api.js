import axios from 'axios';

/**
 * MediCare API Client
 * Configured with environment-based base URL and interceptors for future authentication.
 */
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  timeout: 10000,
});

// Request Interceptor: Attach Bearer token when available
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('medicare_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response Interceptor: Uniform error handling & status tracking
api.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    // Handle unauthorized access gracefully in future phases
    if (error.response && error.response.status === 401) {
      // Future authentication token expiration handling
    }
    return Promise.reject(error);
  }
);

export default api;
