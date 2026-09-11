import React, { useEffect, useState } from 'react';
import { CalendarDays, FileText, Pill, Stethoscope } from 'lucide-react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import RoleGuard from '../components/guards/RoleGuard';
import AdminLayout from '../layouts/AdminLayout';
import { getAppointments } from '../services/appointmentService';
import { getMedicalRecords } from '../services/medicalRecordService';
import { getPrescriptions } from '../services/prescriptionService';
import { getDoctors } from '../services/doctorService';
import { getPatients } from '../services/patientService';

const config = {
  admin: { title: 'operationsOverview', roles: ['admin'] },
  doctor: { title: 'clinicalWorkspace', roles: ['doctor'] },
  patient: { title: 'careOverview', roles: ['patient'] },
};

export default function PortalPage({ role }) {
  const { t } = useTranslation();
  const [data, setData] = useState({ appointments: [], records: [], prescriptions: [], doctors: [], patients: [] });
  const [error, setError] = useState('');
  const settings = config[role];

  useEffect(() => {
    const requests = [getAppointments(), getMedicalRecords(), getPrescriptions()];
    if (role === 'admin') requests.push(getDoctors(), getPatients());
    if (role === 'doctor') requests.push(getPatients());
    Promise.all(requests).then((responses) => {
      setData({
        appointments: responses[0].data.data.appointments || [],
        records: responses[1].data.data || [],
        prescriptions: responses[2].data.data || [],
        doctors: role === 'admin' ? responses[3].data.data || [] : [],
        patients: role !== 'patient' ? (responses[role === 'admin' ? 4 : 3].data.data.patients || []) : [],
      });
    }).catch((requestError) => setError(requestError.response?.data?.message || t('unableToLoadDashboard')));
  }, [role]);

  const today = new Date().toISOString().slice(0, 10);
  const stats = role === 'admin'
    ? [['doctors', data.doctors.length], ['patients', data.patients.length], ['todayAppointments', data.appointments.filter((item) => item.appointment_date === today).length], ['pendingAppointments', data.appointments.filter((item) => item.status === 'pending').length], ['completedAppointments', data.appointments.filter((item) => item.status === 'completed').length]]
    : [['appointments', data.appointments.length], ['relevantPatients', data.patients.length], ['medicalRecords', data.records.length], ['prescriptions', data.prescriptions.length]];

  return <RoleGuard roles={settings.roles}>
    <AdminLayout>
      <div className="space-y-6">
        <div>
          <p className="text-sm font-semibold uppercase tracking-widest text-teal-700 dark:text-teal-300">{t('portal')}</p>
          <h1 className="text-3xl font-bold text-slate-900 dark:text-white">{t(settings.title)}</h1>
          {error && <p className="mt-2 text-sm text-rose-600">{error}</p>}
        </div>
        <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
          {stats.map(([label, value]) => <div key={label} className="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900"><p className="text-sm text-slate-500 dark:text-slate-400">{t(label)}</p><p className="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{value}</p></div>)}
        </div>
        <div className="flex flex-wrap gap-3">
          {role === 'patient' && <><Link className="action-link" to="/patient/book-appointment"><CalendarDays size={16} />{t('findDoctor')}</Link><Link className="action-link" to="/patient/appointments"><CalendarDays size={16} />{t('viewAppointments')}</Link><Link className="action-link" to="/patient/records"><FileText size={16} />{t('viewRecords')}</Link><Link className="action-link" to="/patient/prescriptions"><Pill size={16} />{t('viewPrescriptions')}</Link></>}
          {role === 'admin' && <><Link className="action-link" to="/admin/doctors"><Stethoscope size={16} />{t('manageDoctors')}</Link><Link className="action-link" to="/admin/appointments"><CalendarDays size={16} />{t('manageAppointments')}</Link></>}
          {role === 'doctor' && <><Link className="action-link" to="/doctor/appointments"><CalendarDays size={16} />{t('appointments')}</Link><Link className="action-link" to="/doctor/records"><FileText size={16} />{t('medicalRecords')}</Link></>}
        </div>
        <section className="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-900">
          <h2 className="font-semibold text-slate-900 dark:text-white">{t('recentAppointments')}</h2>
          <div className="mt-4 divide-y divide-slate-100 dark:divide-slate-800">
            {data.appointments.slice(0, 6).map((item) => <div key={item.id} className="flex flex-wrap justify-between gap-2 py-3 text-sm"><span className="text-slate-700 dark:text-slate-200">{item.doctor?.name || item.patient?.name || t('appointments')}</span><span className="text-slate-500">{item.appointment_date} · {item.status}</span></div>)}
            {!data.appointments.length && <p className="py-6 text-sm text-slate-500">{t('noAppointments')}</p>}
          </div>
        </section>
      </div>
    </AdminLayout>
  </RoleGuard>;
}
