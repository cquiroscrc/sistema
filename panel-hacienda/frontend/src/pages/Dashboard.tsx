import { useEffect, useState } from 'react';
import { Send, XOctagon, Hourglass, Power, CheckCircle2, Clock, ArrowRight } from 'lucide-react';
import { Bar, BarChart, CartesianGrid, Legend, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { api, type DocRow, type Status } from '../lib/api';
import { cn, fmtMonto, fmtNum } from '../lib/utils';
import { Card, CardBody, CardHeader, CardTitle, EstadoBadge, Empty, Spinner, Tabs } from '../components/ui';
import type { Vista } from '../App';

function Kpi({ titulo, valor, sub, icon: Icon, tono }: { titulo: string; valor: React.ReactNode; sub?: React.ReactNode; icon: any; tono: 'primary' | 'danger' | 'warning' | 'success' | 'muted' }) {
  const t = { primary: 'bg-primary/10 text-primary', danger: 'bg-danger/10 text-danger', warning: 'bg-warning/15 text-warning', success: 'bg-success/10 text-success', muted: 'bg-muted text-muted-foreground' }[tono];
  return <Card className="p-5"><div className="flex items-start justify-between">
    <div><div className="text-xs font-medium uppercase tracking-wide text-muted-foreground">{titulo}</div><div className="mt-2 text-3xl font-semibold tabular-nums">{valor}</div>{sub && <div className="mt-1 text-xs text-muted-foreground">{sub}</div>}</div>
    <div className={cn('rounded-lg p-2.5', t)}><Icon className="h-5 w-5" /></div></div></Card>;
}

export default function Dashboard({ status, ir }: { status: Status | null; ir: (v: Vista) => void }) {
  const [dias, setDias] = useState<'14' | '30' | '90'>('30');
  const [serie, setSerie] = useState<any[] | null>(null);
  const [recientes, setRecientes] = useState<DocRow[] | null>(null);
  useEffect(() => { setSerie(null); api('grafico', { dias }).then(j => setSerie(j.ok ? j.rows : [])); }, [dias]);
  useEffect(() => { api('docs', { estado: 'rechazado', size: 6 }).then(j => setRecientes(j.ok ? j.rows.slice(0, 6) : [])); }, []);
  const c = status?.conteos;
  const activo = !!status?.servicio_activo;
  return <div className="space-y-6">
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
    <div className="grid gap-4 md:grid-cols-3">
      <Card className="p-4 text-sm"><div className="flex items-center gap-2 text-muted-foreground"><Clock className="h-4 w-4" />Última bitácora</div><div className="mt-1 font-medium tabular-nums">{status?.ultima_bitacora || '—'}</div></Card>
      <Card className="p-4 text-sm"><div className="flex items-center gap-2 text-muted-foreground"><CheckCircle2 className="h-4 w-4" />Último comprobante</div><div className="mt-1 font-medium tabular-nums">{status?.ultimo_documento || '—'}</div></Card>
      <Card className="p-4 text-sm"><div className="flex items-center gap-2 text-muted-foreground"><Power className="h-4 w-4" />Última corrida del servicio</div>
        <div className="mt-1 font-medium">{status?.servicio.ExecMainExitTimestamp || 'Nunca'} {status?.servicio.Result && status.servicio.Result !== 'success' && <EstadoBadge estado="error" />}</div></Card>
    </div>
  </div>;
}
