import { useCallback, useEffect, useState } from 'react';
import { CheckCircle2, Undo2, FolderSync, AlertTriangle, RefreshCw } from 'lucide-react';
import { api } from '../lib/api';
import { cn } from '../lib/utils';
import { Button, Card, CardBody, CardHeader, CardTitle, Input, Modal, Spinner, useToast } from '../components/ui';

export default function RutasDEData() {
  const toast = useToast();
  const [r, setR] = useState<any>(null);
  const [dd, setDd] = useState<any>(null);
  const [modal, setModal] = useState<'aplicar' | 'revertir' | 'sync' | null>(null);
  const [palabra, setPalabra] = useState('');
  const [cargando, setCargando] = useState(false);
  const cargarR = useCallback(() => api('rutas').then(j => { if (!j.ok) toast('error', j.error); setR(j); }), [toast]);
  const cargarDd = useCallback(() => api('dedata').then(setDd), []);
  useEffect(() => { cargarR(); cargarDd(); }, [cargarR, cargarDd]);
  useEffect(() => { if (!dd?.en_curso) return; const t = setTimeout(cargarDd, 5000); return () => clearTimeout(t); }, [dd, cargarDd]);
  const accion = async (a: 'rutas_aplicar' | 'rutas_revertir' | 'dedata_sync', post: Record<string, string>) => {
    setCargando(true);
    try { const j = await api(a, {}, post); toast(j.ok ? 'ok' : 'error', j.mensaje || j.error); if (j.ok) setModal(null);
      if (a === 'dedata_sync') { if (j.log) setDd(j); else cargarDd(); } else if (j.filas) setR(j); else cargarR(); }
    finally { setCargando(false); }
  };
  const u = r?.ultimo_respaldo;
  return <div className="grid gap-6 2xl:grid-cols-5">
    <Card className="2xl:col-span-3">
      <CardHeader><div><CardTitle>Corrección de rutas Windows → Linux</CardTitle><p className="mt-0.5 text-xs text-muted-foreground"><code>/etc/lospaticas/corte-hacienda.sql</code> sobre la BD de <code>db.php</code>{r?.bd && <> (<b>{r.bd.host}/{r.bd.database}</b>)</>}</p></div>
        {r?.ok && (r.aplicado ? <span className="inline-flex items-center gap-1.5 rounded-full bg-success/10 px-2.5 py-1 text-xs font-medium text-success ring-1 ring-inset ring-success/30"><CheckCircle2 className="h-3.5 w-3.5" />Rutas Linux aplicadas</span>
          : <span className="rounded-full bg-muted px-2.5 py-1 text-xs font-medium text-muted-foreground">Rutas Windows (sin aplicar)</span>)}</CardHeader>
      <CardBody className="space-y-4">
        <div className="flex gap-3 rounded-lg border border-info/30 bg-info/10 p-3 text-sm"><AlertTriangle className="h-4 w-4 shrink-0 text-info" />
          <p>Actualiza en <b>dbo.COMPAÑIAS</b> las rutas del certificado, XML/PDF y plantillas, guardando respaldo para <b>Revertir</b>. No inicia el servicio. <b>La restauración final de la BD en el corte sobrescribe estos valores: vuelva a aplicarlo después.</b></p></div>
        {!r ? <Spinner /> : r.ok && <div className="overflow-x-auto rounded-lg border"><table className="w-full text-xs">
          <thead className="bg-muted/60 text-left uppercase tracking-wide text-muted-foreground"><tr><th className="px-3 py-2">Campo</th><th className="px-3 py-2">Actual</th><th className="px-3 py-2">Nuevo</th><th /></tr></thead>
          <tbody className="divide-y">{r.filas.map((f: any) => <tr key={f.campo} className={cn(f.cambia && 'bg-warning/5')}>
            <td className="px-3 py-2 font-mono">{f.campo}</td><td className="break-all px-3 py-2 font-mono text-muted-foreground">{f.actual}</td><td className="break-all px-3 py-2 font-mono">{f.nuevo}</td>
            <td className="px-3 py-2">{f.cambia ? <span className="rounded bg-warning/15 px-1.5 py-0.5 font-medium text-warning">cambia</span> : <span className="rounded bg-success/10 px-1.5 py-0.5 font-medium text-success">igual</span>}</td></tr>)}</tbody></table></div>}
        <div className="flex flex-wrap items-center gap-3">
          <Button disabled={!r?.ok || r.aplicado} onClick={() => { setPalabra(''); setModal('aplicar'); }}><CheckCircle2 className="h-4 w-4" />Aplicar rutas</Button>
          <Button variant="outline" disabled={!u || !!u.revertido} onClick={() => { setPalabra(''); setModal('revertir'); }}><Undo2 className="h-4 w-4" />Revertir al respaldo</Button>
          {u && <span className="text-xs text-muted-foreground">Último respaldo {u.archivo} · {u.fecha} · {u.usuario}{u.revertido && ` · revertido ${u.revertido}`}</span>}
        </div>
        {r?.historial?.length > 0 && <div><div className="mb-1 text-xs font-medium text-muted-foreground">Historial</div><pre className="max-h-40 overflow-auto rounded-lg bg-muted p-3 text-[11px] scrollbar-thin">{r.historial.join('\n')}</pre></div>}
      </CardBody>
    </Card>
    <Card className="2xl:col-span-2">
      <CardHeader><div><CardTitle className="flex items-center gap-2"><FolderSync className="h-4 w-4 text-primary" />Copia de DEData (XML/PDF)</CardTitle><p className="mt-0.5 text-xs text-muted-foreground">//172.16.12.3/DEData → <code>{dd?.destino}</code> · incremental, nunca borra</p></div>
        <Button size="sm" variant="ghost" onClick={cargarDd}><RefreshCw className="h-3.5 w-3.5" /></Button></CardHeader>
      <CardBody className="space-y-3">
        <div className="flex flex-wrap items-center gap-2 text-sm">
          {dd?.en_curso && <span className="inline-flex items-center gap-1.5 rounded-full bg-info/10 px-2.5 py-1 text-xs font-medium text-info"><Spinner />En curso</span>}
          {dd && !dd.credenciales && <span className="rounded-full bg-warning/15 px-2.5 py-1 text-xs font-medium text-warning">Sin credenciales permanentes</span>}
        </div>
        <div className="text-xs text-muted-foreground"><b>Última:</b> {dd?.ultimo_fin || dd?.ultimo_inicio || 'nunca'}</div>
        {dd && !dd.credenciales && <p className="text-xs text-muted-foreground">El botón necesita <code>/etc/lospaticas/smb-dedata.cred</code> (root 600). Sin él, ejecútelo con <code>rw.ps1</code>.</p>}
        <Button disabled={dd?.en_curso} onClick={() => setModal('sync')}><FolderSync className="h-4 w-4" />Sincronizar DEData</Button>
        <pre className="max-h-80 overflow-auto rounded-lg bg-[hsl(222_40%_6%)] p-3 font-mono text-[11px] text-slate-200 scrollbar-thin">{(dd?.log || []).join('\n') || 'Sin ejecuciones.'}</pre>
      </CardBody>
    </Card>
    <Modal open={modal === 'aplicar' || modal === 'revertir'} onClose={() => setModal(null)} tone="warning" title={modal === 'aplicar' ? 'Aplicar rutas Linux' : 'Revertir rutas'}
      footer={<><Button variant="outline" onClick={() => setModal(null)}>Cancelar</Button>
        <Button loading={cargando} disabled={palabra !== (modal === 'aplicar' ? 'APLICAR' : 'REVERTIR')} onClick={() => accion(modal === 'aplicar' ? 'rutas_aplicar' : 'rutas_revertir', { confirmar: palabra })}>Confirmar</Button></>}>
      <p>{modal === 'aplicar' ? 'Se actualizarán los campos marcados en dbo.COMPAÑIAS en una transacción, con respaldo de los valores actuales.' : 'Se restaurarán los valores del último respaldo.'}</p>
      <label className="mb-1 mt-3 block text-xs font-medium">Escriba <b>{modal === 'aplicar' ? 'APLICAR' : 'REVERTIR'}</b> para confirmar</label>
      <Input value={palabra} onChange={e => setPalabra(e.target.value)} autoFocus />
    </Modal>
    <Modal open={modal === 'sync'} onClose={() => setModal(null)} title="Sincronizar DEData"
      footer={<><Button variant="outline" onClick={() => setModal(null)}>Cancelar</Button><Button loading={cargando} onClick={() => accion('dedata_sync', { confirmar: '1' })}>Sincronizar</Button></>}>
      Copia solo archivos nuevos o modificados desde 172.16.12.3 (solo lectura). Corre en segundo plano.
    </Modal>
  </div>;
}
