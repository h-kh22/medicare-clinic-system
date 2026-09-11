import React, { useState } from 'react';
import { NavLink, useNavigate } from 'react-router-dom';
import {
  LayoutDashboard, Users, UserRound, Stethoscope, Plus,
  LogOut, Menu, X, ChevronRight, Activity
} from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { useAuth } from '../context/AuthContext';
import UiControls from '../components/UiControls';

const navItemsByRole = {
  admin: [
    { to: '/admin/dashboard', icon: LayoutDashboard, label: 'dashboard' },
    { to: '/admin/patients', icon: Users, label: 'patients' },
    { to: '/admin/doctors', icon: Stethoscope, label: 'doctors' },
    { to: '/admin/specialties', icon: Activity, label: 'specialties' },
    { to: '/admin/appointments', icon: Activity, label: 'appointments' },
  ],
  doctor: [
    { to: '/doctor/dashboard', icon: LayoutDashboard, label: 'dashboard' },
    { to: '/doctor/appointments', icon: Activity, label: 'appointments' },
    { to: '/doctor/patients', icon: Users, label: 'patients' },
    { to: '/doctor/records', icon: Activity, label: 'medicalRecords' },
    { to: '/doctor/prescriptions', icon: Stethoscope, label: 'prescriptions' },
  ],
  patient: [
    { to: '/patient/dashboard', icon: LayoutDashboard, label: 'dashboard' },
    { to: '/patient/book-appointment', icon: Plus, label: 'bookAppointment' },
    { to: '/patient/appointments', icon: Activity, label: 'appointments' },
    { to: '/patient/records', icon: Activity, label: 'medicalRecords' },
    { to: '/patient/prescriptions', icon: Stethoscope, label: 'prescriptions' },
    { to: '/patient/profile', icon: UserRound, label: 'profile' },
  ],
};

/**
 * AdminLayout
 * Full admin layout with collapsible sidebar and topbar.
 */
export default function AdminLayout({ children }) {
  const { user, logout } = useAuth();
  const { t } = useTranslation();
  const navItems = navItemsByRole[user?.role] || navItemsByRole.patient;
  const navigate = useNavigate();
  const [sidebarOpen, setSidebarOpen] = useState(true);

  const handleLogout = async () => {
    await logout();
    navigate('/');
  };

  return (
    <div className="flex min-h-screen bg-slate-50 dark:bg-slate-950">
      {/* ── Sidebar ── */}
      <aside
        className={`flex flex-col bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900
                    text-white shadow-2xl transition-all duration-300 flex-shrink-0
                    ${sidebarOpen ? 'w-64' : 'w-16'}`}
      >
        {/* Logo */}
        <div className="flex items-center gap-3 px-4 py-5 border-b border-slate-700/50">
          <div className="w-9 h-9 bg-teal-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
            <Activity size={20} className="text-white" />
          </div>
          {sidebarOpen && (
            <div className="overflow-hidden">
              <p className="font-bold text-white text-base leading-none">MediCare</p>
              <p className="text-teal-400 text-xs mt-0.5">{user?.role ? t('rolePortal', { role: user.role }) : t('clinicPortal')}</p>
            </div>
          )}
        </div>

        {/* Nav items */}
        <nav className="flex-1 py-4 px-2 space-y-1">
          {navItems.map(({ to, icon: Icon, label }) => (
            <NavLink
              key={to}
              to={to}
              className={({ isActive }) =>
                `flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                 ${isActive
                   ? 'bg-teal-500 text-white shadow-lg shadow-teal-500/20'
                   : 'text-slate-400 hover:bg-slate-700/50 hover:text-white'
                 }`
              }
            >
              <Icon size={18} className="flex-shrink-0" />
              {sidebarOpen && <span className="truncate">{t(label)}</span>}
            </NavLink>
          ))}
        </nav>

        {/* User info + logout */}
        <div className="border-t border-slate-700/50 p-3">
          {sidebarOpen ? (
            <div className="flex items-center gap-3 px-2 py-2">
              <div className="w-8 h-8 rounded-lg bg-teal-500 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                {user?.name?.charAt(0)?.toUpperCase()}
              </div>
              <div className="flex-1 min-w-0">
                <p className="text-sm font-medium text-white truncate">{user?.name}</p>
                <p className="text-xs text-slate-400 truncate">{user?.email}</p>
              </div>
              <button
                onClick={handleLogout}
                className="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 transition"
                title={t('logout')}
              >
                <LogOut size={15} />
              </button>
            </div>
          ) : (
            <button
              onClick={handleLogout}
              className="w-full flex justify-center p-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-700 transition"
              title={t('logout')}
            >
              <LogOut size={18} />
            </button>
          )}
        </div>
      </aside>

      {/* ── Main area ── */}
      <div className="flex-1 flex flex-col min-w-0">
        {/* Topbar */}
        <header className="flex items-center gap-4 border-b border-slate-100 bg-white px-6 py-3.5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <button
            onClick={() => setSidebarOpen(!sidebarOpen)}
            className="p-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition"
            aria-label={t('toggleSidebar')}
          >
            {sidebarOpen ? <X size={18} /> : <Menu size={18} />}
          </button>
          <div className="flex-1" />
          <UiControls />
          <div className="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
            <span className="rounded-full border border-teal-100 bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-700 dark:border-teal-800 dark:bg-teal-950/50 dark:text-teal-300">
              {user?.role}
            </span>
            <span className="font-medium">{user?.name}</span>
          </div>
        </header>

        {/* Page content */}
        <main className="flex-1 p-6 overflow-auto">
          {children}
        </main>
      </div>
    </div>
  );
}
