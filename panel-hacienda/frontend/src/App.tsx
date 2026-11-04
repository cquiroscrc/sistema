import { useCallback, useEffect, useState } from 'react';
import { LayoutDashboard, ScrollText, FileText, Hourglass, Power, FolderSync, Moon, Sun, LogOut, Menu, Receipt } from 'lucide-react';
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
import RutasDEData from './pages/RutasDEData';

export type Vista = 'inicio' | 'registro' | 'comprobantes' | 'cola' | 'servicio' | 'rutas';
const NAV: { id: Vista; label: string; icon: any }[] = [
  { id: 'inicio', label: 'Resumen', icon: LayoutDashboard },
  { id: 'comprobantes', label: 'Comprobantes', icon: FileText },
  { id: 'registro', label: 'Registro en vivo', icon: ScrollText },
  { id: 'cola', label: 'Cola de envío', icon: Hourglass },
  { id: 'servicio', label: 'Servicio de envío', icon: Power },
  { id: 'rutas', label: 'Rutas y DEData', icon: FolderSync },
];
const vistaDeHash = (): Vista => { const h = location.hash.replace('#/', '') as Vista; return NAV.some(n => n.id === h) ? h : 'inicio'; };

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
  useEffect(() => { if (!usuario) return; cargarStatus(); const t = setInterval(() => !document.hidden && cargarStatus(), 15000); return () => clearInterval(t); }, [usuario, cargarStatus]);

  const tema = () => { const d = !oscuro; setOscuro(d); document.documentElement.classList.toggle('dark', d); localStorage.setItem('ph-tema', d ? 'oscuro' : 'claro'); };
  const salir = async () => { await api('logout', {}, {}).catch(() => {}); setUsuario(null); const s = await api('sesion'); setCsrf(s.csrf || ''); };
  const ir = (v: Vista) => { location.hash = '#/' + v; setMenu(false); };

  if (usuario === undefined) return <div className="flex h-screen items-center justify-center"><Spinner /></div>;
  if (usuario === null) return <Login onOk={(u) => setUsuario(u)} oscuro={oscuro} onTema={tema} />;

  const activo = !!status?.servicio_activo;
  return <div className="flex min-h-screen">
    <aside className={cn('fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-sidebar text-sidebar-foreground transition-transform lg:translate-x-0', menu ? 'translate-x-0' : '-translate-x-full')}>
      <div className="flex items-center gap-3 px-5 py-5">
        <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-sidebar-accent text-white shadow"><Receipt className="h-5 w-5" /></div>
        <div><div className="text-sm font-semibold text-white">Panel Hacienda</div><div className="text-xs opacity-70">Los Paticas · api44</div></div>
      </div>
      <nav className="flex-1 space-y-1 px-3">{NAV.map(n => <button key={n.id} onClick={() => ir(n.id)}
        className={cn('flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors', vista === n.id ? 'bg-white/10 text-white font-medium' : 'hover:bg-white/5 hover:text-white')}>
        <n.icon className={cn('h-4 w-4', vista === n.id && 'text-sidebar-accent')} />{n.label}
        {n.id === 'servicio' && <span className={cn('ml-auto h-2 w-2 rounded-full', activo ? 'bg-emerald-400 shadow-[0_0_8px] shadow-emerald-400' : 'bg-slate-500')} />}
      </button>)}</nav>
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
        <h1 className="text-base font-semibold">{NAV.find(n => n.id === vista)?.label}</h1>
        <div className="ml-auto flex items-center gap-3 text-xs text-muted-foreground">
          {status && <span className="hidden md:inline">BD <b className="text-foreground">{status.bd.database}</b> @ {status.bd.host}{status.bd.restaurada && <> · restaurada {status.bd.restaurada}</>}</span>}
          <span className={cn('inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-medium ring-1 ring-inset', activo ? 'bg-success/10 text-success ring-success/30' : 'bg-muted text-muted-foreground ring-border')}>
            <span className={cn('h-1.5 w-1.5 rounded-full', activo ? 'bg-success animate-pulse' : 'bg-muted-foreground')} />{activo ? 'Envío activo' : 'Envío detenido'}</span>
          <button onClick={tema} className="rounded-md p-2 hover:bg-muted" aria-label="Tema">{oscuro ? <Sun className="h-4 w-4" /> : <Moon className="h-4 w-4" />}</button>
        </div>
      </header>
      <main className="min-w-0 flex-1 p-4 lg:p-8">
        <ErrorBoundary key={vista} nombre={NAV.find(n => n.id === vista)?.label ?? vista}>
          {vista === 'inicio' && <Dashboard status={status} ir={ir} />}
          {vista === 'registro' && <Registro />}
          {vista === 'comprobantes' && <Comprobantes status={status} onCambio={cargarStatus} />}
          {vista === 'cola' && <Cola />}
          {vista === 'servicio' && <Servicio status={status} onCambio={cargarStatus} />}
          {vista === 'rutas' && <RutasDEData />}
        </ErrorBoundary>
      </main>
    </div>
  </div>;
}
