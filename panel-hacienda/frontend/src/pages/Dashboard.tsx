import { useEffect, useRef, useState } from 'react';
import { Send, XOctagon, Hourglass, Power, CheckCircle2, Clock, ArrowRight } from 'lucide-react';
import { Bar, BarChart, CartesianGrid, Legend, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { api, type DocRow, type Status } from '../lib/api';
import { useMantenerSesion } from '../lib/keepalive';
import { cn, fmtMonto, fmtNum } from '../lib/utils';
import { Card, CardBody, CardHeader, CardTitle, EstadoBadge, Empty, Spinner, Tabs, Switch } from '../components/ui';
import type { Vista } from '../App';
import Espacio from '../components/Espacio';

function Kpi({ titulo, valor, sub, icon: Icon, tono }: { titulo: string; valor: React.ReactNode; sub?: React.ReactNode; icon: any; tono: 'primary' | 'danger' | 'warning' | 'success' | 'muted' }) {
  const t = { primary: 'bg-primary/10 text-primary', danger: 'bg-danger/10 text-danger', warning: 'bg-warning/15 text-warning', success: 'bg-success/10 text-success', muted: 'bg-muted text-muted-foreground' }[tono];
  return <Card className="p-5"><div className="flex items-start justify-between">
    <div><div className="text-xs font-medium uppercase tracking-wide text-muted-foreground">{titulo}</div><div className="mt-2 text-3xl font-semibold tabular-nums">{valor}</div>{sub && <div className="mt-1 text-xs text-muted-foreground">{sub}</div>}</div>
    <div className={cn('rounded-lg p-2.5', t)}><Icon className="h-5 w-5" /></div></div></Card>;
}

export default function Dashboard({ status, ir, onStatus }: { status: Status | null; ir: (v: Vista) => void; onStatus: (s: Status) => void }) {
  const [dias, setDias] = useState<'14' | '30' | '90'>('30');
  const [serie, setSerie] = useState<any[] | null>(null);
  const [recientes, setRecientes] = useState<DocRow[] | null>(null);
  const [auto, setAuto] = useState(() => { try { return localStorage.getItem('ph-resumen-auto') !== 'off'; } catch { return true; } });
  const [actualizado, setActualizado] = useState<Date | null>(null);
  const [fallo, setFallo] = useState(false);
  const control = useRef<{ run: () => void } | null>(null);
  useMantenerSesion();
  const valores = useRef({ dias, auto, onStatus }); valores.current = { dias, auto, onStatus };
  useEffect(() => {
    let vivo = true, ocupado = false, pendiente = false;
    let timer: ReturnType<typeof setTimeout> | undefined;
    const run = async () => {
      if (!vivo || document.hidden) return;
      clearTimeout(timer);
      if (ocupado) { pendiente = true; return; }
      ocupado = true; const periodo = valores.current.dias;
      try {
        const j = await api('resumen', { dias: periodo });
        if (vivo && periodo === valores.current.dias) {
          if (!j.ok) throw new Error('refresh');
          valores.current.onStatus(j.status); setSerie(j.serie ?? []); setRecientes(j.recientes ?? []);
          setActualizado(new Date()); setFallo(false);
        }
      } catch { if (vivo) setFallo(true); /* conservar los datos anteriores; api mantiene el manejo de 401 */ }
      finally {
        ocupado = false;
        if (vivo && !document.hidden) {
          if (pendiente) { pendiente = false; void run(); }
          else if (valores.current.auto) timer = setTimeout(() => { if (valores.current.auto) void run(); }, 60000);
        }
      }
    };
    const visible = () => { clearTimeout(timer); if (!document.hidden && valores.current.auto) void run(); };
    control.current = { run: () => { clearTimeout(timer); void run(); } };
    document.addEventListener('visibilitychange', visible); void run();
    return () => { vivo = false; clearTimeout(timer); control.current = null; document.removeEventListener('visibilitychange', visible); };
  }, []);
  const diasPrevios = useRef(dias);
  useEffect(() => { if (diasPrevios.current !== dias) { diasPrevios.current = dias; control.current?.run(); } }, [dias]);
  useEffect(() => { try { localStorage.setItem('ph-resumen-auto', auto ? 'on' : 'off'); } catch {} }, [auto]);
  const c = status?.conteos;
  const activo = !!status?.servicio_activo;
  return <div className="space-y-6">
    <div className="flex flex-wrap items-center justify-end gap-4 text-xs text-muted-foreground" data-testid="resumen-refresh">
      <span>Última actualización: {actualizado ? new Intl.DateTimeFormat('es-CR', { timeZone: 'America/Costa_Rica', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }).format(actualizado) : '—'} · Costa Rica</span>
      {fallo && <span className="text-warning">No se pudo actualizar; se conservan los datos.</span>}
      <Switch checked={auto} onChange={v => { valores.current.auto = v; setAuto(v); if (v) control.current?.run(); }} label="Actualizar cada 60 s" />
    </div>
    <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <Kpi titulo="Enviados hoy" valor={fmtNum(c?.enviados_hoy)} sub={c && <><span className="text-success">{fmtNum(c.aceptado_hoy)} aceptados</span> · <span className="text-danger">{fmtNum(c.rechazado_hoy)} rechazados</span></>} icon={Send} tono="primary" />
      <Kpi titulo="Con error" valor={fmtNum(c?.error)} sub={c?.reintentar ? `${c.reintentar} marcados para reintento` : 'Errores locales / servidor'} icon={XOctagon} tono={c?.error ? 'danger' : 'success'} />
      <Kpi titulo="En cola" valor={fmtNum(c?.cola)} sub={`${fmtNum(c?.pendiente_respuesta)} esperando respuesta`} icon={Hourglass} tono={c?.cola ? 'warning' : 'muted'} />
      <Kpi titulo="Servicio" valor={<span className={activo ? 'text-success' : 'text-muted-foreground'}>{activo ? 'Activo' : 'Detenido'}</span>}
        sub={status ? (activo ? `Próxima corrida: ${status.timer.NextElapseUSecRealtime || '—'}` : 'El envío sigue en 172.16.12.3') : '…'} icon={Power} tono={activo ? 'success' : 'muted'} />
    </div>
    <div className="grid gap-6 xl:grid-cols-3">
      <Card className="xl:col-span-2">
        <CardHeader><CardTitle>Comprobantes por día</CardTitle>
          <Tabs value={dias} onChange={setDias} items={[{ id: '14', label: '14 días' }, { id: '30', label: '30 días' }, { id: '90', label: '90 días' }]} /></CardHeader>
        <CardBody className="h-80">{!serie ? <div className="flex h-full items-center justify-center"><Spinner /></div> : !serie.length ? <Empty>Sin datos en el período.</Empty> :
          <ResponsiveContainer width="100%" height="100%">
            <BarChart data={serie} margin={{ top: 4, right: 8, left: -12, bottom: 0 }}>
              <CartesianGrid strokeDasharray="3 3" stroke="hsl(var(--border))" vertical={false} />
              <XAxis dataKey="dia" tick={{ fontSize: 11, fill: 'hsl(var(--muted-foreground))' }} tickFormatter={(d: string) => d.slice(8, 10) + '/' + d.slice(5, 7)} axisLine={false} tickLine={false} />
              <YAxis tick={{ fontSize: 11, fill: 'hsl(var(--muted-foreground))' }} axisLine={false} tickLine={false} allowDecimals={false} />
              <Tooltip cursor={{ fill: 'hsl(var(--muted))' }} contentStyle={{ background: 'hsl(var(--card))', border: '1px solid hsl(var(--border))', borderRadius: 8, fontSize: 12 }} labelStyle={{ color: 'hsl(var(--foreground))' }} />
              <Legend wrapperStyle={{ fontSize: 12 }} iconType="circle" />
              <Bar isAnimationActive={false} dataKey="aceptados" name="Aceptados" stackId="a" fill="hsl(var(--success))" />
              <Bar isAnimationActive={false} dataKey="rechazados" name="Rechazados" stackId="a" fill="hsl(var(--danger))" />
              <Bar isAnimationActive={false} dataKey="otros" name="Error / pendientes" stackId="a" fill="hsl(var(--warning))" radius={[4, 4, 0, 0]} />
            </BarChart></ResponsiveContainer>}</CardBody>
      </Card>
      <Card>
        <CardHeader><CardTitle>Últimos rechazos</CardTitle><button onClick={() => ir('comprobantes')} className="inline-flex items-center gap-1 text-xs text-primary hover:underline">Ver todos<ArrowRight className="h-3 w-3" /></button></CardHeader>
        <div className="divide-y">{!recientes ? <div className="p-6"><Spinner /></div> : !recientes.length ? <Empty>Sin rechazos.</Empty> : recientes.map(r =>
          <div key={r.tipo + r.numero} className="flex items-center gap-3 px-5 py-3">
            <div className="min-w-0 flex-1"><div className="text-sm font-medium">{r.tipo} {r.numero}</div><div className="truncate text-xs text-muted-foreground" title={r.error}>{r.error || r.receptor}</div></div>
            <div className="text-right"><div className="text-xs tabular-nums">{fmtMonto(r.total)}</div><div className="text-[11px] text-muted-foreground">{r.fecha.slice(0, 16)}</div></div>
          </div>)}</div>
      </Card>
    </div>
    <Espacio />
    <div className="grid gap-4 md:grid-cols-3">
      <Card className="p-4 text-sm"><div className="flex items-center gap-2 text-muted-foreground"><Clock className="h-4 w-4" />Última bitácora</div><div className="mt-1 font-medium tabular-nums">{status?.ultima_bitacora || '—'}</div></Card>
      <Card className="p-4 text-sm"><div className="flex items-center gap-2 text-muted-foreground"><CheckCircle2 className="h-4 w-4" />Último comprobante</div><div className="mt-1 font-medium tabular-nums">{status?.ultimo_documento || '—'}</div></Card>
      <Card className="p-4 text-sm"><div className="flex items-center gap-2 text-muted-foreground"><Power className="h-4 w-4" />Última corrida del servicio</div>
        <div className="mt-1 font-medium">{status?.servicio.ExecMainExitTimestamp || 'Nunca'} {status?.servicio.Result && status.servicio.Result !== 'success' && <EstadoBadge estado="error" />}</div></Card>
    </div>
  </div>;
}
