import React, { useState, useEffect } from 'react';
import { Pencil, Save, X } from 'lucide-react';
import AdminLayout from '../../layouts/AdminLayout';
import RoleGuard from '../../components/guards/RoleGuard';
import PatientCard from '../../components/patients/PatientCard';
import { getMyPatientProfile, updatePatient } from '../../services/patientService';
import { useTranslation } from 'react-i18next';

export default function PatientProfilePage() {
  const { t } = useTranslation();
  const [patient, setPatient]   = useState(null);
  const [editing, setEditing]   = useState(false);
  const [loading, setLoading]   = useState(true);
  const [saving, setSaving]     = useState(false);
  const [error, setError]       = useState('');
  const [emailError, setEmailError] = useState('');
  const [success, setSuccess]   = useState('');
  const [form, setForm]         = useState({});

  useEffect(() => {
    const fetchProfile = async () => {
      try {
        const res = await getMyPatientProfile();
        const p = res.data?.data || {};
        setPatient(p);
        setForm({
          name:              p.name              ?? '',
          email:             p.email             ?? '',
          phone:             p.phone             ?? '',
          date_of_birth:     p.date_of_birth     ?? '',
          gender:            p.gender            ?? '',
          address:           p.address           ?? '',
          emergency_contact: p.emergency_contact ?? '',
        });
      } catch (err) {
        setError(err.response?.data?.message ?? t('failedToLoadProfile'));
      } finally {
        setLoading(false);
      }
    };
    fetchProfile();
  }, []);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
    if (name === 'email') setEmailError('');
  };

  const handleSave = async () => {
    setSaving(true);
    setError('');
    setEmailError('');
    setSuccess('');
    try {
      const res = await updatePatient(patient.id, form);
      const updated = res.data.data;
      setPatient(updated);
      setSuccess(t('profileUpdated'));
      setEditing(false);
    } catch (err) {
      const responseData = err.response?.data || {};
      const validationErrors = responseData.errors || responseData.data?.errors || responseData.data;
      if (validationErrors && typeof validationErrors === 'object') {
        if (validationErrors.email) setEmailError(t('emailAlreadyRegistered'));
        const otherErrors = Object.entries(validationErrors).filter(([field]) => field !== 'email').flatMap(([, messages]) => messages);
        setError(otherErrors.join(' ') || (validationErrors.email ? '' : responseData.message ?? t('updateFailed')));
      } else {
        setError(responseData.message ?? t('updateFailed'));
      }
    } finally {
      setSaving(false);
    }
  };

  const handleCancel = () => {
    setEditing(false);
    setEmailError('');
    setError('');
    setSuccess('');
    // Reset form to current patient data
    if (patient) {
      setForm({
        name:              patient.name              ?? '',
        email:             patient.email             ?? '',
        phone:             patient.phone             ?? '',
        date_of_birth:     patient.date_of_birth     ?? '',
        gender:            patient.gender            ?? '',
        address:           patient.address           ?? '',
        emergency_contact: patient.emergency_contact ?? '',
      });
    }
  };

  return (
    <RoleGuard roles={['patient']}>
      <AdminLayout>
        <div className="max-w-2xl mx-auto px-4 py-10">
          <div className="mb-6">
            <h1 className="text-2xl font-bold text-slate-800">{t('myProfile')}</h1>
            <p className="text-sm text-slate-500 mt-1">{t('profileDescription')}</p>
          </div>

          {/* Alerts */}
          {error && (
            <div className="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm">
              {error}
            </div>
          )}
          {success && (
            <div className="mb-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm">
              ✓ {success}
            </div>
          )}

          {loading ? (
            <div className="flex items-center justify-center py-20">
              <div className="w-8 h-8 border-4 border-teal-500 border-t-transparent rounded-full animate-spin" />
            </div>
          ) : editing ? (
            /* ── Edit Form ── */
            <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
              <h2 className="text-base font-semibold text-slate-800 mb-2">{t('editProfile')}</h2>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <Field label={t('fullName')} name="name" value={form.name} onChange={handleChange} />
                <Field label={t('email')} name="email" type="email" value={form.email} onChange={handleChange} error={emailError} />
                <Field label={t('phone')} name="phone" value={form.phone} onChange={handleChange} placeholder="+1 555 0000" />
                <Field label={t('dateOfBirth')} name="date_of_birth" type="date" value={form.date_of_birth} onChange={handleChange} />

                <div>
                  <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">{t('gender')}</label>
                  <select
                    name="gender"
                    value={form.gender}
                    onChange={handleChange}
                    className="w-full px-3 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition bg-white text-slate-700"
                  >
                    <option value="">{t('selectGender')}</option>
                    <option value="male">{t('male')}</option>
                    <option value="female">{t('female')}</option>
                    <option value="other">{t('other')}</option>
                  </select>
                </div>

                <Field label={t('address')} name="address" value={form.address} onChange={handleChange} className="sm:col-span-2" />
                <Field label={t('emergencyContact')} name="emergency_contact" value={form.emergency_contact} onChange={handleChange} placeholder={t('nameAndPhone')} className="sm:col-span-2" />
              </div>

              <div className="flex gap-3 pt-2">
                <button
                  onClick={handleSave}
                  disabled={saving}
                  className="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                             bg-teal-600 text-white hover:bg-teal-700 disabled:opacity-60 transition shadow-sm"
                >
                  <Save size={15} />
                  {saving ? t('saving') : t('saveChanges')}
                </button>
                <button
                  onClick={handleCancel}
                  className="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                             text-slate-600 bg-slate-100 hover:bg-slate-200 transition"
                >
                  <X size={15} />
                  {t('cancel')}
                </button>
              </div>
            </div>
          ) : (
            /* ── Profile Card ── */
            <PatientCard
              patient={patient || {}}
              actions={
                <button
                  onClick={() => setEditing(true)}
                  className="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                             bg-teal-600 text-white hover:bg-teal-700 transition shadow-sm"
                >
                  <Pencil size={14} />
                  {t('editProfile')}
                </button>
              }
            />
          )}
        </div>
      </AdminLayout>
    </RoleGuard>
  );
}

function Field({ label, name, value, onChange, type = 'text', placeholder, className = '', error }) {
  return (
    <div className={className}>
      <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">
        {label}
      </label>
      <input
        type={type}
        name={name}
        value={value}
        onChange={onChange}
        placeholder={placeholder}
        className={`w-full px-3 py-2.5 text-sm rounded-xl border ${error ? 'border-rose-500' : 'border-slate-200'} focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition placeholder-slate-400 text-slate-700 bg-white`}
      />
      {error && <p className="mt-1 text-sm text-rose-600" role="alert">{error}</p>}
    </div>
  );
}
