import React, { useEffect, useState } from 'react';
import { CalendarDays } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import RoleGuard from '../components/guards/RoleGuard';
import AdminLayout from '../layouts/AdminLayout';
import { createAppointment, getAppointments, cancelAppointment, updateAppointment } from '../services/appointmentService';
import { getDoctors } from '../services/doctorService';

export default function AppointmentPage({ role, booking = false }) {
  const { t } = useTranslation();
  const [appointments, setAppointments] = useState([]);
  const [doctors, setDoctors] = useState([]);
  const [form, setForm] = useState({ doctor_id: '', appointment_date: '', appointment_time: '', reason: '' });
  const [message, setMessage] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(true);

  const load = () => {
    setLoading(true);
    Promise.all([getAppointments(), booking ? getDoctors() : Promise.resolve({ data: { data: [] } })])
      .then(([appointmentsResponse, doctorsResponse]) => {
        setAppointments(appointmentsResponse.data.data.appointments || []);
        setDoctors(doctorsResponse.data.data || []);
      })
      .catch((requestError) => setError(requestError.response?.data?.message || t('unableToLoadAppointments')))
      .finally(() => setLoading(false));
  };

  useEffect(load, []);

  const submit = async (event) => {
    event.preventDefault();
    setError('');
    setMessage('');
    try {
      await createAppointment(form);
      setForm({ doctor_id: '', appointment_date: '', appointment_time: '', reason: '' });
      setMessage(t('appointmentRequested'));
      load();
    } catch (requestError) {
      setError(requestError.response?.data?.message || Object.values(requestError.response?.data?.errors || {}).flat()[0] || t('bookingFailed'));
    }
  };

  const cancel = async (id) => {
    try { await cancelAppointment(id); load(); } catch (requestError) { setError(requestError.response?.data?.message || t('cancellationFailed')); }
  };

  const changeStatus = async (id, status) => {
    try { await updateAppointment(id, { status }); load(); } catch (requestError) { setError(requestError.response?.data?.message || t('unableToUpdateAppointment')); }
  };

  return <RoleGuard roles={[role]}><AdminLayout><div className="space-y-6">
    <div><h1 className="flex items-center gap-3 text-3xl font-bold text-slate-900"><CalendarDays className="text-teal-700" />{t('appointments')}</h1><p className="mt-1 text-slate-500">{role === 'patient' ? t('manageCareSchedule') : t('reviewClinicalAppointments')}</p></div>
    {booking && <form onSubmit={submit} className="grid gap-4 rounded-2xl border border-slate-200 bg-white p-6 md:grid-cols-2"><select className="field" required value={form.doctor_id} onChange={(event) => setForm({ ...form, doctor_id: event.target.value })}><option value="">{t('selectDoctor')}</option>{doctors.map((doctor) => <option key={doctor.id} value={doctor.id}>{doctor.name}{doctor.specialty ? ` · ${doctor.specialty}` : ''}</option>)}</select><input className="field" type="date" required value={form.appointment_date} onChange={(event) => setForm({ ...form, appointment_date: event.target.value })} /><input className="field" type="time" required value={form.appointment_time} onChange={(event) => setForm({ ...form, appointment_time: event.target.value })} /><textarea className="field md:col-span-2" placeholder={t('reasonForVisit')} required value={form.reason} onChange={(event) => setForm({ ...form, reason: event.target.value })} /><button className="rounded-xl bg-teal-700 px-5 py-3 font-semibold text-white md:col-span-2">{t('requestAppointment')}</button></form>}
    {message && <p className="text-sm text-emerald-700">{message}</p>}{error && <p className="text-sm text-rose-600">{error}</p>}
    <div className="overflow-auto rounded-2xl border border-slate-200 bg-white"><table className="min-w-full text-sm"><thead className="bg-slate-50 text-left text-slate-500"><tr>{['doctorPatient', 'date', 'time', 'status', 'action'].map((key) => <th className="p-4" key={key}>{t(key)}</th>)}</tr></thead><tbody>{!loading && appointments.map((item) => <tr className="border-t border-slate-100" key={item.id}><td className="p-4 font-medium">{item.doctor?.name || item.patient?.name}</td><td className="p-4">{item.appointment_date}</td><td className="p-4">{item.appointment_time}</td><td className="p-4">{t(item.status)}</td><td className="flex gap-2 p-4">{role === 'patient' && item.status === 'pending' && <button onClick={() => cancel(item.id)} className="text-rose-700">{t('cancel')}</button>}{role === 'admin' && item.status === 'pending' && <button onClick={() => changeStatus(item.id, 'confirmed')} className="text-teal-700">{t('approve')}</button>}{role === 'doctor' && item.status === 'confirmed' && <button onClick={() => changeStatus(item.id, 'completed')} className="text-teal-700">{t('complete')}</button>}</td></tr>)}</tbody></table>{loading && <p className="p-8 text-center text-slate-500">{t('loadingAppointments')}</p>}{!loading && !appointments.length && <p className="p-8 text-center text-slate-500">{t('noAppointmentsForRole')}</p>}</div>
  </div></AdminLayout></RoleGuard>;
}
