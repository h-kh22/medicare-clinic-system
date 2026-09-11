import React from 'react';
import { Activity, ShieldCheck } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import UiControls from './UiControls';

export default function Navbar() {
  const { t } = useTranslation();

  return (
    <header className="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur-md dark:border-slate-700 dark:bg-slate-950/90">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div className="flex items-center space-x-3">
          <div className="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center shadow-md shadow-teal-500/20">
            <Activity className="w-6 h-6" />
          </div>
          <div>
            <span className="text-xl font-bold tracking-tight text-slate-900 dark:text-white">
              {t('brand')}
            </span>
            <span className="hidden sm:inline-block ml-2 text-xs font-medium px-2 py-0.5 rounded-full bg-teal-50 text-teal-700 border border-teal-200 dark:bg-teal-950/50 dark:text-teal-300 dark:border-teal-800">
              {t('clinicManagement')}
            </span>
          </div>
        </div>

        <div className="flex items-center gap-3">
          <div className="hidden items-center space-x-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs text-slate-600 dark:bg-slate-800 dark:text-slate-300 sm:flex">
            <ShieldCheck className="w-4 h-4 text-teal-600" />
            <span className="font-medium">{t('systemCore')}</span>
          </div>
          <UiControls />
        </div>
      </div>
    </header>
  );
}
