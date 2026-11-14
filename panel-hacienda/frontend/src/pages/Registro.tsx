import { useCallback, useEffect, useRef, useState } from 'react';
import { Pause, Play, Search, ChevronsUp, ArrowDownToLine, Trash2 } from 'lucide-react';
import { api, type LogRow } from '../lib/api';
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
type Filtros = { src: string; nivel: string; q: string; doc: string; desde: string; hasta: string; limit: string };

export default function Registro() {
  const toast = useToast();
  const [f, setF] = useState<Filtros>({ src: 'bitacora', nivel: '', q: '', doc: '', desde: '', hasta: '', limit: '200' });
  const [rows, setRows] = useState<(LogRow & { nuevo?: boolean })[]>([]);
  const [vivo, setVivo] = useState(true);
  const [auto, setAuto] = useState(true);
  const [cargando, setCargando] = useState(false);
  const [info, setInfo] = useState('');
  const cur = useRef<{ nuevo: string | null; antiguo: string | null; mas: boolean }>({ nuevo: null, antiguo: null, mas: false });
  const caja = useRef<HTMLDivElement>(null);
  const fRef = useRef(f); fRef.current = f;

  const params = (x: Filtros) => ({ ...x, desde: x.desde.replace('T', ' '), hasta: x.hasta.replace('T', ' ') });
  const abajo = () => requestAnimationFrame(() => { if (caja.current) caja.current.scrollTop = caja.current.scrollHeight; });

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

  useEffect(() => { cargar(f); /* eslint-disable-next-line */ }, [f.src]);
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
      }
    }, 3000);
    return () => clearInterval(t);
  }, [vivo, auto]);

  const set = (k: keyof Filtros) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setF(s => ({ ...s, [k]: e.target.value }));
  return <div className="space-y-4">
    <Card className="p-4">
      <form className="grid items-end gap-3 sm:grid-cols-2 lg:grid-cols-4 2xl:grid-cols-8 [&>*]:min-w-0" onSubmit={e => { e.preventDefault(); cargar(f); }}>
        <div className="xl:col-span-2"><Label>Fuente</Label><Select value={f.src} onChange={set('src')}>{FUENTES.map(x => <option key={x.id} value={x.id}>{x.label}</option>)}</Select></div>
        <div><Label>Nivel</Label><Select value={f.nivel} onChange={set('nivel')}><option value="">Todos</option><option>Error</option><option>Warning</option><option>Info</option><option>Debug</option></Select></div>
        <div><Label>Texto</Label><Input value={f.q} onChange={set('q')} placeholder="buscar…" /></div>
        <div><Label>Documento</Label><Input value={f.doc} onChange={set('doc')} placeholder="F-0000…" /></div>
        <div><Label>Desde</Label><Input type="datetime-local" value={f.desde} onChange={set('desde')} /></div>
        <div><Label>Hasta</Label><Input type="datetime-local" value={f.hasta} onChange={set('hasta')} /></div>
        <div className="flex gap-2"><div className="w-20"><Label>Filas</Label><Select value={f.limit} onChange={set('limit')}><option>100</option><option>200</option><option>500</option></Select></div>
          <Button type="submit" loading={cargando} className="mt-auto"><Search className="h-4 w-4" />Buscar</Button></div>
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
        {rows.map((r, i) => <div key={i} className={cn('flex gap-3 border-b border-white/[.04] px-4 py-1 hover:bg-white/[.03]', r.nuevo && 'animate-in bg-teal-500/[.07]')}>
          <span className="shrink-0 text-slate-500 tabular-nums">{r.f || '—'}</span>
          <span className={cn('h-fit shrink-0 rounded px-1.5 text-[11px] font-semibold uppercase', nivelCls[r.nivel] || nivelCls.Debug)}>{r.nivel}</span>
          {r.doc && <span className="shrink-0 text-cyan-300">{r.doc}</span>}
          <span className={cn('whitespace-pre-wrap break-words', r.nivel === 'Error' && 'text-red-300', r.nivel === 'Warning' && 'text-amber-200')}>{r.texto}</span>
        </div>)}
      </div>
    </Card>
  </div>;
}
