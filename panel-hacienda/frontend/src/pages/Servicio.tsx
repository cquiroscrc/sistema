import { useState } from 'react';
import { Play, Square, AlertTriangle, ShieldAlert, Timer, Activity } from 'lucide-react';
import { api, type Status } from '../lib/api';
import { cn } from '../lib/utils';
import { Button, Card, CardBody, CardHeader, CardTitle, Input, Modal, useToast } from '../components/ui';

export default function Servicio({ status, onCambio }: { status: Status | null; onCambio: () => void }) {
  const toast = useToast();
  const [modal, setModal] = useState<'start' | 'stop' | null>(null);
  const [palabra, setPalabra] = useState('');
  const [cargando, setCargando] = useState(false);
  const activo = !!status?.servicio_activo;
  const t = status?.timer || {}, s = status?.servicio || {};
  const ejecutar = async (accion: 'start' | 'stop') => {
    setCargando(true);
    try {
      const j = await api('servicio', {}, { accion, confirmar: palabra });
      toast(j.ok ? 'ok' : 'error', j.ok ? (accion === 'start' ? 'Envío automático iniciado y habilitado' : 'Envío automático detenido y deshabilitado') : (j.error || j.salida));
      if (j.ok) { setModal(null); setPalabra(''); }
      onCambio();
    } finally { setCargando(false); }
  };
  const fila = (k: string, v: React.ReactNode) => <div className="flex justify-between gap-4 py-2 text-sm"><span className="text-muted-foreground">{k}</span><span className="text-right font-medium">{v || '—'}</span></div>;
  return <div className="grid gap-6 xl:grid-cols-3">
    <Card className="xl:col-span-2">
      <CardHeader><CardTitle>Servicio <code className="font-mono text-xs">paticas-hacienda-sender</code></CardTitle>
        <span className={cn('inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset', activo ? 'bg-success/10 text-success ring-success/30' : 'bg-muted text-muted-foreground ring-border')}>
          <span className={cn('h-2 w-2 rounded-full', activo ? 'bg-success animate-pulse' : 'bg-muted-foreground')} />{activo ? 'ACTIVO' : 'DETENIDO'}</span></CardHeader>
      <CardBody>
        <div className="grid gap-x-8 sm:grid-cols-2">
          <div className="divide-y">{fila('Timer', `${t.ActiveState || '?'} · ${t.UnitFileState || '?'}`)}{fila('Última activación', t.LastTriggerUSec)}{fila('Próxima corrida', t.NextElapseUSecRealtime)}</div>
          <div className="divide-y">{fila('Última corrida', `${s.ActiveState || '?'}/${s.SubState || '?'}`)}{fila('Resultado', `${s.Result || '—'} (código ${s.ExecMainStatus ?? '—'})`)}{fila('Inicio / fin', <>{s.ExecMainStartTimestamp || '—'}<br />{s.ExecMainExitTimestamp || ''}</>)}</div>
        </div>
        <div className="mt-6 flex flex-wrap gap-3">
          <Button variant="success" disabled={activo} onClick={() => { setPalabra(''); setModal('start'); }}><Play className="h-4 w-4" />Iniciar envío automático</Button>
          <Button variant="danger" disabled={!activo} onClick={() => setModal('stop')}><Square className="h-4 w-4" />Detener envío automático</Button>
        </div>
        <p className="mt-4 text-xs text-muted-foreground">Iniciar habilita y arranca el timer (persiste tras reinicios); corre <code>admin.php</code> cada minuto con bloqueo para que nunca se solapen dos lotes. Detener lo deshabilita y lo para: el lote en curso termina normalmente.</p>
      </CardBody>
    </Card>
    <Card className="border-warning/40">
      <CardHeader><CardTitle className="flex items-center gap-2"><ShieldAlert className="h-4 w-4 text-warning" />Antes de iniciar</CardTitle></CardHeader>
      <CardBody className="space-y-3 text-sm">
        <div className="flex gap-3"><Timer className="mt-0.5 h-4 w-4 shrink-0 text-warning" /><p>Deshabilite la <b>tarea programada en 172.16.12.3</b> que ejecuta admin.php. Si ambos servidores están activos los comprobantes se envían dos veces.</p></div>
        <div className="flex gap-3"><Activity className="mt-0.5 h-4 w-4 shrink-0 text-warning" /><p>La BD <b>{status?.bd.database}</b> en <b>{status?.bd.host}</b> es una copia{status?.bd.restaurada ? ` restaurada el ${status.bd.restaurada}` : ''}: haga la restauración final y aplique la corrección de rutas antes.</p></div>
      </CardBody>
    </Card>
    <Modal open={modal === 'start'} onClose={() => setModal(null)} tone="danger" title={<span className="flex items-center gap-2 text-danger"><AlertTriangle className="h-5 w-5" />Iniciar envío a Hacienda desde este servidor</span>}
      footer={<><Button variant="outline" onClick={() => setModal(null)}>Cancelar</Button><Button variant="danger" loading={cargando} disabled={palabra !== 'INICIAR'} onClick={() => ejecutar('start')}><Play className="h-4 w-4" />Iniciar</Button></>}>
      <div className="space-y-3">
        <div className="rounded-lg bg-danger/10 p-3 text-danger"><b>172.16.12.3 sigue enviando en producción.</b> Si su tarea programada no está deshabilitada, los comprobantes se enviarán <b>dos veces</b> a Hacienda.</div>
        <ul className="list-disc space-y-1 pl-5 text-muted-foreground">
          <li>La tarea programada de Windows en 172.16.12.3 está <b>deshabilitada</b> y su última corrida terminó.</li>
          <li>La BD de {status?.bd.host} ya es la <b>restauración final</b> (hoy es una copia) y la <b>corrección de rutas</b> está aplicada sobre ella.</li>
        </ul>
        <div><label className="mb-1 block text-xs font-medium">Escriba <b>INICIAR</b> para confirmar</label><Input value={palabra} onChange={e => setPalabra(e.target.value)} autoFocus placeholder="INICIAR" /></div>
      </div>
    </Modal>
    <Modal open={modal === 'stop'} onClose={() => setModal(null)} title="Detener envío automático"
      footer={<><Button variant="outline" onClick={() => setModal(null)}>Cancelar</Button><Button variant="danger" loading={cargando} onClick={() => ejecutar('stop')}><Square className="h-4 w-4" />Detener</Button></>}>
      Se detiene y deshabilita el timer. Ningún documento se enviará desde este servidor hasta que lo vuelva a iniciar.
    </Modal>
  </div>;
}
