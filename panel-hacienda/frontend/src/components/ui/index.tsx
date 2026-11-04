import * as React from 'react';
import { X, CheckCircle2, AlertTriangle, Info, XCircle, Loader2 } from 'lucide-react';
import { cn } from '../../lib/utils';

/* ---------- Button ---------- */
type Variant = 'primary' | 'secondary' | 'ghost' | 'danger' | 'outline' | 'success';
const vClass: Record<Variant, string> = {
  primary: 'bg-primary text-primary-foreground hover:bg-primary/90 shadow-sm',
  secondary: 'bg-muted text-foreground hover:bg-muted/70',
  ghost: 'hover:bg-muted text-foreground',
  outline: 'border bg-card hover:bg-muted text-foreground',
  danger: 'bg-danger text-white hover:bg-danger/90 shadow-sm',
  success: 'bg-success text-white hover:bg-success/90 shadow-sm',
};
export const Button = React.forwardRef<HTMLButtonElement, React.ButtonHTMLAttributes<HTMLButtonElement> & { variant?: Variant; size?: 'sm' | 'md' | 'icon'; loading?: boolean }>(
  ({ className, variant = 'primary', size = 'md', loading, children, disabled, ...p }, ref) => (
    <button ref={ref} disabled={disabled || loading}
      className={cn('inline-flex items-center justify-center gap-2 rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 ring-offset-background disabled:opacity-50 disabled:pointer-events-none whitespace-nowrap',
        size === 'sm' ? 'h-8 px-3 text-xs' : size === 'icon' ? 'h-9 w-9' : 'h-9 px-4 text-sm', vClass[variant], className)} {...p}>
      {loading && <Loader2 className="h-4 w-4 animate-spin" />}{children}
    </button>));
Button.displayName = 'Button';

/* ---------- Card ---------- */
export const Card = ({ className, ...p }: React.HTMLAttributes<HTMLDivElement>) => <div className={cn('rounded-lg border bg-card text-card-foreground shadow-soft', className)} {...p} />;
export const CardHeader = ({ className, ...p }: React.HTMLAttributes<HTMLDivElement>) => <div className={cn('flex items-center justify-between gap-3 border-b px-5 py-3.5', className)} {...p} />;
export const CardTitle = ({ className, ...p }: React.HTMLAttributes<HTMLHeadingElement>) => <h3 className={cn('text-sm font-semibold tracking-tight', className)} {...p} />;
export const CardBody = ({ className, ...p }: React.HTMLAttributes<HTMLDivElement>) => <div className={cn('p-5', className)} {...p} />;

/* ---------- Inputs ---------- */
const field = 'h-9 w-full rounded-md border border-input bg-card px-3 text-sm shadow-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring/40 focus:border-ring';
export const Input = React.forwardRef<HTMLInputElement, React.InputHTMLAttributes<HTMLInputElement>>(({ className, ...p }, ref) => <input ref={ref} className={cn(field, className)} {...p} />);
Input.displayName = 'Input';
export const Select = ({ className, ...p }: React.SelectHTMLAttributes<HTMLSelectElement>) => <select className={cn(field, 'pr-8', className)} {...p} />;
export const Label = ({ className, ...p }: React.LabelHTMLAttributes<HTMLLabelElement>) => <label className={cn('mb-1 block text-xs font-medium text-muted-foreground', className)} {...p} />;
export const Switch = ({ checked, onChange, label }: { checked: boolean; onChange: (v: boolean) => void; label: string }) => (
  <button type="button" role="switch" aria-checked={checked} onClick={() => onChange(!checked)} className="inline-flex items-center gap-2 text-sm">
    <span className={cn('relative h-5 w-9 rounded-full transition-colors', checked ? 'bg-primary' : 'bg-muted-foreground/30')}>
      <span className={cn('absolute top-0.5 h-4 w-4 rounded-full bg-white shadow transition-transform', checked ? 'translate-x-4' : 'translate-x-0.5')} />
    </span>{label}
  </button>);

/* ---------- Badge de estado ---------- */
const est: Record<string, { c: string; t: string }> = {
  aceptado: { c: 'bg-success/15 text-success ring-success/25', t: 'Aceptado' },
  rechazado: { c: 'bg-danger/10 text-danger ring-danger/25', t: 'Rechazado' },
  error: { c: 'bg-danger/10 text-danger ring-danger/30', t: 'Error' },
  pendiente: { c: 'bg-warning/15 text-warning ring-warning/30', t: 'Pendiente' },
  enviando: { c: 'bg-info/15 text-info ring-info/30', t: 'Enviando' },
  procesando: { c: 'bg-info/15 text-info ring-info/30', t: 'Enviando' },
  reintentar: { c: 'bg-info/15 text-info ring-info/30', t: 'Reintento' },
};
export const EstadoBadge = ({ estado }: { estado: string }) => {
  const e = est[estado] ?? { c: 'bg-muted text-muted-foreground ring-border', t: estado || '—' };
  return <span className={cn('inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset', e.c)}>
    <span className="h-1.5 w-1.5 rounded-full bg-current" />{e.t}</span>;
};
export const Pill = ({ className, ...p }: React.HTMLAttributes<HTMLSpanElement>) => <span className={cn('inline-flex items-center rounded-md bg-muted px-2 py-0.5 text-xs font-medium text-muted-foreground', className)} {...p} />;

