import React, { createContext, useContext, useState, useEffect } from 'react';
import api from '../services/api';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(() => localStorage.getItem('medicare_token'));
  const [loading, setLoading] = useState(true);

  // Synchronize authenticated session on mount or token change
  useEffect(() => {
    const fetchCurrentUser = async () => {
      const storedToken = localStorage.getItem('medicare_token');
      if (!storedToken) {
        setUser(null);
        setLoading(false);
        return;
      }

      try {
        const response = await api.get('/me');
        if (response.data && response.data.data) {
          setUser(response.data.data.user);
        }
      } catch (error) {
        console.error('Session verification failed:', error);
        localStorage.removeItem('medicare_token');
        setToken(null);
        setUser(null);
      } finally {
        setLoading(false);
      }
    };

    fetchCurrentUser();
  }, [token]);

  const login = async (email, password) => {
    const response = await api.post('/login', { email, password });
    const { user: userData, token: authToken } = response.data.data;

    localStorage.setItem('medicare_token', authToken);
    setToken(authToken);
    setUser(userData);
    return userData;
  };

  const register = async (registrationData) => {
    const response = await api.post('/register', registrationData);
    const { user: userData, token: authToken } = response.data.data;

    localStorage.setItem('medicare_token', authToken);
    setToken(authToken);
    setUser(userData);
    return userData;
  };

  const logout = async () => {
    try {
      await api.post('/logout');
    } catch (error) {
      console.warn('Logout API error:', error);
    } finally {
      localStorage.removeItem('medicare_token');
      setToken(null);
      setUser(null);
    }
  };

  const value = {
    user,
    token,
    loading,
    isAuthenticated: !!user,
    role: user?.role,
    login,
    register,
    logout,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}
