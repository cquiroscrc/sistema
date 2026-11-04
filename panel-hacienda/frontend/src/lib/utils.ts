import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export const cn = (...v: ClassValue[]) => twMerge(clsx(v));
export const fmtMonto = (n: number | null | undefined) =>
  n == null ? '' : '₡' + Number(n).toLocaleString('es-CR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
export const fmtNum = (n: number | null | undefined) => (n == null ? '—' : Number(n).toLocaleString('es-CR'));
