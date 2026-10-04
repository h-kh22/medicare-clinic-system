import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { useAuth } from '../context/AuthContext';
import { AuthCard } from './LoginPage';

export default function RegisterPage() {
  const { t } = useTranslation();
  const { register } = useAuth(); const navigate = useNavigate();
  const [form, setForm] = useState({ name: '', email: '', phone: '', password: '', password_confirmation: '' }); const [error, setError] = useState(''); const [emailError, setEmailError] = useState(''); const [loading, setLoading] = useState(false);
  const submit = async (event) => { event.preventDefault(); setLoading(true); setError(''); setEmailError(''); try { const user = await register(form); navigate(`/${user.role}/dashboard`); } catch (err) { const responseData = err.response?.data || {}; const validationErrors = responseData.errors || responseData.data?.errors || responseData.data || {}; if (validationErrors.email) setEmailError(t('emailAlreadyRegistered')); setError(Object.entries(validationErrors).filter(([field]) => field !== 'email').flatMap(([, messages]) => messages).join(' ') || (validationErrors.email ? '' : responseData.message || t('unableToRegister'))); } finally { setLoading(false); } };
  return <AuthCard title={t('createAccount')} onSubmit={submit} error={error} loading={loading}><input className="field" placeholder={t('fullName')} required value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} /><div><input className="field" type="email" placeholder={t('emailAddress')} required value={form.email} onChange={(e) => { setForm({ ...form, email: e.target.value }); setEmailError(''); }} />{emailError && <p className="mt-1 text-sm text-rose-600" role="alert">{emailError}</p>}</div><input className="field" placeholder={t('phone')} value={form.phone} onChange={(e) => setForm({ ...form, phone: e.target.value })} /><input className="field" type="password" placeholder={t('password')} required value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} /><input className="field" type="password" placeholder={t('confirmPassword')} required value={form.password_confirmation} onChange={(e) => setForm({ ...form, password_confirmation: e.target.value })} /><p className="text-sm text-slate-500">{t('alreadyRegistered')} <Link className="font-semibold text-teal-700 dark:text-teal-300" to="/login">{t('signIn')}</Link></p></AuthCard>;
}
