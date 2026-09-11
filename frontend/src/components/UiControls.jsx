import React, { useState } from 'react';
import { Globe, Moon, Sun } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import i18n, { applyLanguage, applyTheme, THEME_KEY } from '../i18n';

export default function UiControls() {
  const { t } = useTranslation();
  const [theme, setTheme] = useState(() => localStorage.getItem(THEME_KEY) === 'dark' ? 'dark' : 'light');
  const nextLanguage = i18n.language === 'ar' ? 'en' : 'ar';
  const nextTheme = theme === 'dark' ? 'light' : 'dark';

  const toggleLanguage = () => {
    applyLanguage(nextLanguage);
    i18n.changeLanguage(nextLanguage);
  };

  const toggleTheme = () => {
    applyTheme(nextTheme);
    setTheme(nextTheme);
  };

  return (
    <div className="flex items-center gap-1 rounded-2xl border border-slate-200 bg-white/90 p-1 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/90">
      <button
        type="button"
        onClick={toggleLanguage}
        className="flex h-9 items-center gap-1.5 rounded-xl px-2.5 text-xs font-bold text-slate-600 transition hover:bg-teal-50 hover:text-teal-700 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-teal-300"
        aria-label={i18n.language === 'ar' ? t('switchToEnglish') : t('switchToArabic')}
        title={i18n.language === 'ar' ? t('switchToEnglish') : t('switchToArabic')}
      >
        <Globe size={16} />
        <span>{i18n.language === 'ar' ? 'EN' : 'AR'}</span>
      </button>
      <button
        type="button"
        onClick={toggleTheme}
        className="flex h-9 w-9 items-center justify-center rounded-xl text-slate-600 transition hover:bg-teal-50 hover:text-teal-700 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-teal-300"
        aria-label={theme === 'dark' ? t('switchToLight') : t('switchToDark')}
        title={theme === 'dark' ? t('switchToLight') : t('switchToDark')}
      >
        {theme === 'dark' ? <Sun size={17} /> : <Moon size={17} />}
      </button>
    </div>
  );
}