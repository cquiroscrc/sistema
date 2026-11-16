import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { Pause, Play, Search, ArrowDownToLine, Trash2, ChevronDown, ChevronRight, ChevronLeft, ChevronsLeft, AlertTriangle, X, ArrowUp, ArrowDown, ArrowUpDown } from 'lucide-react';
import { api, type LogRow } from '../lib/api';
import { useMantenerSesion } from '../lib/keepalive';
import { cn } from '../lib/utils';
import { Button, Card, Input, Label, Select, Switch, useToast } from '../components/ui';

const FUENTES = [
  { id: 'bitacora', label: 'Bitácora (todo)' },
  { id: 'bitacora_fe', label: 'Bitácora FE' },
  { id: 'bitacora_panel', label: 'Bitácora Panel' },
  { id: 'sender', label: 'Salida del servicio (sender.log)' },
  { id: 'errores', label: 'Errores api (_errors.log)' },
  { id: 'php', label: 'Log PHP (api44 / Hacienda)' },
];
const nivelCls: Record<string, string> = {
  Error: 'text-danger bg-danger/10', Warning: 'text-warning bg-warning/15', Info: 'text-info bg-info/10', Debug: 'text-muted-foreground bg-muted',
};
type Filtros = { src: string; nivel: string; q: string; doc: string; desde: string; hasta: string; limit: string; componente: string; codigo: string; run: string; hist: boolean };

const fmtHora = (f: string) => {
  if (!f) return '—';
  const m = f.match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2}):(\d{2})/);
  return m ? `${m[3]}/${m[2]}/${m[1]} ${m[4]}:${m[5]}:${m[6]}` : f.replace('T', ' ').slice(0, 19);
};
const fmtN = (n: number) => n.toLocaleString('es-CR');

const abrirDoc = (r: LogRow) => {
  const num = (r.numero || '').trim();
  if (!num) return;
  const tipo = (r.tipo_doc || '').trim();
  const q = new URLSearchParams({ doc: num });
  if (tipo) q.set('tipo', tipo);
  location.hash = '#/comprobantes?' + q.toString();
};

// Fondo sólido del área de log (las celdas fijas lo repiten para que no se transparente el contenido al desplazar)
const FONDO = 'bg-[hsl(222_40%_6%)]';
const FONDO_CAB = 'bg-[hsl(222_32%_11%)]';
// Columna fecha/hora fija a la izquierda al desplazar en horizontal; cubre también el espacio entre columnas.
const FIJA = 'sticky left-0 z-[1] -mr-3 pr-3';

// Columnas redimensionables (px). Se guardan en localStorage; doble clic en el borde restablece la columna.
type ColId = 'fecha' | 'nivel' | 'componente' | 'codigo' | 'documento' | 'mensaje';
const COLS: { id: ColId; label: string; def: number; min: number }[] = [
  { id: 'fecha', label: 'Fecha / hora', def: 168, min: 120 },
  { id: 'nivel', label: 'Nivel', def: 76, min: 56 },
  { id: 'componente', label: 'Componente', def: 104, min: 60 },
  { id: 'codigo', label: 'Código', def: 184, min: 70 },
  { id: 'documento', label: 'Documento', def: 264, min: 90 },
  { id: 'mensaje', label: 'Mensaje', def: 320, min: 160 },
];
const ANCHOS_KEY = 'ph-logs-anchos';
const anchosDef = () => Object.fromEntries(COLS.map(c => [c.id, c.def])) as Record<ColId, number>;
const leerAnchos = (): Record<ColId, number> => {
  const d = anchosDef();
  try {
    const j = JSON.parse(localStorage.getItem(ANCHOS_KEY) || '{}');
    for (const c of COLS) if (typeof j[c.id] === 'number' && isFinite(j[c.id])) d[c.id] = Math.max(c.min, Math.min(2000, Math.round(j[c.id])));
  } catch { /* ignore */ }
  return d;
};
// Orden: en el servidor para la bitácora (todas las páginas); en archivos solo dentro de la página.
type Orden = { col: ColId; dir: 'asc' | 'desc' };
const ORDEN_DEF: Orden = { col: 'fecha', dir: 'desc' };
const ORDEN_ARCHIVO: ColId[] = ['fecha', 'nivel', 'mensaje'];

