import { useEffect, useState } from 'react';
import { Download, Trash2, RefreshCw, AlertTriangle, Cloud, HardDrive } from 'lucide-react';
import { api } from '../lib/api';
import { fmtBytes } from '../lib/bytes';
import { Button, Input, Modal, Tabs, Spinner, useToast } from './ui';

type Origen = 'local' | 'mega';
type Archivo = { nombre: string; bytes: number; fecha: string };
type Lista = { cargando: boolean; ok?: boolean; configurado?: boolean; error?: string; archivos: Archivo[] };
const vacia: Lista = { cargando: true, archivos: [] };
const ETQ: Record<Origen, string> = { local: 'Locales', mega: 'MEGA' };

export default function RespaldosModal({ open, onClose }: { open: boolean; onClose: () => void }) {
  const toast = useToast();
  const [tab, setTab] = useState<Origen>('local');
  const [listas, setListas] = useState<Record<Origen, Lista>>({ local: vacia, mega: vacia });
  const [sinc, setSinc] = useState(false);
  const [borrar, setBorrar] = useState<{ origen: Origen; nombre: string } | null>(null);
  const [palabra, setPalabra] = useState('');
  const [ocupado, setOcupado] = useState(false);

  const cargar = (o: Origen) => {
    setListas(l => ({ ...l, [o]: { ...l[o], cargando: true } }));
    api('respaldos', { origen: o })
      .then(j => setListas(l => ({ ...l, [o]: { cargando: false, ok: j.ok, configurado: j.configurado, error: j.error, archivos: j.archivos || [] } })))
      .catch(() => setListas(l => ({ ...l, [o]: { cargando: false, ok: false, error: 'No se pudo leer', archivos: [] } })));
  };
  useEffect(() => { if (open) { setBorrar(null); cargar('local'); cargar('mega'); } }, [open]);

  const otro = (o: Origen): Origen => (o === 'local' ? 'mega' : 'local');
  const enOtro = (o: Origen, n: string) => listas[otro(o)].archivos.some(a => a.nombre === n);

  const eliminar = async () => {
    if (!borrar) return;
    setOcupado(true);
    try {
      const j = await api('respaldo_borrar', {}, { origen: borrar.origen, nombre: borrar.nombre, sincronizar: sinc ? '1' : '0', confirmar: palabra });
      if (j.ok) {
        const extra = (j.resultados || []).slice(1).map((r: any) => `${ETQ[r.origen as Origen]}: ${r.mensaje}`).join(' · ');
        toast('ok', `Eliminado ${borrar.nombre}${extra ? ' · ' + extra : ''}`);
        setBorrar(null); setPalabra('');
        cargar('local'); cargar('mega');
      } else toast('error', j.error || 'No se pudo eliminar');
    } finally { setOcupado(false); }
  };

  const l = listas[tab];
  const total = l.archivos.reduce((s, a) => s + a.bytes, 0);
  return <Modal open={open} onClose={onClose} size="full"
    title={<span className="flex items-center gap-2"><HardDrive className="h-5 w-5" />Archivos de respaldo · inventario</span>}
    footer={<Button variant="outline" onClick={onClose}>Cerrar</Button>}>
    <div className="flex flex-wrap items-center gap-4">
      <Tabs value={tab} onChange={v => { setTab(v); setBorrar(null); }} items={[
        { id: 'local', label: <span className="inline-flex items-center gap-1.5"><HardDrive className="h-3.5 w-3.5" />Locales ({listas.local.archivos.length})</span> },
        { id: 'mega', label: <span className="inline-flex items-center gap-1.5"><Cloud className="h-3.5 w-3.5" />MEGA ({listas.mega.archivos.length})</span> },
      ]} />
      <label className="inline-flex cursor-pointer items-center gap-2 text-sm">
        <input type="checkbox" className="h-4 w-4 accent-[hsl(var(--primary))]" checked={sinc} onChange={e => setSinc(e.target.checked)} />
        Sincronizar borrados (eliminar también el archivo con el mismo nombre en la otra pestaña)
      </label>
      <Button size="sm" variant="outline" className="ml-auto" onClick={() => cargar(tab)} disabled={l.cargando}><RefreshCw className="h-3.5 w-3.5" />Actualizar</Button>
    </div>
    <p className="mt-2 text-xs text-muted-foreground">
      {tab === 'local' ? 'pat-sql-web01 · /var/opt/mssql/backup (solo archivos inventario_AAAAMMDD_HHMM.bak; inventario.bak no se muestra ni se toca)' : 'Cuenta MEGA de los respaldos · /Respaldos/inventario (la subida diaria no cambia)'}
      {l.archivos.length > 0 && ` · ${l.archivos.length} archivos · ${fmtBytes(total)}`}
    </p>
    {borrar && <div className="mt-3 rounded-lg border border-danger/40 bg-danger/10 p-3 text-sm">
      <div className="flex items-center gap-2 font-medium text-danger"><AlertTriangle className="h-4 w-4" />Eliminar {borrar.nombre} de {ETQ[borrar.origen]}</div>
      <p className="mt-1 text-muted-foreground">
        {sinc
          ? (enOtro(borrar.origen, borrar.nombre) ? `También se eliminará la copia con el mismo nombre en ${ETQ[otro(borrar.origen)]}.` : `No hay copia con ese nombre en ${ETQ[otro(borrar.origen)]}.`)
          : `Solo se elimina en ${ETQ[borrar.origen]}${enOtro(borrar.origen, borrar.nombre) ? `; la copia en ${ETQ[otro(borrar.origen)]} se conserva` : ''}.`}
        {' '}No se puede deshacer.
      </p>
      <div className="mt-2 flex flex-wrap items-center gap-2">
        <Input className="w-48" value={palabra} onChange={e => setPalabra(e.target.value)} autoFocus placeholder="ELIMINAR" />
        <Button variant="danger" loading={ocupado} disabled={palabra !== 'ELIMINAR'} onClick={eliminar}><Trash2 className="h-4 w-4" />Eliminar</Button>
        <Button variant="outline" onClick={() => { setBorrar(null); setPalabra(''); }}>Cancelar</Button>
        <span className="text-xs text-muted-foreground">Escriba <b>ELIMINAR</b> para confirmar</span>
      </div>
    </div>}
    <div className="mt-3 min-h-0 flex-1 overflow-auto rounded-lg border scrollbar-thin">
      {l.cargando && !l.archivos.length ? <div className="flex h-40 items-center justify-center"><Spinner /></div>
        : !l.ok ? <div className="p-6 text-sm"><p className="font-medium text-warning">{l.error || 'No disponible'}</p>
            {tab === 'mega' && l.configurado === false && <p className="mt-1 text-muted-foreground">La cuenta MEGA se configura en pat-sql-web01 (/etc/lospaticas/mega.env, MEGAcmd).</p>}</div>
        : !l.archivos.length ? <div className="p-6 text-sm text-muted-foreground">No hay respaldos.</div>
        : <table className="w-full text-sm">
            <thead className="sticky top-0 bg-muted/80 text-left text-xs text-muted-foreground backdrop-blur"><tr>
              <th className="px-4 py-2 font-medium">Nombre</th><th className="px-4 py-2 text-right font-medium">Tamaño</th><th className="px-4 py-2 font-medium">Fecha</th><th className="px-4 py-2 font-medium">{ETQ[otro(tab)]}</th><th className="px-4 py-2" />
            </tr></thead>
            <tbody className="divide-y">{l.archivos.map(a => <tr key={a.nombre} className="hover:bg-muted/40">
              <td className="px-4 py-2 font-mono text-xs">{a.nombre}</td>
              <td className="px-4 py-2 text-right tabular-nums">{fmtBytes(a.bytes)}</td>
              <td className="px-4 py-2 tabular-nums">{a.fecha || '—'}</td>
              <td className="px-4 py-2 text-xs">{enOtro(tab, a.nombre) ? <span className="text-success">también</span> : <span className="text-muted-foreground">no</span>}</td>
              <td className="px-4 py-2"><div className="flex justify-end gap-2">
                <a className="inline-flex h-8 items-center gap-1.5 rounded-md border px-3 text-xs font-medium hover:bg-muted" href={'descargar.php?' + new URLSearchParams({ origen: tab, nombre: a.nombre })}><Download className="h-3.5 w-3.5" />Descargar</a>
                <Button size="sm" variant="danger" onClick={() => { setPalabra(''); setBorrar({ origen: tab, nombre: a.nombre }); }}><Trash2 className="h-3.5 w-3.5" />Eliminar</Button>
              </div></td>
            </tr>)}</tbody>
          </table>}
    </div>
    <p className="mt-2 text-xs text-muted-foreground">La descarga se transmite directamente (≈12 GB por archivo); puede tardar según la conexión. Eliminar queda registrado en la bitácora.</p>
  </Modal>;
}
