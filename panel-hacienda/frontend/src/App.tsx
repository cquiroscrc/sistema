import { useCallback, useEffect, useState } from 'react';
import { LayoutDashboard, ScrollText, FileText, Hourglass, Power, CalendarClock, Settings, Moon, Sun, LogOut, Menu, Receipt, Inbox, ListChecks, ShieldCheck, Server } from 'lucide-react';
import { api, setCsrf, setOnUnauthorized, type Status } from './lib/api';
import { cn } from './lib/utils';
import { Spinner, useToast } from './components/ui';
import Login from './pages/Login';
import { ErrorBoundary } from './components/ErrorBoundary';
import Dashboard from './pages/Dashboard';
import Registro from './pages/Registro';
import Comprobantes from './pages/Comprobantes';
import Cola from './pages/Cola';
import Servicio from './pages/Servicio';
import Tareas from './pages/Tareas';
import Configuracion, { type Pestana } from './pages/Configuracion';
import BuzonFE from './pages/BuzonFE';
import MigracionesAviso from './components/MigracionesAviso';

export type Vista = 'inicio' | 'comprobantes' | 'comprobantes/cola' | 'servicios/envio' | 'servicios/recepcion' | 'tareas' | 'auditoria/logs'
  | 'configuracion' | 'configuracion/respaldos' | 'configuracion/rutas' | 'configuracion/certificados' | 'configuracion/bitacora';
