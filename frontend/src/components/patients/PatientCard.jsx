import React from 'react';
import { User, Phone, Mail, MapPin, AlertCircle, Calendar, Venus, Mars } from 'lucide-react';
import { useTranslation } from 'react-i18next';

/**
 * PatientCard
 * Displays a patient's profile in a card format (used in patient profile view).
 *
 * Props:
 * - patient: PatientResource
 * - actions: ReactNode — optional action buttons to render in the card footer
 */
export default function PatientCard({ patient, actions }) {
  const { t } = useTranslation();
  if (!patient) return null;

  const genderIcon = patient.gender === 'male'
    ? <Mars size={14} className="text-blue-500" />
    : patient.gender === 'female'
    ? <Venus size={14} className="text-pink-500" />
    : null;

  const genderBadge = patient.gender
    ? {
        male: 'bg-blue-50 text-blue-700 border-blue-100',
        female: 'bg-pink-50 text-pink-700 border-pink-100',
        other: 'bg-slate-50 text-slate-600 border-slate-200',
      }[patient.gender]
    : null;

  return (
    <div className="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
      {/* Header gradient banner */}
      <div className="h-24 bg-gradient-to-r from-teal-500 via-teal-600 to-emerald-600 relative">
        <div className="absolute inset-0 opacity-10"
          style={{ backgroundImage: 'radial-gradient(circle at 20% 50%, white 1px, transparent 1px)', backgroundSize: '24px 24px' }}
        />
      </div>

      {/* Avatar */}
      <div className="px-6 pb-6">
        <div className="-mt-10 mb-4">
          <div className="w-20 h-20 rounded-2xl bg-gradient-to-br from-teal-400 to-emerald-500
                          flex items-center justify-center text-white text-3xl font-bold shadow-lg border-4 border-white">
            {patient.name?.charAt(0)?.toUpperCase() ?? <User size={32} />}
          </div>
        </div>

        {/* Name + gender */}
        <div className="flex items-start justify-between mb-5">
          <div>
            <h2 className="text-xl font-bold text-slate-800">{patient.name ?? '—'}</h2>
            {patient.gender && (
              <span className={`inline-flex items-center gap-1 mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium border ${genderBadge}`}>
                {genderIcon}
                {t(patient.gender)}
              </span>
            )}
          </div>
        </div>

        {/* Details grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
          <DetailItem icon={<Mail size={15} className="text-teal-500" />} label={t('email')} value={patient.email} />
          <DetailItem icon={<Phone size={15} className="text-teal-500" />} label={t('phone')} value={patient.phone} />
          <DetailItem icon={<Calendar size={15} className="text-teal-500" />} label={t('dateOfBirth')} value={patient.date_of_birth} />
          <DetailItem icon={<MapPin size={15} className="text-teal-500" />} label={t('address')} value={patient.address} />
          <DetailItem
            icon={<AlertCircle size={15} className="text-amber-500" />}
            label={t('emergencyContact')}
            value={patient.emergency_contact}
            className="sm:col-span-2"
          />
        </div>

        {/* Actions */}
        {actions && (
          <div className="pt-4 border-t border-slate-100 flex gap-3">
            {actions}
          </div>
        )}
      </div>
    </div>
  );
}

function DetailItem({ icon, label, value, className = '' }) {
  return (
    <div className={`flex items-start gap-3 ${className}`}>
      <div className="mt-0.5 flex-shrink-0">{icon}</div>
      <div className="min-w-0">
        <p className="text-xs text-slate-400 font-medium uppercase tracking-wide mb-0.5">{label}</p>
        <p className="text-sm text-slate-700 font-medium truncate">{value ?? <span className="text-slate-300">{useTranslation().t('notProvided')}</span>}</p>
      </div>
    </div>
  );
}
