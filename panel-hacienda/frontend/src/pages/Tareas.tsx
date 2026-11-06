import { useEffect, useState } from 'react';
import { CalendarClock, Play, Square, AlertTriangle, ShieldAlert } from 'lucide-react';
import { api } from '../lib/api';
import { cn } from '../lib/utils';
import { Button, Card, CardBody, CardHeader, CardTitle, Input, Modal, useToast } from '../components/ui';

type Tarea = {
  id: string; nombre: string; descripcion: string; hora: string; servidor: string;
  enabled: boolean; timer: Record<string, string>; servicio: Record<string, string>;
  log: string[]; log_error?: string;
};

export default function Tareas({ onCambio }: { onCambio?: () => void }) {
  const toast = useToast();
  const [items, setItems] = useState<Tarea[] | null>(null);
  const [modal, setModal] = useState<{ id: string; acc: 'enable' | 'disable'; nombre: string } | null>(null);
  const [palabra, setPalabra] = useState('');
  const [cargando, setCargando] = useState(false);
  const cargar = () => { api('tareas').then(j => { if (j.ok) setItems(j.tareas); else toast('error', j.error); }).catch(() => {}); };
  useEffect(() => { cargar(); }, []);
  const ejecutar = async () => {
    if (!modal) return;
    setCargando(true);
    try {
      const j = await api('tarea', {}, { id: modal.id, accion: modal.acc, confirmar: palabra });
      toast(j.ok ? 'ok' : 'error', j.ok ? (modal.acc === 'enable' ? 'Tarea habilitada' : 'Tarea deshabilitada') : (j.error || j.salida));
      if (j.ok) { setModal(null); setPalabra(''); cargar(); onCambio?.(); }
    } finally { setCargando(false); }
  };
  const fila = (k: string, v: React.ReactNode) => <div className="flex justify-between gap-4 py-1.5 text-sm"><span className="text-muted-foreground">{k}</span><span className="text-right font-medium">{v || '—'}</span></div>;
  const esperado = modal?.acc === 'enable' ? 'ACTIVAR' : 'DESACTIVAR';
  return <div className="space-y-6">
    <Card className="border-warning/40">
      <CardHeader><CardTitle className="flex items-center gap-2"><ShieldAlert className="h-4 w-4 text-warning" />Corte desde 172.16.12.3</CardTitle></CardHeader>
      <CardBody className="space-y-2 text-sm text-muted-foreground">
        <p>Estas tareas siguen corriendo en el servidor Windows. Activarlas aquí <b className="text-foreground">duplicaría correos</b> (cierre de caja) o escribiría sobre la copia de la BD en 172.16.12.100.</p>
        <p>En el corte: deshabilite primero las tareas programadas de Windows y luego active cada timer desde esta pantalla (escribir ACTIVAR).</p>
      </CardBody>
    </Card>
    <div className="grid gap-6 xl:grid-cols-2">
      {(items ?? []).map(t => {
        const activo = t.enabled;
        return <Card key={t.id}>
          <CardHeader>
            <div className="min-w-0">
              <CardTitle className="flex items-center gap-2"><CalendarClock className="h-4 w-4 text-primary" />{t.nombre}</CardTitle>
              <p className="mt-0.5 text-xs text-muted-foreground">{t.descripcion} · {t.hora} · {t.servidor}</p>
            </div>
            <span className={cn('inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset', activo ? 'bg-success/10 text-success ring-success/30' : 'bg-muted text-muted-foreground ring-border')}>
              <span className={cn('h-2 w-2 rounded-full', activo ? 'bg-success animate-pulse' : 'bg-muted-foreground')} />{activo ? 'HABILITADA' : 'DESHABILITADA'}</span>
          </CardHeader>
          <CardBody>
            <div className="grid gap-x-8 sm:grid-cols-2">
              <div className="divide-y">
                {fila('Timer', `${t.timer.ActiveState || '?'} · ${t.timer.UnitFileState || '?'}`)}
                {fila('Última activación', t.timer.LastTriggerUSec)}
                {fila('Próxima corrida', t.timer.NextElapseUSecRealtime)}
              </div>
              <div className="divide-y">
                {fila('Última corrida', `${t.servicio.ActiveState || '?'}/${t.servicio.SubState || '?'}`)}
                {fila('Resultado', `${t.servicio.Result || '—'} (código ${t.servicio.ExecMainStatus ?? '—'})`)}
                {fila('Inicio / fin', <>{t.servicio.ExecMainStartTimestamp || '—'}<br />{t.servicio.ExecMainExitTimestamp || ''}</>)}
              </div>
            </div>
            <div className="mt-4 flex flex-wrap gap-3">
              <Button variant="success" disabled={activo} onClick={() => { setPalabra(''); setModal({ id: t.id, acc: 'enable', nombre: t.nombre }); }}><Play className="h-4 w-4" />Activar</Button>
              <Button variant="danger" disabled={!activo} onClick={() => { setPalabra(''); setModal({ id: t.id, acc: 'disable', nombre: t.nombre }); }}><Square className="h-4 w-4" />Desactivar</Button>
            </div>
            <details className="mt-4">
              <summary className="cursor-pointer text-xs text-muted-foreground">Últimas líneas del registro ({t.log.length})</summary>
              {t.log_error && <p className="mt-1 text-xs text-warning">{t.log_error}</p>}
              <pre className="mt-2 max-h-48 overflow-auto rounded-lg bg-[hsl(222_40%_6%)] p-3 font-mono text-[11px] text-slate-200 scrollbar-thin">{t.log.length ? t.log.join('\n') : 'Sin registro todavía.'}</pre>
            </details>
          </CardBody>
        </Card>;
      })}
      {!items && <Card><CardBody className="text-sm text-muted-foreground">Cargando tareas…</CardBody></Card>}
    </div>
    <Modal open={!!modal} onClose={() => setModal(null)} tone={modal?.acc === 'enable' ? 'danger' : undefined}
      title={modal?.acc === 'enable'
        ? <span className="flex items-center gap-2 text-danger"><AlertTriangle className="h-5 w-5" />Activar {modal?.nombre}</span>
        : `Desactivar ${modal?.nombre || ''}`}
      footer={<><Button variant="outline" onClick={() => setModal(null)}>Cancelar</Button>
        <Button variant={modal?.acc === 'enable' ? 'danger' : 'danger'} loading={cargando} disabled={palabra !== esperado} onClick={ejecutar}>
          {modal?.acc === 'enable' ? <Play className="h-4 w-4" /> : <Square className="h-4 w-4" />}{modal?.acc === 'enable' ? 'Activar' : 'Desactivar'}
        </Button></>}>
      {modal?.acc === 'enable'
        ? <div className="space-y-3">
            <div className="rounded-lg bg-danger/10 p-3 text-danger">Si la misma tarea sigue activa en 172.16.12.3 se duplicará el trabajo (correo de cierre de caja o escrituras en la copia de la BD).</div>
            <div><label className="mb-1 block text-xs font-medium">Escriba <b>ACTIVAR</b> para confirmar</label><Input value={palabra} onChange={e => setPalabra(e.target.value)} autoFocus placeholder="ACTIVAR" /></div>
          </div>
        : <div className="space-y-3">
            <p>Se detiene y deshabilita el timer. No volverá a ejecutarse hasta que lo active de nuevo.</p>
            <div><label className="mb-1 block text-xs font-medium">Escriba <b>DESACTIVAR</b> para confirmar</label><Input value={palabra} onChange={e => setPalabra(e.target.value)} autoFocus placeholder="DESACTIVAR" /></div>
          </div>}
    </Modal>
  </div>;
}
