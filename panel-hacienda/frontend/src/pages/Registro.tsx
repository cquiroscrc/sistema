import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { Pause, Play, Search, ArrowDownToLine, Trash2, ChevronDown, ChevronRight, ChevronLeft, AlertTriangle, X } from 'lucide-react';
import { api, type LogRow } from '../lib/api';
import { cn } from '../lib/utils';
import { Button, Card, Input, Label, Select, Switch, Tabs, useToast } from '../components/ui';

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
type Vista = 'eventos' | 'avisos' | 'diagnostico';
type Filtros = { src: string; nivel: string; q: string; doc: string; desde: string; hasta: string; limit: string; vista: Vista; componente: string; codigo: string; run: string; hist: boolean };

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

export default function Registro() {
  const toast = useToast();
  const [f, setF] = useState<Filtros>({ src: 'bitacora', nivel: '', q: '', doc: '', desde: '', hasta: '', limit: '200', vista: 'eventos', componente: '', codigo: '', run: '', hist: false });
  const [rows, setRows] = useState<(LogRow & { nuevo?: boolean })[]>([]);
  const [vivo, setVivo] = useState(true);
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
      if (esBd(x.src)) extra.page = p;
      else if (p > 1) extra.before = pila.current[p - 1];
      const j = await api('logs', { ...params(x), ...extra });
      if (!j.ok) { toast('error', j.error); return; }
      if (!esBd(x.src)) pila.current[p] = j.cursor_antiguo ?? undefined;
      setRows([...(j.rows ?? [])].reverse());
      cur.current = { nuevo: j.cursor_nuevo, antiguo: j.cursor_antiguo };
      setMas(!!j.mas); setTotal(typeof j.total === 'number' ? j.total : null);
      setPag(p); pagRef.current = p;
      setInfo(j.aviso || '');
      abajo();
    } finally { setCargando(false); }
  }, [toast]);

  useEffect(() => { cargar(f, 1); cargarAvisos(); /* eslint-disable-next-line */ }, [f.src, f.vista]);
  useEffect(() => {
    if (!vivo) return;
    const t = setInterval(async () => {
      // En vivo solo en la página 1 (las demás son históricas y no se mueven)
      if (document.hidden || cur.current.nuevo == null || pagRef.current !== 1) return;
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

  const grupos = useMemo(() => {
    if (!esBd(f.src)) return [{ key: 'log', run: '', label: '', items: rows }];
    const out: { key: string; run: string; label: string; items: typeof rows }[] = [];
    for (const r of rows) {
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
  }, [rows, f.src]);

  const paginas = total != null ? Math.max(1, Math.ceil(total / Number(f.limit || 200))) : null;
  const claveAvisos = avisos.join('|');
  const verAvisos = avisos.length > 0 && avisoOculto !== claveAvisos;

  // Columnas fijas para que fecha, nivel, componente, código y documento queden alineados (también en filas viejas sin esos campos).
  const logGrid = 'grid min-w-[72rem] grid-cols-[1.25rem_10.5rem_4.75rem_6.5rem_11.5rem_16.5rem_minmax(12rem,1fr)] items-start gap-x-3 px-4 py-1';
  const celda = 'min-w-0 truncate';
  const Linea = ({ r }: { r: LogRow & { nuevo?: boolean } }) => (
    <div className={cn(logGrid, 'group border-b border-white/[.04] hover:bg-white/[.03]', r.nuevo && 'animate-in bg-teal-500/[.07]')}>
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
      {(esBd(f.src) || verAvisos) && <div className="mb-3 flex flex-wrap items-center gap-3">
        {esBd(f.src) && <Tabs value={f.vista} onChange={v => setF(s => ({ ...s, vista: v }))} items={[
          { id: 'eventos' as Vista, label: 'Eventos' },
          { id: 'avisos' as Vista, label: 'Errores y avisos' },
          { id: 'diagnostico' as Vista, label: 'Diagnóstico' },
        ]} />}
        {verAvisos && <div className="relative ml-auto min-w-0 max-w-full">
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
        <div><Label>Fuente</Label><Select className="w-auto" value={f.src} onChange={set('src')}>{FUENTES.map(x => <option key={x.id} value={x.id}>{x.label}</option>)}</Select></div>
        <div><Label>Nivel</Label><Select className="w-auto" value={f.nivel} onChange={set('nivel')}><option value="">Todos</option><option>Error</option><option>Warning</option><option>Info</option><option>Debug</option></Select></div>
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
    <Card className="overflow-hidden">
      <div className="flex flex-wrap items-center gap-4 border-b px-4 py-2.5 text-sm">
        <Button size="sm" variant={vivo ? 'secondary' : 'primary'} onClick={() => setVivo(v => !v)}>{vivo ? <><Pause className="h-3.5 w-3.5" />Pausar</> : <><Play className="h-3.5 w-3.5" />Reanudar</>}</Button>
        <span className="inline-flex items-center gap-2 text-xs text-muted-foreground"><span className={cn('h-2 w-2 rounded-full', vivo && pag === 1 ? 'bg-success animate-pulse' : 'bg-muted-foreground')} />{!vivo ? 'En pausa' : pag === 1 ? 'En vivo (cada 3 s)' : 'En vivo solo en la página 1'}</span>
        <Switch checked={auto} onChange={setAuto} label="Desplazamiento automático" />
        <div className="ml-auto flex flex-wrap items-center gap-2 text-xs text-muted-foreground">{info && <span className="text-warning">{info}</span>}
          <span>{total != null ? `${fmtN(total)} registros` : `${rows.length} líneas${mas ? ' · hay más' : ''}`}</span>
          <div className="flex items-center gap-1" role="navigation" aria-label="Páginas del log">
            <Button size="sm" variant="outline" disabled={pag <= 1 || cargando} onClick={() => ir(pag - 1)} title="Más recientes" aria-label="Página anterior (más recientes)"><ChevronLeft className="h-3.5 w-3.5" /></Button>
            <span className="whitespace-nowrap px-1 tabular-nums">Página {fmtN(pag)}{paginas != null && ` de ${fmtN(paginas)}`}</span>
            <Button size="sm" variant="outline" disabled={!mas || cargando} onClick={() => ir(pag + 1)} title="Más antiguos" aria-label="Página siguiente (más antiguos)"><ChevronRight className="h-3.5 w-3.5" /></Button>
          </div>
          <Button size="sm" variant="ghost" onClick={abajo} title="Ir al final"><ArrowDownToLine className="h-3.5 w-3.5" /></Button>
          <Button size="sm" variant="ghost" onClick={() => setRows([])} title="Limpiar vista"><Trash2 className="h-3.5 w-3.5" /></Button></div>
      </div>
      <div ref={caja} className={cn('h-[62vh] overflow-auto font-mono text-[12.5px] leading-relaxed text-slate-200 scrollbar-thin', FONDO)}>
        <div className={cn(logGrid, 'sticky top-0 z-20 border-b border-white/10 py-1.5 font-sans text-[11px] font-semibold uppercase tracking-wide text-slate-400', FONDO_CAB)}>
          <span aria-hidden="true" />
          <span className={cn(FIJA, FONDO_CAB)}>Fecha / hora</span>
          <span>Nivel</span><span>Componente</span><span>Código</span><span>Documento</span><span>Mensaje</span>
        </div>
        {!rows.length && <div className="p-8 text-center text-slate-500">Sin registros para el filtro.</div>}
        {grupos.map(g => {
          const hayGrupo = esBd(f.src) && !!g.run;
          const abierto = !cerrados[g.key];
          return <div key={g.key}>
            {hayGrupo && <button type="button" className={cn(logGrid, 'group w-full border-b border-white/10 bg-[hsl(222_30%_9%)] py-1.5 text-left text-[12px] text-slate-300 hover:bg-[hsl(222_28%_12%)]')} onClick={() => setCerrados(s => ({ ...s, [g.key]: !s[g.key] }))}>
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
    </Card>
  </div>;
}
