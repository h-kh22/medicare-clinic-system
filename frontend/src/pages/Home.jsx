import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { 
  Activity, 
  Server, 
  CheckCircle2, 
  XCircle, 
  Database, 
  Stethoscope, 
  Users, 
  Calendar,
  LogIn
} from 'lucide-react';
import api from '../services/api';
import { useTranslation } from 'react-i18next';

export default function Home() {
  const { t } = useTranslation();
  const [health, setHealth] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const checkApiHealth = async () => {
      try {
        setLoading(true);
        const res = await api.get('/health');
        setHealth(res.data);
        setError(null);
      } catch (err) {
        setError(err.message || t('unableToReachApi'));
        setHealth(null);
      } finally {
        setLoading(false);
      }
    };

    checkApiHealth();
  }, []);

  return (
    <div className="max-w-5xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
      {/* Hero Section */}
      <div className="text-center space-y-4">
        <div className="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-100 text-teal-800 text-xs font-semibold tracking-wide uppercase">
          <Activity className="w-4 h-4 text-teal-600" />
          <span>{t('clinicSystem')}</span>
        </div>

        <h1 className="text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-tight">
          Medi<span className="text-teal-600">Care</span>
        </h1>

        <p className="text-lg sm:text-xl text-slate-600 max-w-2xl mx-auto font-normal">
          {t('apiPlatform')}
        </p>

        {/* Live Backend Connection Card */}
        <div className="pt-6 max-w-md mx-auto">
          <div className="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 transition-all hover:shadow-md text-left">
            <div className="flex items-center justify-between pb-3 border-b border-slate-100">
              <div className="flex items-center space-x-2">
                <Server className="w-4 h-4 text-slate-500" />
                <span className="text-xs font-semibold uppercase tracking-wider text-slate-500">{t('apiHealthStatus')}</span>
              </div>
              <div>
                {loading && (
                  <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                    {t('connecting')}
                  </span>
                )}
                {!loading && health && (
                  <span className="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <CheckCircle2 className="w-3.5 h-3.5" />
                    <span>{t('online')}</span>
                  </span>
                )}
                {!loading && error && (
                  <span className="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                    <XCircle className="w-3.5 h-3.5" />
                    <span>{t('offline')}</span>
                  </span>
                )}
              </div>
            </div>

            <div className="pt-3 text-xs space-y-1.5">
              <div className="flex justify-between text-slate-600">
                <span className="text-slate-400">{t('endpoint')}:</span>
                <code className="bg-slate-100 px-1.5 py-0.5 rounded text-slate-800 font-mono">/api/health</code>
              </div>
              {health && (
                <>
                  <div className="flex justify-between text-slate-600">
                    <span className="text-slate-400">{t('application')}:</span>
                    <span className="font-medium text-slate-800">{health?.data?.application}</span>
                  </div>
                  <div className="flex justify-between text-slate-600">
                    <span className="text-slate-400">{t('version')}:</span>
                    <span className="font-medium text-slate-800">{health?.data?.version}</span>
                  </div>
                </>
              )}
              {error && (
                <div className="text-rose-600 text-xs bg-rose-50 p-2 rounded-lg border border-rose-100">
                  {error}
                </div>
              )}

              <Link
                to="/login"
                className="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-teal-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
              >
                <LogIn className="h-4 w-4" />
                {t('goToLogin')}
              </Link>
            </div>
          </div>
        </div>
      </div>

      {/* Overview Cards */}
      <div className="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="bg-white rounded-xl p-6 border border-slate-200 shadow-sm hover:border-teal-300 transition-colors">
          <div className="w-10 h-10 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center mb-4">
            <Stethoscope className="w-5 h-5" />
          </div>
          <h2 className="text-base font-semibold text-slate-900">{t('healthcareExcellence')}</h2>
          <p className="mt-1 text-xs text-slate-500 leading-relaxed">
            {t('healthcareExcellenceDescription')}
          </p>
        </div>

        <div className="bg-white rounded-xl p-6 border border-slate-200 shadow-sm hover:border-teal-300 transition-colors">
          <div className="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
            <Database className="w-5 h-5" />
          </div>
          <h2 className="text-base font-semibold text-slate-900">{t('robustRestApi')}</h2>
          <p className="mt-1 text-xs text-slate-500 leading-relaxed">
            {t('robustRestApiDescription')}
          </p>
        </div>

        <div className="bg-white rounded-xl p-6 border border-slate-200 shadow-sm hover:border-teal-300 transition-colors">
          <div className="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
            <Users className="w-5 h-5" />
          </div>
          <h2 className="text-base font-semibold text-slate-900">{t('multiRoleArchitecture')}</h2>
          <p className="mt-1 text-xs text-slate-500 leading-relaxed">
            {t('multiRoleArchitectureDescription')}
          </p>
        </div>
      </div>
    </div>
  );
}
