export const fmtBytes = (b?: number | null) => {
  if (b === null || b === undefined || isNaN(b)) return '—';
  const u = ['B', 'KB', 'MB', 'GB', 'TB']; let i = 0; let v = b;
  while (v >= 1024 && i < u.length - 1) { v /= 1024; i++; }
  return `${v.toLocaleString('es-CR', { maximumFractionDigits: i >= 3 ? 2 : 0 })} ${u[i]}`;
};
