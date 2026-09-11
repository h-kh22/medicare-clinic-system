import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import AdminLayout from '../../layouts/AdminLayout';
import RoleGuard from '../../components/guards/RoleGuard';
import { getPatient } from '../../services/patientService';
import { useTranslation } from 'react-i18next';

export default function AdminPatientDetailPage() { const { t } = useTranslation(); const { id } = useParams(); const [patient, setPatient] = useState(null); const [error, setError] = useState(''); useEffect(() => { getPatient(id).then((r) => setPatient(r.data.data)).catch((e) => setError(e.response?.data?.message || t('failedToLoadPatient'))); }, [id]); const fields = [['fullName', 'name'], ['email', 'email'], ['phone', 'phone'], ['dateOfBirth', 'date_of_birth'], ['gender', 'gender'], ['emergencyContact', 'emergency_contact'], ['address', 'address']]; return <RoleGuard roles={['admin']}><AdminLayout><div className="max-w-3xl space-y-6"><h1 className="text-3xl font-bold text-slate-900">{t('patientDetails')}</h1>{error && <p className="text-rose-600">{error}</p>}{patient && <div className="grid gap-4 rounded-2xl border border-slate-200 bg-white p-6 sm:grid-cols-2">{fields.map(([label, field]) => <div className={field === 'address' ? 'sm:col-span-2' : ''} key={field}><p className="text-xs uppercase text-slate-500">{t(label)}</p><p className="font-semibold">{patient[field] || t('notProvided')}</p></div>)}</div>}</div></AdminLayout></RoleGuard>; }
