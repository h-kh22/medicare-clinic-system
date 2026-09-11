import React from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { useTranslation } from 'react-i18next';

/**
 * Pagination
 * Displays pagination controls based on Laravel pagination meta.
 *
 * Props:
 * - meta: { current_page, last_page, total, per_page }
 * - onPageChange: (page: number) => void
 */
export default function Pagination({ meta, onPageChange }) {
  const { t } = useTranslation();
  if (!meta || meta.last_page <= 1) return null;

  const { current_page, last_page, total } = meta;

  const pages = Array.from({ length: last_page }, (_, i) => i + 1);

  // Show at most 5 pages centered around current page
  const getVisiblePages = () => {
    if (last_page <= 5) return pages;
    const start = Math.max(1, current_page - 2);
    const end = Math.min(last_page, start + 4);
    return pages.slice(start - 1, end);
  };

  const visible = getVisiblePages();

  return (
    <div className="flex items-center justify-between px-1 py-3">
      <p className="text-xs text-slate-500">
        {t('showingPage')} <span className="font-medium text-slate-700">{current_page}</span> {t('of')}{' '}
        <span className="font-medium text-slate-700">{last_page}</span>
        {' '}&mdash; <span className="font-medium">{total}</span> {t('total')}
      </p>

      <div className="flex items-center gap-1">
        <button
          onClick={() => onPageChange(current_page - 1)}
          disabled={current_page === 1}
          className="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50
                     disabled:opacity-40 disabled:cursor-not-allowed transition"
          aria-label={t('previousPage')}
        >
          <ChevronLeft size={16} />
        </button>

        {visible[0] > 1 && (
          <>
            <button onClick={() => onPageChange(1)} className="page-btn">1</button>
            {visible[0] > 2 && <span className="px-1 text-slate-400">…</span>}
          </>
        )}

        {visible.map((p) => (
          <button
            key={p}
            onClick={() => onPageChange(p)}
            className={`w-8 h-8 rounded-lg text-sm font-medium transition border
              ${p === current_page
                ? 'bg-teal-600 text-white border-teal-600 shadow-sm'
                : 'border-slate-200 text-slate-600 hover:bg-slate-50'
              }`}
          >
            {p}
          </button>
        ))}

        {visible[visible.length - 1] < last_page && (
          <>
            {visible[visible.length - 1] < last_page - 1 && (
              <span className="px-1 text-slate-400">…</span>
            )}
            <button onClick={() => onPageChange(last_page)} className="page-btn">{last_page}</button>
          </>
        )}

        <button
          onClick={() => onPageChange(current_page + 1)}
          disabled={current_page === last_page}
          className="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50
                     disabled:opacity-40 disabled:cursor-not-allowed transition"
          aria-label={t('nextPage')}
        >
          <ChevronRight size={16} />
        </button>
      </div>
    </div>
  );
}
