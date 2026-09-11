import React, { useState, useEffect, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import { Users, RefreshCw } from 'lucide-react';
import AdminLayout from '../../layouts/AdminLayout';
import RoleGuard from '../../components/guards/RoleGuard';
import PatientTable from '../../components/patients/PatientTable';
import SearchBar from '../../components/common/SearchBar';
import Pagination from '../../components/common/Pagination';
import { getPatients } from '../../services/patientService';
import { useTranslation } from 'react-i18next';

export default function AdminPatientsPage() {
  const { t } = useTranslation();
  const navigate = useNavigate();

  const [patients, setPatients] = useState([]);
  const [meta, setMeta]         = useState(null);
  const [search, setSearch]     = useState('');
  const [page, setPage]         = useState(1);
  const [loading, setLoading]   = useState(true);
  const [error, setError]       = useState('');

  const fetchPatients = useCallback(async () => {
    setLoading(true);
    setError('');
    try {
      const res = await getPatients({ search, page });
      setPatients(res.data.data.patients);
      setMeta(res.data.data.meta);
    } catch (err) {
      setError(err.response?.data?.message ?? t('failedToLoadPatients'));
    } finally {
      setLoading(false);
    }
  }, [search, page]);

  useEffect(() => {
    fetchPatients();
  }, [fetchPatients]);

  // When search changes, reset to page 1
  const handleSearch = (value) => {
    setSearch(value);
    setPage(1);
  };

  return (
    <RoleGuard roles={['admin']}>
      <AdminLayout>
        <div className="space-y-6">
          {/* Page header */}
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <Users size={24} className="text-teal-600" />
                {t('patients')}
              </h1>
              <p className="text-sm text-slate-500 mt-1">
                {t('manageRegisteredPatients')}
              </p>
            </div>
            <button
              onClick={fetchPatients}
              className="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                         text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-100 transition"
            >
              <RefreshCw size={15} className={loading ? 'animate-spin' : ''} />
              {t('refresh')}
            </button>
          </div>

          {/* Stats card */}
          {meta && (
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <StatCard label={t('totalPatients')} value={meta.total} color="teal" />
              <StatCard label={t('page')} value={`${meta.current_page} / ${meta.last_page}`} color="emerald" />
              <StatCard label={t('perPage')} value={meta.per_page} color="cyan" />
            </div>
          )}

          {/* Search bar */}
          <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
            <SearchBar
              placeholder={t('searchPatients')}
              onSearch={handleSearch}
            />
          </div>

          {/* Table */}
          <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
            {error && (
              <div className="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm">
                {error}
              </div>
            )}

            {loading ? (
              <div className="flex items-center justify-center py-20">
                <div className="w-8 h-8 border-4 border-teal-500 border-t-transparent rounded-full animate-spin" />
              </div>
            ) : (
              <>
                <PatientTable
                  patients={patients}
                  showActions
                  onView={(p) => navigate(`/admin/patients/${p.id}`)}
                />
                <div className="mt-3">
                  <Pagination meta={meta} onPageChange={setPage} />
                </div>
              </>
            )}
          </div>
        </div>
      </AdminLayout>
    </RoleGuard>
  );
}

function StatCard({ label, value, color }) {
  const colors = {
    teal:    'from-teal-50 to-teal-50/50 text-teal-800 border-teal-100',
    emerald: 'from-emerald-50 to-emerald-50/50 text-emerald-800 border-emerald-100',
    cyan:    'from-cyan-50 to-cyan-50/50 text-cyan-800 border-cyan-100',
  };

  return (
    <div className={`rounded-2xl border bg-gradient-to-br p-4 ${colors[color]}`}>
      <p className="text-xs font-medium opacity-60 uppercase tracking-wide">{label}</p>
      <p className="text-2xl font-bold mt-1">{value}</p>
    </div>
  );
}
