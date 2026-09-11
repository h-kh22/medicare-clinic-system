import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import RoleGuard from '../components/guards/RoleGuard';
import AdminLayout from '../layouts/AdminLayout';
import { getMedicalRecords } from '../services/medicalRecordService';
import { getPrescriptions } from '../services/prescriptionService';

export default function ClinicalListPage({ type, role }) {
  const { t } = useTranslation();
  const [items, setItems] = useState([]);
  const [error, setError] = useState('');
  useEffect(() => { (type === 'records' ? getMedicalRecords() : getPrescriptions()).then((response) => setItems(response.data.data || [])).catch((requestError) => setError(requestError.response?.data?.message || t('unableToLoadClinicalData'))); }, [type]);
  const records = type === 'records';
  return <RoleGuard roles={[role]}><AdminLayout><div className="space-y-6"><div className="flex flex-wrap items-center justify-between gap-3"><h1 className="text-3xl font-bold text-slate-900">{t(records ? 'medicalRecords' : 'prescriptions')}</h1>{role === 'doctor' && <Link className="rounded-xl bg-teal-700 px-4 py-2.5 font-semibold text-white" to={records ? '/doctor/records/create' : '/doctor/prescriptions/create'}>{t(records ? 'createRecord' : 'createPrescriptionAction')}</Link>}</div>{error && <p className="text-sm text-rose-600">{error}</p>}<div className="grid gap-4">{items.map((item) => <article key={item.id} className="rounded-2xl border border-slate-200 bg-white p-6"><div className="flex justify-between"><div><p className="text-sm text-slate-500">{item.prescription_date || item.date || t('clinicalRecord')}</p><h2 className="font-semibold text-slate-900">{item.doctor?.name || t('careTeam')}</h2></div><span className="text-xs text-slate-500">{item.patient?.name}</span></div>{records ? <div className="mt-4 grid gap-2 text-sm"><p><b>{t('diagnosis')}:</b> {item.diagnosis}</p><p><b>{t('symptoms')}:</b> {item.symptoms || t('notRecorded')}</p><p><b>{t('treatment')}:</b> {item.treatment || t('notRecorded')}</p><p><b>{t('notes')}:</b> {item.notes || t('none')}</p></div> : <div className="mt-4 grid gap-2">{(item.items || []).map((line) => <div className="rounded-lg bg-slate-50 p-3 text-sm" key={line.id}><b>{line.medicine_name}</b> · {line.dosage} · {line.frequency} · {line.duration}<p className="text-slate-500">{line.instructions}</p></div>)}</div>}</article>)}{!items.length && <div className="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">{t(records ? 'noClinicalRecords' : 'noPrescriptions')}</div>}</div></div></AdminLayout></RoleGuard>;
}
