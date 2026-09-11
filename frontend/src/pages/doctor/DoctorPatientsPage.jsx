import React, { useState, useEffect, useCallback } from 'react';
import { Stethoscope, RefreshCw } from 'lucide-react';
import MainLayout from '../../layouts/MainLayout';
import RoleGuard from '../../components/guards/RoleGuard';
import PatientTable from '../../components/patients/PatientTable';
import Pagination from '../../components/common/Pagination';
import { getPatients } from '../../services/patientService';
import { useTranslation } from 'react-i18next';

/**
 * DoctorPatientsPage
 * Doctors can see only patients who have had appointments with them.
 * The filtering is enforced server-side in PatientController@index.
 * Doctors cannot search arbitrary patients — no search bar here.
 */
export default function DoctorPatientsPage() {
  const { t } = useTranslation();
  const [patients, setPatients] = useState([]);
  const [meta, setMeta]         = useState(null);
  const [page, setPage]         = useState(1);
  const [loading, setLoading]   = useState(true);
  const [error, setError]       = useState('');

  const fetchPatients = useCallback(async () => {
    setLoading(true);
    setError('');
    try {
      const res = await getPatients({ page });
      setPatients(res.data.data.patients);
      setMeta(res.data.data.meta);
    } catch (err) {
      setError(err.response?.data?.message ?? t('failedToLoadPatients'));
    } finally {
      setLoading(false);
    }
  }, [page]);

  useEffect(() => {
    fetchPatients();
  }, [fetchPatients]);

  return (
    <RoleGuard roles={['doctor']}>
      <MainLayout>
        <div className="max-w-5xl mx-auto px-4 py-10 space-y-6">
          {/* Header */}
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <Stethoscope size={24} className="text-teal-600" />
                {t('myPatients')}
              </h1>
              <p className="text-sm text-slate-500 mt-1">
                {t('patientsWithAppointments')}
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

          {/* Table card */}
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
                  showActions={false}
                />
                <div className="mt-3">
                  <Pagination meta={meta} onPageChange={setPage} />
                </div>
              </>
            )}
          </div>
        </div>
      </MainLayout>
    </RoleGuard>
  );
}