/* ---------- Modal / Drawer ---------- */
function useEsc(open: boolean, onClose: () => void) {
  React.useEffect(() => { if (!open) return; const h = (e: KeyboardEvent) => e.key === 'Escape' && onClose(); window.addEventListener('keydown', h); return () => window.removeEventListener('keydown', h); }, [open, onClose]);
}
export function Modal({ open, onClose, title, children, footer, tone }: { open: boolean; onClose: () => void; title: React.ReactNode; children: React.ReactNode; footer?: React.ReactNode; tone?: 'danger' | 'warning' }) {
  useEsc(open, onClose);
  if (!open) return null;
  return <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div className="absolute inset-0 bg-black/50 backdrop-blur-sm" onClick={onClose} />
    <div role="dialog" aria-modal className={cn('relative w-full max-w-lg rounded-xl border bg-card shadow-2xl', tone === 'danger' && 'border-danger/40', tone === 'warning' && 'border-warning/50')}>
      <div className="flex items-center justify-between border-b px-5 py-4"><h2 className="font-semibold">{title}</h2>
        <button onClick={onClose} className="rounded-md p-1 text-muted-foreground hover:bg-muted" aria-label="Cerrar"><X className="h-4 w-4" /></button></div>
      <div className="px-5 py-4 text-sm">{children}</div>
      {footer && <div className="flex justify-end gap-2 border-t bg-muted/40 px-5 py-3 rounded-b-xl">{footer}</div>}
    </div></div>;
}
export function Drawer({ open, onClose, title, children, footer }: { open: boolean; onClose: () => void; title: React.ReactNode; children: React.ReactNode; footer?: React.ReactNode }) {
  useEsc(open, onClose);
  return <div className={cn('fixed inset-0 z-40', open ? '' : 'pointer-events-none')}>
    <div className={cn('absolute inset-0 bg-black/40 transition-opacity', open ? 'opacity-100' : 'opacity-0')} onClick={onClose} />
    <aside className={cn('absolute right-0 top-0 flex h-full w-full max-w-4xl flex-col border-l bg-card shadow-2xl transition-transform duration-300', open ? 'translate-x-0' : 'translate-x-full')}>
      <div className="flex items-center justify-between gap-3 border-b px-6 py-4">{title}
        <button onClick={onClose} className="rounded-md p-1.5 text-muted-foreground hover:bg-muted" aria-label="Cerrar"><X className="h-5 w-5" /></button></div>
      <div className="flex-1 overflow-y-auto px-6 py-5 scrollbar-thin">{children}</div>
      {footer && <div className="flex flex-wrap items-center gap-2 border-t bg-muted/40 px-6 py-3">{footer}</div>}
    </aside></div>;
}

/* ---------- Tabs ---------- */
export function Tabs<T extends string>({ value, onChange, items }: { value: T; onChange: (v: T) => void; items: { id: T; label: React.ReactNode }[] }) {
  return <div className="inline-flex rounded-lg bg-muted p-1">{items.map(i =>
    <button key={i.id} onClick={() => onChange(i.id)} className={cn('rounded-md px-3 py-1.5 text-xs font-medium transition-colors', value === i.id ? 'bg-card shadow-sm text-foreground' : 'text-muted-foreground hover:text-foreground')}>{i.label}</button>)}</div>;
}

/* ---------- Toasts ---------- */
type Toast = { id: number; tipo: 'ok' | 'error' | 'aviso' | 'info'; texto: string };
const ToastCtx = React.createContext<(tipo: Toast['tipo'], texto: string) => void>(() => {});
export const useToast = () => React.useContext(ToastCtx);
export function ToastProvider({ children }: { children: React.ReactNode }) {
  const [items, setItems] = React.useState<Toast[]>([]);
  const push = React.useCallback((tipo: Toast['tipo'], texto: string) => {
    const id = Date.now() + Math.random(); setItems(s => [...s, { id, tipo, texto }]);
    setTimeout(() => setItems(s => s.filter(t => t.id !== id)), tipo === 'error' ? 8000 : 4500);
  }, []);
  const icon = { ok: <CheckCircle2 className="h-5 w-5 text-success" />, error: <XCircle className="h-5 w-5 text-danger" />, aviso: <AlertTriangle className="h-5 w-5 text-warning" />, info: <Info className="h-5 w-5 text-info" /> };
  return <ToastCtx.Provider value={push}>{children}
    <div className="fixed bottom-4 right-4 z-[60] flex w-96 max-w-[calc(100vw-2rem)] flex-col gap-2">
      {items.map(t => <div key={t.id} className="flex items-start gap-3 rounded-lg border bg-card p-3.5 text-sm shadow-xl animate-in">{icon[t.tipo]}<div className="flex-1">{t.texto}</div>
        <button onClick={() => setItems(s => s.filter(x => x.id !== t.id))} className="text-muted-foreground"><X className="h-4 w-4" /></button></div>)}
    </div></ToastCtx.Provider>;
}

export const Empty = ({ children }: { children: React.ReactNode }) => <div className="py-10 text-center text-sm text-muted-foreground">{children}</div>;
export const Spinner = () => <Loader2 className="h-5 w-5 animate-spin text-muted-foreground" />;
