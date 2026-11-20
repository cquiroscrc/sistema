import { useCallback, useEffect, useState , useRef} from 'react';
import { Pause, Play, Search, FileText, FileDown, Eye, RotateCcw, ChevronLeft, ChevronRight, ExternalLink, Copy } from 'lucide-react';
import { api, apiUrl, type DocRow, type Status } from '../lib/api';
import { useMantenerSesion } from '../lib/keepalive';
import { fmtMonto, fmtNum, cn } from '../lib/utils';
import { SortTh, siguienteOrden, type Orden } from '../components/SortTh';
import { Button, Card, Drawer, Empty, EstadoBadge, Input, Label, Modal, Pill, Select, Spinner, Tabs, useToast } from '../components/ui';

type F = { estado: string; tipo: string; q: string; desde: string; hasta: string };
// Columna de acciones fija a la derecha con fondo sólido (capa ::before para el tono del encabezado / hover de la fila)
const STICKY = "sticky right-0 z-10 bg-card shadow-[-6px_0_6px_-6px_rgba(0,0,0,0.25)] before:pointer-events-none before:absolute before:inset-0 before:-z-10 before:content-['']";

export default function Comprobantes({ status, onCambio }: { status: Status | null; onCambio: () => void }) {
  const [f, setF] = useState<F>({ estado: '', tipo: '', q: '', desde: '', hasta: '' });
  const [page, setPage] = useState(1);
  const [data, setData] = useState<{ total: number; size: number; rows: DocRow[] } | null>(null);
  const [sel, setSel] = useState<{ tipo: string; numero: string } | null>(null);
  const [orden, setOrden] = useState<Orden>({ col: 'fecha', dir: 'desc' });
  const [reint, setReint] = useState<DocRow | null>(null);
  const [reintEnv, setReintEnv] = useState(false);
  const toast = useToast();
  const ordRef = useRef(orden);
  // Ultima consulta aplicada (filtros enviados, pagina, orden): el auto-refresco repite exactamente esa, no lo que se este escribiendo.
  const apl = useRef<{ p: number; x: F; o: Orden } | null>(null);
  const seq = useRef(0);
  const [auto, setAuto] = useState(() => { try { return localStorage.getItem('ph-comprobantes-auto') !== 'off'; } catch { return true; } });
  const [actualizado, setActualizado] = useState<Date | null>(null);
  useMantenerSesion();
  const cargar = useCallback(async (p: number, x: F, o: Orden = ordRef.current, silencioso = false) => {
    const n = ++seq.current; apl.current = { p, x, o };
    if (!silencioso) setData(null);
    let j: any;
    try { j = await api('docs', { ...x, page: p, size: 25, orden: o.col, dir: o.dir }); } catch { if (silencioso) return; j = { ok: false }; }
    if (n !== seq.current) return; // llego una consulta mas nueva
    if (silencioso && !j.ok) return; // conservar la tabla si falla un refresco
    setData(j.ok ? j : { total: 0, size: 25, rows: [] }); setPage(p); if (j.ok) setActualizado(new Date());
  }, []);
  // Auto-refresco cada 30 s de la pagina actual con los filtros/orden aplicados; en pausa con la pestaña oculta.
  // Sin vaciar la tabla (no parpadea ni pierde el scroll); el drawer y el modal abiertos se conservan.
  useEffect(() => { try { localStorage.setItem('ph-comprobantes-auto', auto ? 'on' : 'off'); } catch {} }, [auto]);
  useEffect(() => {
    if (!auto) return;
    const tick = () => { if (!document.hidden && apl.current) { const a = apl.current; void cargar(a.p, a.x, a.o, true); } };
    const t = setInterval(tick, 30000);
    const vis = () => { if (!document.hidden) tick(); };
    document.addEventListener('visibilitychange', vis);
    return () => { clearInterval(t); document.removeEventListener('visibilitychange', vis); };
  }, [auto, cargar]);
  useEffect(() => {
    const q = new URLSearchParams((location.hash.split('?')[1] || ''));
    const doc = (q.get('doc') || '').trim();
    const tipo = (q.get('tipo') || '').trim();
    if (doc) {
      const nf = { ...f, q: doc };
      setF(nf);
      if (tipo) setSel({ tipo, numero: doc });
      cargar(1, nf);
      return;
    }
    cargar(1, f);
    /* eslint-disable-next-line */
  }, []);
  const set = (k: keyof F) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setF(s => ({ ...s, [k]: e.target.value }));
  const paginas = data ? Math.max(1, Math.ceil(data.total / data.size)) : 1;
  const reintentar = async () => {
    if (!reint) return; setReintEnv(true);
    try {
      const j = await api('reintentar', {}, { tipo: reint.tipo, numero: reint.numero });
      toast(j.ok ? 'ok' : 'error', j.mensaje || j.error); if (j.ok) { setReint(null); onCambio(); cargar(page, f); }
    } finally { setReintEnv(false); }
  };
  const ordenar = (c: string) => { const o = siguienteOrden(ordRef.current, c); ordRef.current = o; setOrden(o); cargar(1, f, o); };
  return <div className="space-y-4">
    <Card className="p-4">
      <form className="grid items-end gap-3 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-6 [&>*]:min-w-0" onSubmit={e => { e.preventDefault(); cargar(1, f); }}>
        <div><Label>Estado</Label><Select value={f.estado} onChange={set('estado')}><option value="">Todos</option><option value="aceptado">Aceptado</option><option value="rechazado">Rechazado</option>
          <option value="error">Error</option><option value="pendiente">Pendiente de respuesta</option><option value="procesando">Enviando / procesando</option><option value="reintentar">Marcado para reintento</option></Select></div>
        <div><Label>Tipo</Label><Select value={f.tipo} onChange={set('tipo')}><option value="">Todos</option><option value="FE">FE · Factura</option><option value="TE">TE · Tiquete</option><option value="NC">NC · Nota crédito</option><option value="ND">ND · Nota débito</option></Select></div>
        <div className="sm:col-span-2 lg:col-span-1 2xl:col-span-2"><Label>Buscar</Label><div className="relative"><Search className="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
          <Input className="pl-9" value={f.q} onChange={set('q')} placeholder="Número, clave, cédula o correo del receptor" /></div></div>
        <div><Label>Desde</Label><Input type="date" value={f.desde} onChange={set('desde')} /></div>
        <div className="flex gap-2"><div className="flex-1"><Label>Hasta</Label><Input type="date" value={f.hasta} onChange={set('hasta')} /></div><Button type="submit" className="mt-auto">Filtrar</Button></div>
      </form>
    </Card>
    <Card className="overflow-hidden">
      <div className="overflow-x-auto"><table className="w-full text-sm">
        <thead className="bg-muted/60 text-left text-xs uppercase tracking-wide text-muted-foreground"><tr>
          {([['Fecha', 'fecha'], ['Documento', 'documento'], ['Consecutivo / clave', 'consecutivo'], ['Receptor', 'receptor'], ['Total', 'total'], ['Estado', 'estado']] as const).map(([h, c]) =>
            <SortTh key={h} label={h} col={c} orden={orden} onSort={ordenar} className={h === 'Total' ? 'text-right' : undefined} />)}
          <th className={cn(STICKY, 'px-4 py-3 text-right font-medium before:bg-muted/60')}><span className="sr-only">Acciones</span></th></tr></thead>
        <tbody className="divide-y">
          {!data ? <tr><td colSpan={7} className="p-10 text-center"><Spinner /></td></tr> : !(data.rows ?? []).length ? <tr><td colSpan={7}><Empty>Sin comprobantes para el filtro.</Empty></td></tr> :
            (data.rows ?? []).map(r => <tr key={r.tipo + r.numero} className="group cursor-pointer hover:bg-muted/40" onClick={() => setSel({ tipo: r.tipo, numero: r.numero })}>
              <td className="whitespace-nowrap px-4 py-3 tabular-nums text-muted-foreground">{r.fecha.slice(0, 16)}</td>
              <td className="whitespace-nowrap px-4 py-3"><Pill className="mr-2">{r.tipo}</Pill><span className="font-medium">{r.numero}</span></td>
              <td className="px-4 py-3"><div className="font-mono text-xs">{r.consecutivo}</div><div className="max-w-[15rem] truncate font-mono text-[11px] text-muted-foreground" title={r.clave}>{r.clave}</div></td>
              <td className="px-4 py-3"><div>{r.receptor}</div><div className="max-w-[14rem] truncate text-xs text-muted-foreground">{r.email}</div></td>
              <td className="whitespace-nowrap px-4 py-3 text-right tabular-nums">{fmtMonto(r.total)}</td>
              <td className="px-4 py-3"><EstadoBadge estado={r.estado} />{r.error && !['aceptado'].includes(r.estado) && <div className="mt-1 max-w-[16rem] truncate text-xs text-muted-foreground" title={r.error}>{r.error}</div>}</td>
              <td className={cn(STICKY, 'whitespace-nowrap px-4 py-3 text-right before:bg-muted/40 before:opacity-0 group-hover:before:opacity-100')} onClick={e => e.stopPropagation()}>
                {r.pdf ? <a className="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-primary hover:bg-primary/10" target="_blank" rel="noreferrer" href={apiUrl('pdf', { tipo: r.tipo, numero: r.numero })}><FileText className="h-3.5 w-3.5" />PDF</a>
                  : <span className="text-xs text-muted-foreground">PDF no disponible</span>}
                <Button size="sm" variant="ghost" title="Ver detalle" aria-label="Ver detalle" onClick={() => setSel({ tipo: r.tipo, numero: r.numero })}><Eye className="h-3.5 w-3.5" /></Button>
                <span className="inline-block" title={r.reintento?.ok ? 'Reintentar envío' : `No se puede reintentar: ${r.reintento?.motivo ?? ''}`}>
                  <Button size="sm" variant="ghost" disabled={!r.reintento?.ok} aria-label={r.reintento?.ok ? 'Reintentar envío' : `No se puede reintentar: ${r.reintento?.motivo ?? ''}`} onClick={() => setReint(r)}><RotateCcw className="h-3.5 w-3.5" /></Button></span></td>
            </tr>)}
        </tbody></table></div>
      <div className="flex items-center justify-between border-t px-4 py-3 text-xs text-muted-foreground">
        <div className="flex flex-wrap items-center gap-3"><span>{data ? `${fmtNum(data.total)} comprobantes` : '…'}</span>
          <span className="inline-flex items-center gap-1.5"><span className={cn('h-2 w-2 rounded-full', auto ? 'bg-success animate-pulse' : 'bg-muted-foreground')} />{auto ? 'Auto-actualización cada 30 s' : 'Auto-actualización en pausa'}{actualizado && ` · ${actualizado.toLocaleTimeString('es-CR', { hour: '2-digit', minute: '2-digit', second: '2-digit' })}`}</span>
          <Button size="sm" variant="ghost" onClick={() => setAuto(v => !v)} aria-pressed={auto}>{auto ? <><Pause className="h-3.5 w-3.5" />Pausar</> : <><Play className="h-3.5 w-3.5" />Reanudar</>}</Button></div>
        <div className="flex items-center gap-2"><Button size="sm" variant="outline" disabled={page <= 1} onClick={() => cargar(page - 1, f)}><ChevronLeft className="h-3.5 w-3.5" /></Button>
          Página {page} de {fmtNum(paginas)}<Button size="sm" variant="outline" disabled={page >= paginas} onClick={() => cargar(page + 1, f)}><ChevronRight className="h-3.5 w-3.5" /></Button></div>
      </div>
    </Card>
    <Modal open={!!reint} onClose={() => setReint(null)} title="Reintentar envío a Hacienda" tone="warning"
      footer={<><Button variant="outline" onClick={() => setReint(null)}>Cancelar</Button><Button loading={reintEnv} onClick={reintentar}><RotateCcw className="h-4 w-4" />Devolver a la cola</Button></>}>
      <p>El documento <b>{reint?.tipo} {reint?.numero}</b> volverá a la cola de envío (estado <code>reintentar</code>) y el próximo lote del servicio lo enviará a Hacienda.</p>
      <p className="mt-2 text-muted-foreground">Se registra en la bitácora con su usuario.</p>
    </Modal>
    <DocDrawer sel={sel} onClose={() => setSel(null)} status={status} onCambio={() => { onCambio(); cargar(page, f); }} />
  </div>;
}

