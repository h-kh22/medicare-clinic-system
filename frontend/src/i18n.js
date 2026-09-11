import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import en from './locales/en.json';
import ar from './locales/ar.json';

export const LANGUAGE_KEY = 'medicare_language';
export const THEME_KEY = 'medicare_theme';

const storedLanguage = localStorage.getItem(LANGUAGE_KEY);
const initialLanguage = storedLanguage === 'ar' ? 'ar' : 'en';

export function applyLanguage(language) {
  document.documentElement.lang = language;
  document.documentElement.dir = language === 'ar' ? 'rtl' : 'ltr';
  localStorage.setItem(LANGUAGE_KEY, language);
}

export function applyTheme(theme) {
  document.documentElement.classList.toggle('dark', theme === 'dark');
  localStorage.setItem(THEME_KEY, theme);
}

i18n
  .use(initReactI18next)
  .init({
    resources: { en: { translation: en }, ar: { translation: ar } },
    lng: initialLanguage,
    fallbackLng: 'en',
    interpolation: { escapeValue: false },
  });

applyLanguage(initialLanguage);
applyTheme(localStorage.getItem(THEME_KEY) === 'dark' ? 'dark' : 'light');

export default i18n;