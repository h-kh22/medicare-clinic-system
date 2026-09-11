import api from './api';

export const getPrescriptions = (params = {}) => api.get('/prescriptions', { params });
export const getPrescription = (id) => api.get(`/prescriptions/${id}`);
export const createPrescription = (data) => api.post('/prescriptions', data);
export const updatePrescription = (id, data) => api.put(`/prescriptions/${id}`, data);