function Campo({ k, v, mono }: { k: string; v: React.ReactNode; mono?: boolean }) {
  return <div><div className="text-xs text-muted-foreground">{k}</div><div className={cn('mt-0.5 break-words text-sm', mono && 'font-mono text-xs')}>{v || '—'}</div></div>;
}

function claseMensajeHacienda(m: string): string {
  const e = m.split('\n', 1)[0].trim().toLocaleLowerCase('es');
  if (e.startsWith('rechaz')) return 'border-danger/40 bg-danger/10';
  if (e.includes('parcial')) return 'border-warning/40 bg-warning/10';
  if (e.startsWith('aceptado')) return 'border-success/30 bg-success/5';
  return 'border-warning/40 bg-warning/10';
}

function DocDrawer({ sel, onClose, status, onCambio }: { sel: { tipo: string; numero: string } | null; onClose: () => void; status: Status | null; onCambio: () => void }) {
  const toast = useToast();
  const [d, setD] = useState<any>(null);
  // Al cerrar, sel pasa a null mientras el drawer anima su salida: se usa la ultima seleccion (antes: apiUrl(null) -> pantalla en blanco)
  const ultimo = useRef(sel);
  if (sel) ultimo.current = sel;
  const s = sel ?? ultimo.current ?? { tipo: '', numero: '' };
  const [tab, setTab] = useState<'resumen' | 'pdf' | 'xml' | 'resp' | 'bit'>('resumen');
  const [confirmar, setConfirmar] = useState(false);
  const [enviando, setEnviando] = useState(false);
  useEffect(() => { if (!sel) return; setD(null); setTab('resumen'); api('doc', sel).then(j => { if (j.ok) setD(j); else { toast('error', j.error); onClose(); } }); }, [sel]); // eslint-disable-line
  const activo = !!status?.servicio_activo;
  const reintentar = async () => {
    if (!sel) return; setEnviando(true);
    try {
      const j = await api('reintentar', {}, { tipo: sel.tipo, numero: sel.numero });
      toast(j.ok ? 'ok' : 'error', j.mensaje || j.error); if (j.ok) { setConfirmar(false); onCambio(); api('doc', sel).then(x => x.ok && setD(x)); }
    } finally { setEnviando(false); }
  };
  const copiar = (t: string) => { navigator.clipboard?.writeText(t); toast('info', 'Copiado'); };
  const doc = d?.doc;
  return <Drawer open={!!sel} onClose={onClose}
    title={<div className="flex min-w-0 items-center gap-3"><FileText className="h-5 w-5 text-primary" /><div className="min-w-0"><div className="truncate font-semibold">{sel?.tipo} {sel?.numero}</div>
      {doc && <div className="truncate text-xs text-muted-foreground">{d.receptor_nombre || doc.NIT_RECEPTOR}</div>}</div>{doc && <EstadoBadge estado={doc.ESTADO_UI} />}</div>}
    footer={d && doc && <>
      {d.pdf ? <a target="_blank" rel="noreferrer" href={apiUrl('pdf', s)}><Button size="sm" variant="outline"><ExternalLink className="h-3.5 w-3.5" />Ver PDF</Button></a> : <span className="text-xs text-muted-foreground">PDF no disponible</span>}
      {d.xml_firmado ? <a href={apiUrl('xml', { ...s, kind: 'firmado' })}><Button size="sm" variant="outline"><FileDown className="h-3.5 w-3.5" />XML firmado</Button></a> : <span className="text-xs text-muted-foreground">XML firmado no disponible</span>}
      {d.xml_resp ? <a href={apiUrl('xml', { ...s, kind: 'respuesta' })}><Button size="sm" variant="outline"><FileDown className="h-3.5 w-3.5" />XML respuesta</Button></a> : <span className="text-xs text-muted-foreground">Respuesta no disponible</span>}
      <div className="ml-auto flex items-center gap-2">
        {!d.puede_reintentar && <span className="text-xs text-muted-foreground">{d.reintento_motivo || 'No se puede reintentar'}</span>}
        <Button size="sm" variant="primary" disabled={!d.puede_reintentar || !activo} onClick={() => setConfirmar(true)}><RotateCcw className="h-3.5 w-3.5" />Reintentar</Button></div>
    </>}>
    {!d || !doc ? <div className="flex justify-center py-20"><Spinner /></div> : <div className="space-y-5">
      <Tabs value={tab} onChange={setTab} items={[{ id: 'resumen', label: 'Resumen' }, { id: 'pdf', label: 'PDF' }, { id: 'xml', label: 'XML enviado' }, { id: 'resp', label: 'Respuesta Hacienda' }, { id: 'bit', label: `Bitácora (${(d.bitacora ?? []).length})` }]} />
      {tab === 'resumen' && <div className="space-y-5">
        <div className="grid gap-4 sm:grid-cols-3">
          <Card className="p-4"><div className="text-xs text-muted-foreground">Total comprobante</div><div className="mt-1 text-xl font-semibold tabular-nums">{fmtMonto(doc.TOTALCOMPROBANTE)}</div><div className="text-xs text-muted-foreground">Impuesto {fmtMonto(doc.TOTALIMPUESTO)}</div></Card>
          <Card className="p-4"><div className="text-xs text-muted-foreground">Emisión</div><div className="mt-1 font-medium">{doc.FECHATIMBRADO}</div><div className="text-xs text-muted-foreground">Registro {doc.FECHA_HORA}</div></Card>
          <Card className="p-4"><div className="text-xs text-muted-foreground">Envío</div><div className="mt-1 text-sm">Enviado <b>{doc.ENVIADO || '—'}</b> · Aceptado <b>{doc.ACEPTADO || '—'}</b></div><div className="text-xs text-muted-foreground">Correo al cliente: {doc.EMAIL_ENVIADO || '—'}</div></Card>
        </div>
        <div className="grid gap-4 sm:grid-cols-2">
          <Campo k="Clave" v={<span className="inline-flex items-center gap-2">{doc.SELLO_SAT}<button onClick={() => copiar(doc.SELLO_SAT)} className="text-muted-foreground hover:text-foreground"><Copy className="h-3.5 w-3.5" /></button></span>} mono />
          <Campo k="Consecutivo" v={d.consecutivo} mono />
          <Campo k="Receptor" v={`${d.receptor_nombre || ''} (${doc.NIT_RECEPTOR || '—'})`} />
          <Campo k="Correo receptor" v={doc.EMAIL_RECEPTOR} />
        </div>
        {d.mensaje_hacienda && <div className={cn('rounded-lg border p-4 text-sm', claseMensajeHacienda(d.mensaje_hacienda))}><div className="mb-1 font-medium">Mensaje de Hacienda</div><div className="whitespace-pre-wrap">{d.mensaje_hacienda}</div></div>}
        <Campo k="Último mensaje del sistema" v={doc.ULTIMO_ERROR} />
      </div>}
      {tab === 'pdf' && (d.pdf ? <iframe title="PDF" src={apiUrl('pdf', s)} className="h-[70vh] w-full rounded-lg border bg-white" /> : <Empty>PDF no disponible.</Empty>)}
      {tab === 'xml' && <pre className="max-h-[70vh] overflow-auto rounded-lg bg-[hsl(222_40%_6%)] p-4 font-mono text-xs text-slate-200 scrollbar-thin">{d.xml || 'Sin XML guardado.'}</pre>}
      {tab === 'resp' && <pre className="max-h-[70vh] overflow-auto rounded-lg bg-[hsl(222_40%_6%)] p-4 font-mono text-xs text-slate-200 scrollbar-thin">{d.xml_respuesta || doc.RESPUESTA_HACIENDA || 'Sin respuesta de Hacienda.'}</pre>}
      {tab === 'bit' && <div className="divide-y rounded-lg border font-mono text-xs">{(d.bitacora ?? []).length ? (d.bitacora ?? []).map((b: any, i: number) =>
        <div key={i} className="flex gap-3 px-3 py-1.5"><span className="shrink-0 text-muted-foreground">{b.f}</span><span className={cn('shrink-0 font-semibold', b.nivel === 'Error' ? 'text-danger' : b.nivel === 'Warning' ? 'text-warning' : 'text-muted-foreground')}>{b.nivel}</span><span className="whitespace-pre-wrap">{b.texto}</span></div>) : <Empty>Sin entradas.</Empty>}</div>}
    </div>}
    <Modal open={confirmar} onClose={() => setConfirmar(false)} title="Reintentar envío a Hacienda" tone="warning"
      footer={<><Button variant="outline" onClick={() => setConfirmar(false)}>Cancelar</Button><Button loading={enviando} onClick={reintentar}><RotateCcw className="h-4 w-4" />Marcar para reenvío</Button></>}>
      <p>El documento <b>{sel?.tipo} {sel?.numero}</b> quedará con estado <code>reintentar</code> y el próximo lote del servicio (cada minuto) lo enviará a Hacienda.</p>
      <p className="mt-2 text-muted-foreground">Se registra en la bitácora con su usuario.</p>
    </Modal>
  </Drawer>;
}
