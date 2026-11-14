import { ArrowDown, ArrowUp, ArrowUpDown } from 'lucide-react';
import { cn } from '../lib/utils';

export type Orden = { col: string; dir: 'asc' | 'desc' };

/** Clic en la misma columna alterna asc/desc; en otra columna empieza ascendente. */
export const siguienteOrden = (o: Orden, col: string): Orden => (o.col === col ? { col, dir: o.dir === 'asc' ? 'desc' : 'asc' } : { col, dir: 'asc' });

export function SortTh({ label, col, orden, onSort, className }: { label: string; col?: string; orden: Orden; onSort: (col: string) => void; className?: string }) {
  if (!col) return <th className={cn('px-4 py-3 font-medium', className)}>{label}</th>;
  const activo = orden.col === col;
  const Icono = !activo ? ArrowUpDown : orden.dir === 'asc' ? ArrowUp : ArrowDown;
  return <th className={cn('px-4 py-3 font-medium', className)} aria-sort={activo ? (orden.dir === 'asc' ? 'ascending' : 'descending') : 'none'}>
    <button type="button" onClick={() => onSort(col)} title={`Ordenar por ${label.toLowerCase()}`}
      className={cn('inline-flex items-center gap-1 uppercase tracking-wide hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring rounded', activo && 'text-foreground')}>
      {label}<Icono className={cn('h-3.5 w-3.5', !activo && 'opacity-40')} />
    </button>
  </th>;
}
