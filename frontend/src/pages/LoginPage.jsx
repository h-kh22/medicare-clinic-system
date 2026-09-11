import React, { useState } from 'react';
import { Link, Navigate, useNavigate } from 'react-router-dom';
import { Activity } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { useAuth } from '../context/AuthContext';

export default function LoginPage() {
  const { t } = useTranslation();
  const { login, user } = useAuth();
  const navigate = useNavigate();
  const [form, setForm] = useState({ email: '', password: '' });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  if (user) return <Navigate to={`/${user.role}/dashboard`} replace />;
  const submit = async (event) => { event.preventDefault(); setLoading(true); setError(''); try { const loggedIn = await login(form.email, form.password); navigate(`/${loggedIn.role}/dashboard`); } catch (err) { setError(err.response?.data?.message || t('unableToSignIn')); } finally { setLoading(false); } };
  return <AuthCard title={t('welcomeBack')} onSubmit={submit} error={error} loading={loading}>
    <input className="field" type="email" placeholder={t('emailAddress')} required value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} />
    <input className="field" type="password" placeholder={t('password')} required value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} />
    <p className="text-sm text-slate-500">{t('needAccount')} <Link className="font-semibold text-teal-700 dark:text-teal-300" to="/register">{t('register')}</Link></p>
  </AuthCard>;
}

export function AuthCard({ title, children, onSubmit, error, loading }) {
  const { t } = useTranslation();
  const isLogin = title === t('welcomeBack');
  return <div className="flex min-h-screen items-center justify-center bg-slate-100 p-6 dark:bg-slate-950"><form onSubmit={onSubmit} className="w-full max-w-md space-y-4 rounded-2xl border border-slate-200 bg-white p-8 shadow-xl dark:border-slate-700 dark:bg-slate-900"><div className="mb-6 flex items-center gap-3"><div className="rounded-xl bg-teal-600 p-3 text-white"><Activity size={22} /></div><div><p className="font-bold text-slate-900 dark:text-white">{t('brand')}</p><p className="text-xs text-slate-500">{t('clinicPortal')}</p></div></div><h1 className="text-2xl font-bold text-slate-900 dark:text-white">{title}</h1>{error && <div className="rounded-lg border border-rose-100 bg-rose-50 p-3 text-sm text-rose-700">{error}</div>}{children}<button disabled={loading} className="w-full rounded-xl bg-teal-700 py-3 font-semibold text-white disabled:opacity-50">{loading ? t('pleaseWait') : isLogin ? t('signIn') : t('createAccountAction')}</button></form></div>;
}
