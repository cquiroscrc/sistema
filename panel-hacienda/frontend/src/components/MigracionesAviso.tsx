import { useState } from 'react';
import { DatabaseZap } from 'lucide-react';
import { api, type MigResumen } from '../lib/api';
import { Button, Input, Modal, useToast } from './ui';

/** Aviso global: migraciones de BD pendientes (p. ej. después de restaurar inventario) + botón para aplicarlas. */
export default function MigracionesAviso({ m, bd, onCambio }: { m?: MigResumen; bd?: string; onCambio: () => void }) {
  const toast = useToast();
  const [abierto, setAbierto] = useState(false);
  const [palabra, setPalabra] = useState('');
  const [cargando, setCargando] = useState(false);
  if (!m) return null;
  if (m.error) return <div className="mb-6 rounded-lg border border-warning/40 bg-warning/10 px-4 py-3 text-sm">No se pudo revisar las migraciones de BD: {m.error}</div>;
  if (!m.pendientes) return null;
  const aplicar = async () => {
    setCargando(true);
    try {
      const j = await api('migraciones_aplicar', {}, { confirmar: palabra });
      toast(j.ok ? 'ok' : 'error', j.mensaje || j.error);
      if (j.ok) setAbierto(false);
      onCambio();
    } finally { setCargando(false); }
  };
  return <>
    <div className="mb-6 flex flex-wrap items-center gap-3 rounded-lg border border-warning/50 bg-warning/10 px-4 py-3 text-sm">
      <DatabaseZap className="h-5 w-5 shrink-0 text-warning" />
      <div className="min-w-0 flex-1"><b>Hay {m.pendientes} migraci{m.pendientes === 1 ? 'ón pendiente' : 'ones pendientes'}</b> en la BD{bd ? ` ${bd}` : ''}: {(m.nombres ?? []).join(', ')}.
        <span className="text-muted-foreground"> Crean las tablas del panel (Buzón FE, control de migraciones) que faltan, p. ej. después de restaurar inventario.</span></div>
      <Button size="sm" onClick={() => { setPalabra(''); setAbierto(true); }}>Aplicar migraciones</Button>
    </div>
    <Modal open={abierto} onClose={() => setAbierto(false)} title="Aplicar migraciones de BD" tone="warning"
      footer={<><Button variant="outline" onClick={() => setAbierto(false)}>Cancelar</Button><Button loading={cargando} disabled={palabra !== 'MIGRAR'} onClick={aplicar}>Aplicar</Button></>}>
      <p>Se ejecutarán en <b>{bd || 'la BD actual'}</b>, cada una en su propia transacción:</p>
      <ul className="my-2 list-disc pl-5 font-mono text-xs">{(m.nombres ?? []).map(n => <li key={n}>{n}</li>)}</ul>
      <p className="text-muted-foreground">Son idempotentes: solo crean lo que falta y no borran datos. Queda registrado en la bitácora con su usuario.</p>
      <label className="mt-3 block text-xs font-medium">Escriba <b>MIGRAR</b> para confirmar</label>
      <Input className="mt-1" value={palabra} onChange={e => setPalabra(e.target.value)} autoFocus />
    </Modal>
  </>;
}
