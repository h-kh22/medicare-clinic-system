import React from 'react';
import { User, Phone, Mail, MapPin, AlertCircle, Eye } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

/**
 * PatientTable
 * Renders a professional hospital-style table of patients.
 *
 * Props:
 * - patients: PatientResource[] from the API
 * - onView: (patient) => void  — optional callback
 * - showActions: bool  — show action buttons (admin/doctor only)
 */
export default function PatientTable({ patients = [], onView, showActions = true }) {
  const { t } = useTranslation();
  const navigate = useNavigate();

  if (patients.length === 0) {
    return (
      <div className="flex flex-col items-center justify-center py-16 text-slate-400">
        <User size={48} className="mb-3 opacity-30" />
        <p className="text-sm font-medium">{t('noPatientsFound')}</p>
        <p className="text-xs mt-1">{t('adjustSearchCriteria')}</p>
      </div>
    );
  }

  return (
    <div className="overflow-x-auto rounded-2xl border border-slate-100 shadow-sm">
      <table className="w-full text-sm">
        <thead>
          <tr className="bg-gradient-to-r from-teal-50 to-emerald-50 border-b border-slate-100">
            <th className="text-left px-5 py-3.5 text-xs font-semibold text-teal-700 uppercase tracking-wide">
              {t('patient')}
            </th>
            <th className="text-left px-5 py-3.5 text-xs font-semibold text-teal-700 uppercase tracking-wide">
              {t('contact')}
            </th>
            <th className="text-left px-5 py-3.5 text-xs font-semibold text-teal-700 uppercase tracking-wide hidden md:table-cell">
              {t('dateOfBirth')}
            </th>
            <th className="text-left px-5 py-3.5 text-xs font-semibold text-teal-700 uppercase tracking-wide hidden lg:table-cell">
              {t('gender')}
            </th>
            <th className="text-left px-5 py-3.5 text-xs font-semibold text-teal-700 uppercase tracking-wide hidden lg:table-cell">
              {t('emergencyContact')}
            </th>
            {showActions && (
              <th className="text-right px-5 py-3.5 text-xs font-semibold text-teal-700 uppercase tracking-wide">
                {t('actions')}
              </th>
            )}
          </tr>
        </thead>
        <tbody className="divide-y divide-slate-50 bg-white">
          {patients.map((patient) => (
            <tr
              key={patient.id}
              className="hover:bg-teal-50/30 transition-colors group"
            >
              {/* Patient name + email */}
              <td className="px-5 py-4">
                <div className="flex items-center gap-3">
                  <div className="w-9 h-9 rounded-full bg-gradient-to-br from-teal-400 to-emerald-500
                                  flex items-center justify-center text-white font-semibold text-sm flex-shrink-0 shadow-sm">
                    {patient.name?.charAt(0)?.toUpperCase() ?? '?'}
                  </div>
                  <div>
                    <p className="font-semibold text-slate-800">{patient.name ?? '—'}</p>
                    <p className="text-xs text-slate-400 flex items-center gap-1 mt-0.5">
                      <Mail size={10} />
                      {patient.email ?? '—'}
                    </p>
                  </div>
                </div>
              </td>

              {/* Phone */}
              <td className="px-5 py-4">
                <span className="flex items-center gap-1.5 text-slate-600">
                  <Phone size={13} className="text-teal-400" />
                  {patient.phone ?? <span className="text-slate-300">—</span>}
                </span>
              </td>

              {/* DoB */}
              <td className="px-5 py-4 hidden md:table-cell text-slate-600">
                {patient.date_of_birth ?? <span className="text-slate-300">—</span>}
              </td>

              {/* Gender */}
              <td className="px-5 py-4 hidden lg:table-cell">
                {patient.gender ? (
                  <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    ${patient.gender === 'male'
                      ? 'bg-blue-50 text-blue-700'
                      : patient.gender === 'female'
                      ? 'bg-pink-50 text-pink-700'
                      : 'bg-slate-100 text-slate-600'
                    }`}>
                    {patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1)}
                  </span>
                ) : (
                  <span className="text-slate-300">—</span>
                )}
              </td>

              {/* Emergency contact */}
              <td className="px-5 py-4 hidden lg:table-cell text-slate-600 text-xs">
                {patient.emergency_contact ? (
                  <span className="flex items-center gap-1">
                    <AlertCircle size={12} className="text-amber-400" />
                    {patient.emergency_contact}
                  </span>
                ) : (
                  <span className="text-slate-300">—</span>
                )}
              </td>

              {/* Actions */}
              {showActions && (
                <td className="px-5 py-4 text-right">
                  <button
                    onClick={() => onView ? onView(patient) : navigate(`/admin/patients/${patient.id}`)}
                    className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                               text-teal-700 bg-teal-50 hover:bg-teal-100 transition border border-teal-100"
                  >
                    <Eye size={13} />
                    {t('view')}
                  </button>
                </td>
              )}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
