import api from './api';

export const getDoctors = (params = {}) => api.get('/doctors', { params });
export const getDoctor = (id) => api.get(`/doctors/${id}`);
export const getAdminDoctors = (params = {}) => api.get('/admin/doctors', { params });
export const createDoctor = (data) => api.post('/admin/doctors', data);
export const updateDoctor = (id, data) => api.put(`/admin/doctors/${id}`, data);
export const deleteDoctor = (id) => api.delete(`/admin/doctors/${id}`);
