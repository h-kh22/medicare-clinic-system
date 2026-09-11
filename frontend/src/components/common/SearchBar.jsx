import React, { useState, useEffect, useRef } from 'react';
import { Search, X } from 'lucide-react';
import { useTranslation } from 'react-i18next';

/**
 * SearchBar
 * Debounced search input that calls `onSearch` after 350ms idle time.
 *
 * Props:
 * - placeholder (string)
 * - onSearch (function) — receives the trimmed search string
 * - initialValue (string)
 */
export default function SearchBar({ placeholder = 'Search…', onSearch, initialValue = '' }) {
  const { t } = useTranslation();
  const [value, setValue] = useState(initialValue);
  const timerRef = useRef(null);

  useEffect(() => {
    // Debounce: only call onSearch after user stops typing for 350ms
    clearTimeout(timerRef.current);
    timerRef.current = setTimeout(() => {
      onSearch(value.trim());
    }, 350);

    return () => clearTimeout(timerRef.current);
  }, [value]);

  const handleClear = () => {
    setValue('');
    onSearch('');
  };

  return (
    <div className="relative w-full max-w-md">
      <Search
        className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
        size={16}
      />
      <input
        type="text"
        value={value}
        onChange={(e) => setValue(e.target.value)}
        placeholder={placeholder === 'Search…' ? t('search') : placeholder}
        className="w-full pl-9 pr-9 py-2.5 text-sm rounded-xl border border-slate-200 bg-white
                   shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent
                   transition placeholder-slate-400 text-slate-700"
      />
      {value && (
        <button
          onClick={handleClear}
          className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition"
          aria-label={t('clearSearch')}
        >
          <X size={14} />
        </button>
      )}
    </div>
  );
}
