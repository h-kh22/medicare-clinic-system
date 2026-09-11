import React from 'react';
import Navbar from '../components/Navbar';
import { useTranslation } from 'react-i18next';

export default function MainLayout({ children }) {
  const { t } = useTranslation();
  return (
    <div className="flex min-h-screen flex-col bg-gradient-to-b from-slate-50 via-teal-50/20 to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
      <Navbar />
      <main className="flex-1">
        {children}
      </main>
      <footer className="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
        <p>&copy; {new Date().getFullYear()} {t('footerCopyright')}</p>
      </footer>
    </div>
  );
}
