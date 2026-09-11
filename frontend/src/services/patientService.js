import api from './api';

/**
 * Patient Service
 * All Axios calls for patient-related API endpoints.
 */

/** Admin/Doctor: get paginated list of patients with optional search */
export const getPatients = (params = {}) => api.get('/patients', { params });

/** Admin/Doctor: get a single patient by their patient DB ID */
export const getPatient = (id) => api.get(`/patients/${id}`);

/** Patient: get their own profile without needing to know their patient ID */
export const getMyPatientProfile = () => api.get('/patients/me');

/** Admin / Patient (own): update a patient profile by patient ID */
export const updatePatient = (id, data) => api.put(`/patients/${id}`, data);