type Item = { id: Vista; label: string; icon: any };
type Grupo = { label: string; icon: any; hijos: Item[] };
const MENU: (Item | Grupo)[] = [
  { id: 'inicio', label: 'Resumen', icon: LayoutDashboard },
  { label: 'Comprobantes', icon: FileText, hijos: [
    { id: 'comprobantes', label: 'Registro de envíos', icon: ListChecks },
    { id: 'comprobantes/cola', label: 'Cola de envío', icon: Hourglass },
  ] },
  { label: 'Servicios', icon: Server, hijos: [
    { id: 'servicios/envio', label: 'Envío', icon: Power },
    { id: 'servicios/recepcion', label: 'Recepción', icon: Inbox },
  ] },
  { id: 'tareas', label: 'Tareas programadas', icon: CalendarClock },
  { label: 'Auditoría', icon: ShieldCheck, hijos: [{ id: 'auditoria/logs', label: 'Logs', icon: ScrollText }] },
  { id: 'configuracion', label: 'Configuración', icon: Settings },
];
const NAV: Item[] = MENU.flatMap(m => 'hijos' in m ? m.hijos : [m]);
// Pestañas de Configuración con ruta propia (no salen en el menú)
const EXTRA: Item[] = [
  { id: 'configuracion/respaldos', label: 'Configuración', icon: Settings },
  { id: 'configuracion/rutas', label: 'Configuración', icon: Settings },
  { id: 'configuracion/certificados', label: 'Configuración', icon: Settings },
];
// Rutas viejas -> nuevas
const ALIAS: Record<string, Vista> = { registro: 'auditoria/logs', cola: 'comprobantes/cola', servicio: 'servicios/envio', buzon: 'servicios/recepcion', rutas: 'configuracion/rutas', dedata: 'configuracion/rutas' };
const vistaDeHash = (): Vista => {
  const h = location.hash.replace(/^#\/?/, '').split('?')[0];
  if (ALIAS[h]) { history.replaceState(null, '', '#/' + ALIAS[h]); return ALIAS[h]; }
  return [...NAV, ...EXTRA].some(n => n.id === h) ? h as Vista : 'inicio';
};
const navActivo = (n: Item, v: Vista) => n.id === v || (n.id === 'configuracion' && v.startsWith('configuracion/'));
const pestanaDe = (v: Vista): Pestana => v === 'configuracion/respaldos' ? 'respaldos' : v === 'configuracion/rutas' ? 'rutas' : v === 'configuracion/certificados' ? 'certificados' : v === 'configuracion/bitacora' ? 'bitacora' : 'correo';

export default function App() {
  const toast = useToast();
  const [usuario, setUsuario] = useState<{ usuario: string; nombre: string } | null | undefined>(undefined);
  const [vista, setVista] = useState<Vista>(vistaDeHash());
  const [status, setStatus] = useState<Status | null>(null);
  const [oscuro, setOscuro] = useState(document.documentElement.classList.contains('dark'));
  const [menu, setMenu] = useState(false);

  useEffect(() => {
    setOnUnauthorized(() => { setUsuario(null); toast('aviso', 'La sesión expiró. Ingrese de nuevo.'); });
    api('sesion').then(j => { setCsrf(j.csrf || ''); setUsuario(j.autenticado ? { usuario: j.usuario, nombre: j.nombre } : null); }).catch(() => setUsuario(null));
    const h = () => setVista(vistaDeHash()); window.addEventListener('hashchange', h); return () => window.removeEventListener('hashchange', h);
  }, [toast]);

  const cargarStatus = useCallback(async () => { try { const s = await api<Status>('status'); if (s.ok) setStatus(s); } catch { /* 401 manejado */ } }, []);
  useEffect(() => { if (!usuario || vista === 'inicio') return; cargarStatus(); const t = setInterval(() => !document.hidden && cargarStatus(), 15000); return () => clearInterval(t); }, [usuario, cargarStatus, vista]);

  const tema = () => { const d = !oscuro; setOscuro(d); document.documentElement.classList.toggle('dark', d); localStorage.setItem('ph-tema', d ? 'oscuro' : 'claro'); };
  const salir = async () => { await api('logout', {}, {}).catch(() => {}); setUsuario(null); const s = await api('sesion'); setCsrf(s.csrf || ''); };
  const ir = (v: Vista) => { location.hash = '#/' + v; setMenu(false); };
  const itemNav = (n: Item, sub = false) => <button key={n.id} onClick={() => ir(n.id)}
    className={cn('flex w-full items-center gap-3 rounded-md px-3 text-sm transition-colors', sub ? 'py-1.5' : 'py-2', navActivo(n, vista) ? 'bg-white/10 text-white font-medium' : 'hover:bg-white/5 hover:text-white')}>
    <n.icon className={cn('h-4 w-4', navActivo(n, vista) && 'text-sidebar-accent')} />{n.label}
    {n.id === 'servicios/envio' && <span className={cn('ml-auto h-2 w-2 rounded-full', !!status?.servicio_activo ? 'bg-emerald-400 shadow-[0_0_8px] shadow-emerald-400' : 'bg-slate-500')} />}
    {n.id === 'servicios/recepcion' && <span title={status?.buzon === 'activo' ? 'Buzón activo' : status?.buzon === 'error' ? 'Buzón con fallo' : 'Buzón detenido'} className={cn('ml-auto h-2 w-2 rounded-full', status?.buzon === 'activo' ? 'bg-emerald-400 shadow-[0_0_8px] shadow-emerald-400' : status?.buzon === 'error' ? 'bg-red-500 shadow-[0_0_8px] shadow-red-500' : 'bg-slate-500')} />}
  </button>;
  const grupo = MENU.find(m => 'hijos' in m && m.hijos.some(h => h.id === vista)) as Grupo | undefined;
  const titulo = (grupo ? grupo.label + ' · ' : '') + ([...NAV, ...EXTRA].find(n => n.id === vista)?.label ?? '');

  if (usuario === undefined) return <div className="flex h-screen items-center justify-center"><Spinner /></div>;
  if (usuario === null) return <Login onOk={(u) => setUsuario(u)} oscuro={oscuro} onTema={tema} />;

  const activo = !!status?.servicio_activo;
  return <div className="flex min-h-screen">
    <aside className={cn('fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-sidebar text-sidebar-foreground transition-transform lg:translate-x-0', menu ? 'translate-x-0' : '-translate-x-full')}>
      <div className="flex items-center gap-3 px-5 py-5">
        <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-sidebar-accent text-white shadow"><Receipt className="h-5 w-5" /></div>
        <div><div className="text-sm font-semibold text-white">Panel Hacienda</div><div className="text-xs opacity-70">Los Paticas · api44</div></div>
      </div>
      <nav className="flex-1 space-y-1 overflow-y-auto px-3">{MENU.map(m => 'hijos' in m
        ? <div key={m.label}>
            <button onClick={() => ir(m.hijos[0].id)} className={cn('flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors', m.hijos.some(h => h.id === vista) ? 'text-white font-medium' : 'hover:bg-white/5 hover:text-white')}>
              <m.icon className={cn('h-4 w-4', m.hijos.some(h => h.id === vista) && 'text-sidebar-accent')} />{m.label}</button>
            <div className="ml-5 space-y-0.5 border-l border-white/10 pl-2">{m.hijos.map(h => itemNav(h, true))}</div>
          </div>
        : itemNav(m))}</nav>
      <div className="m-3 rounded-lg bg-white/5 p-3 text-xs">
        <div className="font-medium text-white">{usuario.nombre || usuario.usuario}</div>
        <div className="opacity-70">{usuario.usuario}</div>
        <button onClick={salir} className="mt-2 inline-flex items-center gap-1.5 text-sidebar-accent hover:underline"><LogOut className="h-3.5 w-3.5" />Cerrar sesión</button>
      </div>
    </aside>
    {menu && <div className="fixed inset-0 z-20 bg-black/40 lg:hidden" onClick={() => setMenu(false)} />}
    <div className="flex min-w-0 flex-1 flex-col lg:pl-64">
      <header className="sticky top-0 z-10 flex h-14 items-center gap-3 border-b bg-background/80 px-4 backdrop-blur lg:px-8">
        <button className="lg:hidden" onClick={() => setMenu(true)} aria-label="Menú"><Menu className="h-5 w-5" /></button>
        <h1 className="text-base font-semibold">{titulo}</h1>
        <div className="ml-auto flex items-center gap-3 text-xs text-muted-foreground">
          {status && <span className="hidden md:inline">BD <b className="text-foreground">{status.bd.database}</b> @ {status.bd.host}{status.bd.restaurada && <> · restaurada {status.bd.restaurada}</>}</span>}
          <span className={cn('inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-medium ring-1 ring-inset', activo ? 'bg-success/10 text-success ring-success/30' : 'bg-muted text-muted-foreground ring-border')}>
            <span className={cn('h-1.5 w-1.5 rounded-full', activo ? 'bg-success animate-pulse' : 'bg-muted-foreground')} />{activo ? 'Envío activo' : 'Envío detenido'}</span>
          <button onClick={tema} className="rounded-md p-2 hover:bg-muted" aria-label="Tema">{oscuro ? <Sun className="h-4 w-4" /> : <Moon className="h-4 w-4" />}</button>
        </div>
      </header>
      <main className="min-w-0 flex-1 p-4 lg:p-8">
        <MigracionesAviso m={status?.migraciones} bd={status?.bd?.database} onCambio={cargarStatus} />
        <ErrorBoundary key={vista} nombre={titulo || vista}>
          {vista === 'inicio' && <Dashboard status={status} ir={ir} onStatus={setStatus} />}
          {vista === 'auditoria/logs' && <Registro />}
          {vista === 'comprobantes' && <Comprobantes status={status} onCambio={cargarStatus} />}
          {vista === 'comprobantes/cola' && <Cola />}
          {vista === 'servicios/envio' && <Servicio status={status} onCambio={cargarStatus} />}
          {vista === 'servicios/recepcion' && <BuzonFE />}
          {vista === 'tareas' && <Tareas onCambio={cargarStatus} />}
          {vista.startsWith('configuracion') && <Configuracion tab={pestanaDe(vista)} onTab={t => ir(t === 'correo' ? 'configuracion' : `configuracion/${t}` as Vista)} />}
        </ErrorBoundary>
      </main>
    </div>
  </div>;
}
