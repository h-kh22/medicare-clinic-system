import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';

// Layouts
import MainLayout from '../layouts/MainLayout';

// Pages — public / general
import Home from '../pages/Home';

// Pages — admin
import AdminPatientsPage from '../pages/admin/AdminPatientsPage';

// Pages — patient
import PatientProfilePage from '../pages/patient/PatientProfilePage';

// Pages — doctor
import DoctorPatientsPage from '../pages/doctor/DoctorPatientsPage';
import LoginPage from '../pages/LoginPage';
import RegisterPage from '../pages/RegisterPage';
import PortalPage from '../pages/PortalPage';
import AppointmentPage from '../pages/AppointmentPage';
import ClinicalListPage from '../pages/ClinicalListPage';
import AdminDoctorsPage from '../pages/admin/AdminDoctorsPage';
import AdminSpecialtiesPage from '../pages/admin/AdminSpecialtiesPage';
import AdminPatientDetailPage from '../pages/admin/AdminPatientDetailPage';
import DoctorDirectoryPage from '../pages/DoctorDirectoryPage';
import ClinicalCreatePage from '../pages/ClinicalCreatePage';

export default function AppRoutes() {
  return (
    <Routes>
      {/* Public */}
      <Route path="/" element={<MainLayout><Home /></MainLayout>} />
      <Route path="/login" element={<LoginPage />} />
      <Route path="/register" element={<RegisterPage />} />
      <Route path="/doctors" element={<DoctorDirectoryPage />} />
      <Route path="/doctors/:id" element={<DoctorDirectoryPage detail />} />
      <Route path="/patient/doctors" element={<DoctorDirectoryPage />} />
      <Route path="/patient/doctors/:id" element={<DoctorDirectoryPage detail />} />

      {/* Admin — patients */}
      {/* RoleGuard is embedded inside each admin page */}
      <Route path="/admin/patients" element={<AdminPatientsPage />} />
      <Route path="/admin/patients/:id" element={<AdminPatientDetailPage />} />
      <Route path="/admin/doctors" element={<AdminDoctorsPage />} />
      <Route path="/admin/specialties" element={<AdminSpecialtiesPage />} />

      {/* Patient — own profile */}
      <Route path="/patient/profile" element={<PatientProfilePage />} />

      {/* Doctor — linked patients */}
      <Route path="/doctor/patients" element={<DoctorPatientsPage />} />

      <Route path="/admin/dashboard" element={<PortalPage role="admin" />} />
      <Route path="/doctor/dashboard" element={<PortalPage role="doctor" />} />
      <Route path="/patient/dashboard" element={<PortalPage role="patient" />} />
      <Route path="/patient/appointments" element={<AppointmentPage role="patient" />} />
      <Route path="/patient/book-appointment" element={<AppointmentPage role="patient" booking />} />
      <Route path="/doctor/appointments" element={<AppointmentPage role="doctor" />} />
      <Route path="/admin/appointments" element={<AppointmentPage role="admin" />} />
      <Route path="/patient/records" element={<ClinicalListPage role="patient" type="records" />} />
      <Route path="/doctor/records" element={<ClinicalListPage role="doctor" type="records" />} />
      <Route path="/doctor/records/create" element={<ClinicalCreatePage type="record" />} />
      <Route path="/admin/medical-records" element={<ClinicalListPage role="admin" type="records" />} />
      <Route path="/patient/prescriptions" element={<ClinicalListPage role="patient" type="prescriptions" />} />
      <Route path="/doctor/prescriptions" element={<ClinicalListPage role="doctor" type="prescriptions" />} />
      <Route path="/doctor/prescriptions/create" element={<ClinicalCreatePage type="prescription" />} />
      <Route path="/admin/prescriptions" element={<ClinicalListPage role="admin" type="prescriptions" />} />

      {/* Fallback */}
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  );
}
