import React from 'react';
import { Navigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useTranslation } from 'react-i18next';

/**
 * RoleGuard
 * Wraps a route and redirects if the authenticated user's role
 * is not in the allowed `roles` array.
 *
 * Usage:
 *   <RoleGuard roles={['admin']}>
 *     <AdminPage />
 *   </RoleGuard>
 */
export default function RoleGuard({ roles = [], redirectTo = '/', children }) {
  const { t } = useTranslation();
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-slate-50">
        <div className="flex flex-col items-center gap-3">
          <div className="w-10 h-10 border-4 border-teal-500 border-t-transparent rounded-full animate-spin" />
          <p className="text-slate-500 text-sm">{t('verifyingAccess')}</p>
        </div>
      </div>
    );
  }

  if (!user) return <Navigate to="/login" replace />;

  if (roles.length > 0 && !roles.includes(user.role)) {
    return <Navigate to={redirectTo} replace />;
  }

  return children;
}
