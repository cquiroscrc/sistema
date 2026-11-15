import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { Pause, Play, Search, ChevronsUp, ArrowDownToLine, Trash2, ChevronDown, ChevronRight, AlertTriangle } from 'lucide-react';
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

const abrirDoc = (r: LogRow) => {
  const num = (r.numero || '').trim();
  if (!num) return;
  const tipo = (r.tipo_doc || '').trim();
  const q = new URLSearchParams({ doc: num });
  if (tipo) q.set('tipo', tipo);
  location.hash = '#/comprobantes?' + q.toString();
};

export default function Registro() {
  const toast = useToast();
  const [f, setF] = useState<Filtros>({ src: 'bitacora', nivel: '', q: '', doc: '', desde: '', hasta: '', limit: '200', vista: 'eventos', componente: '', codigo: '', run: '', hist: false });
  const [rows, setRows] = useState<(LogRow & { nuevo?: boolean })[]>([]);
  const [vivo, setVivo] = useState(true);
  const [auto, setAuto] = useState(true);
  const [cargando, setCargando] = useState(false);
  const [info, setInfo] = useState('');
  const [avisos, setAvisos] = useState<string[]>([]);
  const [cerrados, setCerrados] = useState<Record<string, boolean>>({});
  const cur = useRef<{ nuevo: string | null; antiguo: string | null; mas: boolean }>({ nuevo: null, antiguo: null, mas: false });
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

  const cargar = useCallback(async (x: Filtros) => {
    setCargando(true);
    try {
      const j = await api('logs', params(x));
      if (!j.ok) { toast('error', j.error); return; }
      setRows([...(j.rows ?? [])].reverse());
      cur.current = { nuevo: j.cursor_nuevo, antiguo: j.cursor_antiguo, mas: j.mas };
      setInfo(j.aviso || '');
      abajo();
    } finally { setCargando(false); }
  }, [toast]);

  const anteriores = async () => {
    const j = await api('logs', { ...params(fRef.current), before: cur.current.antiguo ?? undefined });
    if (!j.ok) return;
    const el = caja.current; const h = el?.scrollHeight ?? 0;
    setRows(r => [...[...(j.rows ?? [])].reverse(), ...r]);
    cur.current = { ...cur.current, antiguo: j.cursor_antiguo, mas: j.mas };
    requestAnimationFrame(() => { if (el) el.scrollTop = el.scrollHeight - h; });
  };

  useEffect(() => { cargar(f); cargarAvisos(); /* eslint-disable-next-line */ }, [f.src, f.vista]);
  useEffect(() => {
    if (!vivo) return;
    const t = setInterval(async () => {
      if (document.hidden || cur.current.nuevo == null) return;
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
    // if a later row in the group is RUN_RESUMEN, use it as label
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

  const Linea = ({ r }: { r: LogRow & { nuevo?: boolean } }) => (
    <div className={cn('flex gap-3 border-b border-white/[.04] px-4 py-1 hover:bg-white/[.03]', r.nuevo && 'animate-in bg-teal-500/[.07]')}>
      <span className="shrink-0 text-slate-500 tabular-nums">{fmtHora(r.f)}</span>
      <span className={cn('h-fit shrink-0 rounded px-1.5 text-[11px] font-semibold uppercase', nivelCls[r.nivel] || nivelCls.Debug)}>{r.nivel}</span>
      {r.componente && <span className="shrink-0 text-slate-400">{r.componente}</span>}
      {r.codigo && <span className="shrink-0 font-mono text-[11px] text-slate-500">{r.codigo}</span>}
      {r.doc && <button type="button" className="shrink-0 text-cyan-300 hover:underline" onClick={() => abrirDoc(r)}>{r.doc}</button>}
      {r.hist && <span className="shrink-0 rounded bg-white/10 px-1 text-[10px] uppercase text-slate-400">hist</span>}
      <span className={cn('whitespace-pre-wrap break-words', r.nivel === 'Error' && 'text-red-300', r.nivel === 'Warning' && 'text-amber-200')}>{r.texto}</span>
    </div>
  );

  return <div className="space-y-4">
    {avisos.length > 0 && <Card className="border-warning/40 bg-warning/10 p-3 text-sm">
      <div className="flex items-start gap-2 text-warning"><AlertTriangle className="mt-0.5 h-4 w-4 shrink-0" /><div className="space-y-1">{avisos.map((a, i) => <div key={i}>{a}</div>)}</div></div>
    </Card>}
    <Card className="p-4">
      {esBd(f.src) && <div className="mb-3"><Tabs value={f.vista} onChange={v => setF(s => ({ ...s, vista: v }))} items={[
        { id: 'eventos' as Vista, label: 'Eventos' },
        { id: 'avisos' as Vista, label: 'Errores y avisos' },
        { id: 'diagnostico' as Vista, label: 'Diagnóstico' },
      ]} /></div>}
      <form className="grid items-end gap-3 sm:grid-cols-2 lg:grid-cols-4 2xl:grid-cols-8 [&>*]:min-w-0" onSubmit={e => { e.preventDefault(); cargar(f); }}>
        <div className="xl:col-span-2"><Label>Fuente</Label><Select value={f.src} onChange={set('src')}>{FUENTES.map(x => <option key={x.id} value={x.id}>{x.label}</option>)}</Select></div>
        <div><Label>Nivel</Label><Select value={f.nivel} onChange={set('nivel')}><option value="">Todos</option><option>Error</option><option>Warning</option><option>Info</option><option>Debug</option></Select></div>
        <div><Label>Texto</Label><Input value={f.q} onChange={set('q')} placeholder="buscar…" /></div>
        <div><Label>Documento</Label><Input value={f.doc} onChange={set('doc')} placeholder="F-0000…" /></div>
        {esBd(f.src) && <>
          <div><Label>Componente</Label><Input value={f.componente} onChange={set('componente')} placeholder="envio, token…" /></div>
          <div><Label>Código</Label><Input value={f.codigo} onChange={set('codigo')} placeholder="RUN_RESUMEN…" /></div>
          <div><Label>Corrida (RUN_ID)</Label><Input value={f.run} onChange={set('run')} placeholder="id de la corrida" /></div>
        </>}
        <div><Label>Desde</Label><Input type="datetime-local" value={f.desde} onChange={set('desde')} /></div>
        <div><Label>Hasta</Label><Input type="datetime-local" value={f.hasta} onChange={set('hasta')} /></div>
        <div className="flex gap-2"><div className="w-20"><Label>Filas</Label><Select value={f.limit} onChange={set('limit')}><option>100</option><option>200</option><option>500</option></Select></div>
          <Button type="submit" loading={cargando} className="mt-auto"><Search className="h-4 w-4" />Buscar</Button></div>
        {esBd(f.src) && <label className="flex items-center gap-2 pb-2 text-sm text-muted-foreground"><input type="checkbox" checked={f.hist} onChange={e => setF(s => ({ ...s, hist: e.target.checked }))} />Incluir histórico (antes de 2026)</label>}
      </form>
    </Card>
    <Card className="overflow-hidden">
      <div className="flex flex-wrap items-center gap-4 border-b px-4 py-2.5 text-sm">
        <Button size="sm" variant={vivo ? 'secondary' : 'primary'} onClick={() => setVivo(v => !v)}>{vivo ? <><Pause className="h-3.5 w-3.5" />Pausar</> : <><Play className="h-3.5 w-3.5" />Reanudar</>}</Button>
        <span className="inline-flex items-center gap-2 text-xs text-muted-foreground"><span className={cn('h-2 w-2 rounded-full', vivo ? 'bg-success animate-pulse' : 'bg-muted-foreground')} />{vivo ? 'En vivo (cada 3 s)' : 'En pausa'}</span>
        <Switch checked={auto} onChange={setAuto} label="Desplazamiento automático" />
        <div className="ml-auto flex items-center gap-2 text-xs text-muted-foreground">{info && <span className="text-warning">{info}</span>}{rows.length} líneas
          <Button size="sm" variant="ghost" onClick={abajo} title="Ir al final"><ArrowDownToLine className="h-3.5 w-3.5" /></Button>
          <Button size="sm" variant="ghost" onClick={() => setRows([])} title="Limpiar vista"><Trash2 className="h-3.5 w-3.5" /></Button></div>
      </div>
      <div ref={caja} className="h-[62vh] overflow-y-auto bg-[hsl(222_40%_6%)] font-mono text-[12.5px] leading-relaxed text-slate-200 scrollbar-thin">
        {cur.current.mas && <div className="p-2 text-center"><Button size="sm" variant="outline" className="border-white/10 bg-white/5 text-slate-200 hover:bg-white/10" onClick={anteriores}><ChevronsUp className="h-3.5 w-3.5" />Cargar anteriores</Button></div>}
        {!rows.length && <div className="p-8 text-center text-slate-500">Sin registros para el filtro.</div>}
        {grupos.map(g => {
          const hayGrupo = esBd(f.src) && !!g.run;
          const abierto = !cerrados[g.key];
          return <div key={g.key}>
            {hayGrupo && <button type="button" className="flex w-full items-center gap-2 border-b border-white/10 bg-white/[.04] px-4 py-1.5 text-left text-[12px] text-slate-300 hover:bg-white/[.07]" onClick={() => setCerrados(s => ({ ...s, [g.key]: !s[g.key] }))}>
              {abierto ? <ChevronDown className="h-3.5 w-3.5" /> : <ChevronRight className="h-3.5 w-3.5" />}
              <span className="font-medium">{g.label || g.run}</span>
              <span className="text-slate-500">{g.items.length} línea{g.items.length === 1 ? '' : 's'}</span>
              <span className="ml-auto font-mono text-[10px] text-slate-500">{g.run}</span>
            </button>}
            {(!hayGrupo || abierto) && g.items.map((r, i) => <Linea key={g.key + i} r={r} />)}
          </div>;
        })}
      </div>
    </Card>
  </div>;
}