export default function Registro() {
  const toast = useToast();
  const [f, setF] = useState<Filtros>({ src: 'bitacora', nivel: '', q: '', doc: '', desde: '', hasta: '', limit: '200', componente: '', codigo: '', run: '', hist: false });
  const [rows, setRows] = useState<(LogRow & { nuevo?: boolean })[]>([]);
  const [vivo, setVivo] = useState(true);
  useMantenerSesion();
  const [auto, setAuto] = useState(true);
  const [cargando, setCargando] = useState(false);
  const [info, setInfo] = useState('');
  const [avisos, setAvisos] = useState<string[]>([]);
  const [avisoAbierto, setAvisoAbierto] = useState(false);
  const [avisoOculto, setAvisoOculto] = useState('');
  const [cerrados, setCerrados] = useState<Record<string, boolean>>({});
  const [pag, setPag] = useState(1);
  const [mas, setMas] = useState(false);
  const [total, setTotal] = useState<number | null>(null);
  const [anchos, setAnchos] = useState<Record<ColId, number>>(leerAnchos);
  const [orden, setOrden] = useState<Orden>(ORDEN_DEF);
  const ordRef = useRef(orden); ordRef.current = orden;
  const pagRef = useRef(1);
  const pila = useRef<(string | undefined)[]>([]); // fuentes de archivo: cursor "before" para pedir la página siguiente
  const cur = useRef<{ nuevo: string | null; antiguo: string | null }>({ nuevo: null, antiguo: null });
  const caja = useRef<HTMLDivElement>(null);
  const fRef = useRef(f); fRef.current = f;
  const esBd = (s: string) => s.startsWith('bitacora');

  const params = (x: Filtros) => ({
    ...x, hist: x.hist ? '1' : undefined,
    desde: x.desde.replace('T', ' '), hasta: x.hasta.replace('T', ' '),
  });
  const abajo = () => requestAnimationFrame(() => { if (caja.current) caja.current.scrollTop = caja.current.scrollHeight; });

  const cargarAvisos = useCallback(async () => {
    try {
      const j = await api('logs_avisos');
      if (j.ok) setAvisos(j.resumen ?? []);
    } catch { /* ignore */ }
  }, []);

  /** Página p (1 = lo más reciente). BD: OFFSET/FETCH en el servidor; archivos: cursor por bytes. */
  const cargar = useCallback(async (x: Filtros, p = 1) => {
    setCargando(true);
    try {
      const extra: Record<string, string | number | undefined> = {};
      const o = ordRef.current;
      if (esBd(x.src)) { extra.page = p; extra.orden = o.col; extra.dir = o.dir; }
      else if (p > 1) extra.before = pila.current[p - 1];
      const j = await api('logs', { ...params(x), ...extra });
      if (!j.ok) { toast('error', j.error); return; }
      if (!esBd(x.src)) pila.current[p] = j.cursor_antiguo ?? undefined;
      // Orden por defecto (fecha desc): estilo "cola de log", lo más reciente abajo. Con otro orden se muestra tal cual llega.
      const cola = !esBd(x.src) || (o.col === 'fecha' && o.dir === 'desc');
      setRows(cola ? [...(j.rows ?? [])].reverse() : (j.rows ?? []));
      cur.current = { nuevo: j.cursor_nuevo, antiguo: j.cursor_antiguo };
      setMas(!!j.mas); setTotal(typeof j.total === 'number' ? j.total : null);
      setPag(p); pagRef.current = p;
      setInfo(j.aviso || '');
      if (cola) abajo(); else requestAnimationFrame(() => { if (caja.current) caja.current.scrollTop = 0; });
    } finally { setCargando(false); }
  }, [toast]);

  useEffect(() => { cargar(f, 1); cargarAvisos(); /* eslint-disable-next-line */ }, [f.src]);
  useEffect(() => { try { localStorage.setItem(ANCHOS_KEY, JSON.stringify(anchos)); } catch { /* ignore */ } }, [anchos]);
  // Con histórico solo se ordena por fecha (22,7M filas sin índices por otras columnas)
  useEffect(() => { if (f.hist && ordRef.current.col !== 'fecha') { const o = ORDEN_DEF; ordRef.current = o; setOrden(o); cargar(fRef.current, 1); } /* eslint-disable-next-line */ }, [f.hist]);
  useEffect(() => {
    if (!vivo) return;
    const t = setInterval(async () => {
      // En vivo solo en la página 1 (las demás son históricas y no se mueven)
      const o = ordRef.current;
      if (document.hidden || cur.current.nuevo == null || pagRef.current !== 1 || o.col !== 'fecha' || o.dir !== 'desc') return;
      const x = fRef.current;
      const j = await api('logs', { ...params({ ...x, desde: '', hasta: '' }), since: cur.current.nuevo }).catch(() => null);
      if (!j?.ok) return;
      cur.current.nuevo = j.cursor_nuevo ?? cur.current.nuevo;
      if (j.rows?.length) {
        setRows(r => [...r.map(y => ({ ...y, nuevo: false })), ...[...(j.rows ?? [])].reverse().map((y: LogRow) => ({ ...y, nuevo: true }))].slice(-3000));
        if (auto) abajo();
        cargarAvisos();
      }
    }, 3000);
    return () => clearInterval(t);
  }, [vivo, auto, cargarAvisos]);

  const set = (k: keyof Filtros) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const v = e.target.type === 'checkbox' ? (e.target as HTMLInputElement).checked : e.target.value;
    setF(s => ({ ...s, [k]: v as never }));
  };

  const vistaRows = useMemo(() => {
    if (esBd(f.src) || (orden.col === 'fecha' && orden.dir === 'desc')) return rows;
    // Archivos: orden por nivel o mensaje solo dentro de la página (orden estable: empata por fecha)
    const k = (r: LogRow) => String(orden.col === 'nivel' ? r.nivel || '' : r.texto || '');
    const s = [...rows].sort((a, b) => k(a).localeCompare(k(b), 'es'));
    return orden.dir === 'desc' ? s.reverse() : s;
  }, [rows, orden, f.src]);
  const agrupar = esBd(f.src) && orden.col === 'fecha';
  const grupos = useMemo(() => {
    if (!agrupar) return [{ key: 'log', run: '', label: '', items: vistaRows }];
    const out: { key: string; run: string; label: string; items: typeof rows }[] = [];
    for (const r of vistaRows) {
      const run = (r.run_id || '').trim();
      const last = out[out.length - 1];
      if (run && last && last.run === run) { last.items.push(r); continue; }
      const resumen = r.codigo === 'RUN_RESUMEN' ? r.texto : '';
      out.push({ key: run || ('f-' + (r.f || out.length)), run, label: resumen, items: [r] });
    }
    for (const g of out) {
      const res = [...g.items].reverse().find(x => x.codigo === 'RUN_RESUMEN');
      if (res) g.label = res.texto;
      else if (!g.label && g.run) {
        const hora = fmtHora(g.items[0]?.f || '').slice(11, 16);
        g.label = `Corrida ${hora || '—'} · ${g.items.length} línea${g.items.length === 1 ? '' : 's'}`;
      }
    }
    return out;
  }, [vistaRows, agrupar]);

  const paginas = total != null ? Math.max(1, Math.ceil(total / Number(f.limit || 200))) : null;
  const claveAvisos = avisos.join('|');
  const verAvisos = avisos.length > 0 && avisoOculto !== claveAvisos;

  // Columnas fijas para que fecha, nivel, componente, código y documento queden alineados (también en filas viejas sin esos campos).
  const logGrid = 'grid items-start gap-x-3 px-4 py-1';
  const gridStyle: React.CSSProperties = {
    gridTemplateColumns: `1.25rem ${anchos.fecha}px ${anchos.nivel}px ${anchos.componente}px ${anchos.codigo}px ${anchos.documento}px minmax(${anchos.mensaje}px, 1fr)`,
    minWidth: 20 + COLS.reduce((t, c) => t + anchos[c.id], 0) + 6 * 12 + 32,
  };
  const ordenable = (c: ColId) => esBd(f.src) ? (!f.hist || c === 'fecha') : ORDEN_ARCHIVO.includes(c);
  const ordenar = (c: ColId) => {
    if (!ordenable(c)) return;
    if (!esBd(f.src) && c === 'fecha') { ordRef.current = ORDEN_DEF; setOrden(ORDEN_DEF); return; } // archivos: fecha = vista de log normal
    const o: Orden = orden.col === c ? { col: c, dir: orden.dir === 'asc' ? 'desc' : 'asc' } : { col: c, dir: c === 'fecha' ? 'desc' : 'asc' };
    ordRef.current = o; setOrden(o);
    if (esBd(f.src)) cargar(fRef.current, 1);
  };
  const arrastre = useRef<{ id: ColId; x: number; w: number } | null>(null);
  const iniciarResize = (id: ColId) => (e: React.PointerEvent<HTMLSpanElement>) => {
    e.preventDefault(); e.stopPropagation();
    arrastre.current = { id, x: e.clientX, w: anchos[id] };
    const el = e.currentTarget; el.setPointerCapture(e.pointerId);
    const mover = (ev: PointerEvent) => {
      const a = arrastre.current; if (!a) return;
      const min = COLS.find(c => c.id === a.id)!.min;
      setAnchos(s => ({ ...s, [a.id]: Math.max(min, Math.min(2000, Math.round(a.w + ev.clientX - a.x))) }));
    };
    const fin = () => { arrastre.current = null; el.removeEventListener('pointermove', mover); el.removeEventListener('pointerup', fin); el.removeEventListener('pointercancel', fin); };
    el.addEventListener('pointermove', mover); el.addEventListener('pointerup', fin); el.addEventListener('pointercancel', fin);
  };
  const resetCol = (id: ColId) => setAnchos(s => ({ ...s, [id]: COLS.find(c => c.id === id)!.def }));
  const Cabecera = ({ c }: { c: typeof COLS[number] }) => {
    const activo = orden.col === c.id; const puede = ordenable(c.id);
    const Icono = !activo ? ArrowUpDown : orden.dir === 'asc' ? ArrowUp : ArrowDown;
    const titulo = puede ? `Ordenar por ${c.label.toLowerCase()}` + (c.id === 'fecha' ? ' (página 1 = lo más reciente al ordenar descendente)' : '')
      : f.hist ? 'Con histórico solo se ordena por fecha' : 'No se puede ordenar esta columna en esta fuente';
    return <span className={cn('relative min-w-0', c.id === 'fecha' && cn(FIJA, FONDO_CAB))} aria-sort={activo ? (orden.dir === 'asc' ? 'ascending' : 'descending') : 'none'}>
      <button type="button" disabled={!puede} onClick={() => ordenar(c.id)} title={titulo}
        className={cn('inline-flex max-w-full items-center gap-1 truncate rounded uppercase tracking-wide focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring', puede ? 'hover:text-slate-100' : 'cursor-default', activo && 'text-slate-100')}>
        <span className="truncate">{c.label}</span>{puede && <Icono className={cn('h-3 w-3 shrink-0', !activo && 'opacity-40')} />}
      </button>
      {c.id !== 'mensaje' && <span role="separator" aria-orientation="vertical" aria-label={`Ancho de ${c.label}`} title="Arrastrar para cambiar el ancho · doble clic para restablecer"
        onPointerDown={iniciarResize(c.id)} onDoubleClick={() => resetCol(c.id)}
        className="absolute -right-[0.4rem] top-[-0.375rem] bottom-[-0.375rem] z-[2] w-2 cursor-col-resize touch-none after:absolute after:inset-y-1 after:left-[3px] after:w-px after:bg-white/15 hover:after:bg-primary" />}
    </span>;
  };
  const celda = 'min-w-0 truncate';
  const Linea = ({ r }: { r: LogRow & { nuevo?: boolean } }) => (
    <div style={gridStyle} className={cn(logGrid, 'group border-b border-white/[.04] hover:bg-white/[.03]', r.nuevo && 'animate-in bg-teal-500/[.07]')}>
      <span aria-hidden="true" />
      <span className={cn(FIJA, FONDO, 'whitespace-nowrap text-slate-500 tabular-nums group-hover:bg-[hsl(222_34%_8%)]')}>{fmtHora(r.f)}</span>
      <span className="min-w-0">{r.nivel ? <span className={cn('inline-block rounded px-1.5 text-[11px] font-semibold uppercase', nivelCls[r.nivel] || nivelCls.Debug)}>{r.nivel}</span> : <span className="inline-block min-h-[1.1em]">&nbsp;</span>}</span>
      <span className={cn(celda, 'text-slate-400')}>{r.componente || ' '}</span>
      <span className={cn(celda, 'font-mono text-[11px] text-slate-500')}>{r.codigo || ' '}</span>
      <span className={cn(celda, 'font-mono text-cyan-300')}>
        {r.doc ? <button type="button" className="truncate hover:underline" onClick={() => abrirDoc(r)}>{r.doc}{r.hist ? ' · hist' : ''}</button> : ' '}
      </span>
      <span className={cn('min-w-0 whitespace-pre-wrap break-words', r.nivel === 'Error' && 'text-red-300', r.nivel === 'Warning' && 'text-amber-200')}>{r.texto || ' '}</span>
    </div>
  );

  const ir = (p: number) => { if (p >= 1 && !cargando) cargar(fRef.current, p); };

  return <div className="space-y-4">
    <Card className="p-4">
      {verAvisos && <div className="mb-3 flex items-center">
        {<div className="relative min-w-0 max-w-full">
          {/* Avisos abiertos: una sola línea; el detalle se abre encima sin desplazar la página */}
          <div className="flex min-w-0 items-center gap-1.5 rounded-full border border-warning/40 bg-warning/10 py-0.5 pl-2.5 pr-1 text-xs text-warning">
            <AlertTriangle className="h-3.5 w-3.5 shrink-0" />
            <button type="button" className="min-w-0 truncate text-left hover:underline" aria-expanded={avisoAbierto} title={avisos.join('\n')} onClick={() => setAvisoAbierto(a => !a)}>
              {avisos[0]}{avisos.length > 1 && <b className="ml-1.5">+{avisos.length - 1} más</b>}
            </button>
            <button type="button" className="shrink-0 rounded-full p-0.5 hover:bg-warning/20" title="Ocultar avisos" aria-label="Ocultar avisos" onClick={() => { setAvisoOculto(claveAvisos); setAvisoAbierto(false); }}><X className="h-3 w-3" /></button>
          </div>
          {avisoAbierto && <div className="absolute right-0 top-full z-30 mt-1 w-[min(40rem,90vw)] space-y-1 rounded-lg border border-warning/40 bg-card p-3 text-xs text-warning shadow-lg">
            {avisos.map((a, i) => <div key={i} className="flex gap-2"><AlertTriangle className="mt-0.5 h-3.5 w-3.5 shrink-0" /><span>{a}</span></div>)}
          </div>}
        </div>}
      </div>}
      <form className="flex flex-wrap items-end gap-3" onSubmit={e => { e.preventDefault(); cargar(f, 1); }}>
        <div><Label>Fuente</Label><Select className="w-auto" value={f.src} onChange={e => { ordRef.current = ORDEN_DEF; setOrden(ORDEN_DEF); set('src')(e); }}>{FUENTES.map(x => <option key={x.id} value={x.id}>{x.label}</option>)}</Select></div>
        <div><Label>Nivel</Label><Select className="w-auto" value={f.nivel} onChange={set('nivel')}><option value="">Todos</option><option value="avisos">Error y Warning</option><option>Error</option><option>Warning</option><option>Info</option><option>Debug</option></Select></div>
        <div><Label>Texto</Label><Input className="w-56" value={f.q} onChange={set('q')} placeholder="buscar…" /></div>
        <div><Label>Documento</Label><Input className="w-48" value={f.doc} onChange={set('doc')} placeholder="F-0000…" /></div>
        {esBd(f.src) && <>
          <div><Label>Componente</Label><Input className="w-48" value={f.componente} onChange={set('componente')} placeholder="envio, token…" /></div>
          <div><Label>Código</Label><Input className="w-48" value={f.codigo} onChange={set('codigo')} placeholder="RUN_RESUMEN…" /></div>
          <div><Label>Corrida (RUN_ID)</Label><Input className="w-56" value={f.run} onChange={set('run')} placeholder="id de la corrida" /></div>
        </>}
        <div><Label>Desde</Label><Input className="w-auto" type="datetime-local" value={f.desde} onChange={set('desde')} /></div>
        <div><Label>Hasta</Label><Input className="w-auto" type="datetime-local" value={f.hasta} onChange={set('hasta')} /></div>
        <div><Label>Filas</Label><Select className="w-auto" value={f.limit} onChange={set('limit')}><option>100</option><option>200</option><option>500</option></Select></div>
        <Button type="submit" loading={cargando}><Search className="h-4 w-4" />Buscar</Button>
        {esBd(f.src) && <label className="flex shrink-0 items-center gap-2 whitespace-nowrap pb-2 text-sm text-muted-foreground"><input type="checkbox" checked={f.hist} onChange={e => setF(s => ({ ...s, hist: e.target.checked }))} />Incluir histórico (antes de 2026)</label>}
      </form>
    </Card>
    <Card>
      <div className="flex flex-wrap items-center gap-4 rounded-t-lg border-b px-4 py-2.5 text-sm">
        <Button size="sm" variant={vivo ? 'secondary' : 'primary'} onClick={() => setVivo(v => !v)}>{vivo ? <><Pause className="h-3.5 w-3.5" />Pausar</> : <><Play className="h-3.5 w-3.5" />Reanudar</>}</Button>
        <span className="inline-flex items-center gap-2 text-xs text-muted-foreground"><span className={cn('h-2 w-2 rounded-full', vivo && pag === 1 && orden.col === 'fecha' && orden.dir === 'desc' ? 'bg-success animate-pulse' : 'bg-muted-foreground')} />{!vivo ? 'En pausa' : pag === 1 && orden.col === 'fecha' && orden.dir === 'desc' ? 'En vivo (cada 3 s)' : 'En vivo solo en la página 1 con orden por fecha'}</span>
        <Switch checked={auto} onChange={setAuto} label="Desplazamiento automático" />
        <div className="ml-auto flex flex-wrap items-center gap-2 text-xs text-muted-foreground">{info && <span className="text-warning">{info}</span>}
          <Button size="sm" variant="ghost" onClick={abajo} title="Ir al final"><ArrowDownToLine className="h-3.5 w-3.5" /></Button>
          <Button size="sm" variant="ghost" onClick={() => setRows([])} title="Limpiar vista"><Trash2 className="h-3.5 w-3.5" /></Button></div>
      </div>
      <div ref={caja} className={cn('h-[62vh] overflow-auto font-mono text-[12.5px] leading-relaxed text-slate-200 scrollbar-thin', FONDO)}>
        <div style={gridStyle} className={cn(logGrid, 'sticky top-0 z-20 select-none border-b border-white/10 py-1.5 font-sans text-[11px] font-semibold uppercase tracking-wide text-slate-400', FONDO_CAB)}>
          <span aria-hidden="true" />
          {COLS.map(c => <Cabecera key={c.id} c={c} />)}
        </div>
        {!rows.length && <div className="p-8 text-center text-slate-500">Sin registros para el filtro.</div>}
        {grupos.map(g => {
          const hayGrupo = agrupar && !!g.run;
          const abierto = !cerrados[g.key];
          return <div key={g.key}>
            {hayGrupo && <button type="button" style={gridStyle} className={cn(logGrid, 'group w-full border-b border-white/10 bg-[hsl(222_30%_9%)] py-1.5 text-left text-[12px] text-slate-300 hover:bg-[hsl(222_28%_12%)]')} onClick={() => setCerrados(s => ({ ...s, [g.key]: !s[g.key] }))}>
              <span className="flex h-full items-center text-slate-400">{abierto ? <ChevronDown className="h-3.5 w-3.5 shrink-0" /> : <ChevronRight className="h-3.5 w-3.5 shrink-0" />}</span>
              <span className={cn(FIJA, 'whitespace-nowrap bg-[hsl(222_30%_9%)] text-slate-500 tabular-nums group-hover:bg-[hsl(222_28%_12%)]')}>{fmtHora(g.items[0]?.f || '')}</span>
              <span className="min-w-0"><span className="inline-block rounded px-1.5 text-[11px] font-semibold uppercase text-info bg-info/10">Corrida</span></span>
              <span className="min-w-0 truncate text-slate-400">{' '}</span>
              <span className="min-w-0 truncate font-mono text-[11px] text-slate-500">{' '}</span>
              <span className="min-w-0 truncate font-mono text-slate-500">{g.items.length} línea{g.items.length === 1 ? '' : 's'}</span>
              <span className="min-w-0 whitespace-pre-wrap break-words text-left text-slate-300"><span className="font-medium">{g.label || 'Corrida'}</span> <span className="font-mono text-[10px] text-slate-500">{g.run}</span></span>
            </button>}
            {(!hayGrupo || abierto) && g.items.map((r, i) => <Linea key={g.key + i} r={r} />)}
          </div>;
        })}
      </div>
      {/* Paginación abajo, fija al pie de la ventana si la página se desplaza */}
      <div className="sticky bottom-0 z-20 flex flex-wrap items-center gap-3 rounded-b-lg border-t bg-card px-4 py-2 text-xs text-muted-foreground" role="navigation" aria-label="Páginas del log">
        <span>{total != null ? `${fmtN(total)} registros` : `${rows.length} líneas${mas ? ' · hay más' : ''}`}</span>
        {!esBd(f.src) && !(orden.col === 'fecha' && orden.dir === 'desc') && <span>· orden aplicado solo dentro de la página</span>}
        <div className="ml-auto flex items-center gap-1">
          <Button size="sm" variant="outline" disabled={pag <= 1 || cargando} onClick={() => ir(1)} title="Primera página" aria-label="Primera página"><ChevronsLeft className="h-3.5 w-3.5" /></Button>
          <Button size="sm" variant="outline" disabled={pag <= 1 || cargando} onClick={() => ir(pag - 1)} title="Página anterior" aria-label="Página anterior"><ChevronLeft className="h-3.5 w-3.5" /></Button>
          <span className="whitespace-nowrap px-2 tabular-nums">Página {fmtN(pag)}{paginas != null && ` de ${fmtN(paginas)}`}</span>
          <Button size="sm" variant="outline" disabled={!mas || cargando} onClick={() => ir(pag + 1)} title="Página siguiente" aria-label="Página siguiente"><ChevronRight className="h-3.5 w-3.5" /></Button>
        </div>
      </div>
    </Card>
  </div>;
}
