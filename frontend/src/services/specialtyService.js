import api from './api';

export const getSpecialties = () => api.get('/specialties');
export const createSpecialty = (data) => api.post('/admin/specialties', data);
export const updateSpecialty = (id, data) => api.put(`/admin/specialties/${id}`, data);
export const deleteSpecialty = (id) => api.delete(`/admin/specialties/${id}`);
