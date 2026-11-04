import { useCallback, useEffect, useRef, useState } from 'react';
import { Inbox, RefreshCw, Search, FileText, FileDown, ChevronLeft, ChevronRight, Mail, AlertTriangle, CalendarDays, Receipt, Percent, Copy, ExternalLink, Paperclip } from 'lucide-react';
import { api, apiUrl } from '../lib/api';
import { cn, fmtMoneda, fmtNum } from '../lib/utils';
import { Button, Card, CardBody, CardHeader, CardTitle, Drawer, Empty, EstadoBadge, Input, Label, Pill, Select, Spinner, Switch, Tabs, useToast } from '../components/ui';

type Fila = { id: number; clave: string; tipo: string | null; consecutivo: string | null; fecha: string | null; emisor: string; nit_emisor: string | null; receptor: string;
  para: boolean; moneda: string | null; total: number | null; impuesto: number | null; estado: string; xml: boolean; respuesta: boolean; pdf: boolean };
const TIPOS: Record<string, string> = { FE: 'Factura', TE: 'Tiquete', NC: 'Nota de crédito', ND: 'Nota de débito', FEC: 'Factura de compra', FEE: 'Exportación', REP: 'Recibo de pago' };
const vacio = { q: '', tipo: '', estado: '', desde: '', hasta: '', para: '' };

function EstadoSync({ onCambio }: { onCambio: () => void }) {
  const toast = useToast();
  const [e, setE] = useState<any>(null);
  const [lanzando, setLanzando] = useState(false);
  const cargar = useCallback(() => api('buzon_estado').then(j => j.ok && setE(j)).catch(() => {}), []);
  useEffect(() => { cargar(); }, [cargar]);
  const corriendo = !!e && (e.en_curso || e.estado?.estado === 'en curso');
  const antes = useRef(corriendo);
  useEffect(() => { const t = setInterval(cargar, corriendo ? 3000 : 20000); return () => clearInterval(t); }, [corriendo, cargar]);
  useEffect(() => { if (antes.current && !corriendo) onCambio(); antes.current = corriendo; }, [corriendo, onCambio]);
  const sync = async () => {
    setLanzando(true);
    try { const j = await api('buzon_sync', {}, {}); toast(j.ok ? 'ok' : 'error', j.mensaje || j.error); if (j.estado) setE(j); }
    finally { setLanzando(false); }
  };
  const s = e?.estado ?? {};
  const cfg = s.config ?? {};
  return <Card>
    <CardHeader>
      <div className="flex min-w-0 items-center gap-3">
        <div className="rounded-lg bg-primary/10 p-2 text-primary"><Mail className="h-5 w-5" /></div>
        <div className="min-w-0"><CardTitle>{cfg.usuario || 'facturaelectronica@lospaticas.com'}</CardTitle>
          <p className="truncate text-xs text-muted-foreground">IMAP {cfg.host || 'mail.lospaticas.com'}:{cfg.port || 993} · solo lectura · carpetas {cfg.carpetas_cfg || 'INBOX'} · cada 5 min ({e?.timer?.ActiveState === 'active' ? 'timer activo' : 'timer inactivo'})</p></div>
      </div>
      <div className="flex items-center gap-2">
        {corriendo ? <EstadoBadge estado="en curso" /> : <EstadoBadge estado={s.estado || 'nunca'} />}
        <Button size="sm" onClick={sync} loading={lanzando} disabled={corriendo}><RefreshCw className={cn('h-3.5 w-3.5', corriendo && 'animate-spin')} />Sincronizar ahora</Button>
      </div>
    </CardHeader>
    <CardBody className="grid gap-4 text-sm md:grid-cols-4">
      <div><div className="text-xs text-muted-foreground">Última corrida</div><div className="font-medium">{s.inicio || '—'}</div><div className="text-xs text-muted-foreground">{s.fin ? `fin ${s.fin}${s.segundos != null ? ` · ${s.segundos} s` : ''}` : ''}</div></div>
      <div><div className="text-xs text-muted-foreground">Último resultado</div>
        <div className="font-medium">{s.ultima ? `${fmtNum(s.ultima.correos)} correos · ${fmtNum(s.ultima.documentos)} documentos` : '—'}</div>
        <div className="text-xs text-muted-foreground">{s.ultimo_ok ? `último correcto ${s.ultimo_ok}` : ''}{s.pendientes ? ` · pendientes ${fmtNum(s.pendientes)}` : ''}</div></div>
      <div className="md:col-span-2"><div className="text-xs text-muted-foreground">Mensaje</div>
        <div className={cn('font-medium', s.estado === 'error' && 'text-danger', s.estado === 'sin credenciales' && 'text-warning')}>{s.mensaje || 'Sin ejecuciones todavía'}</div>
        {e && !e.tablas && <div className="mt-1 flex items-center gap-1.5 text-xs text-warning"><AlertTriangle className="h-3.5 w-3.5" />Las tablas FE_BUZON_* no existen en la BD actual.</div>}
        {(s.carpetas ?? []).length > 0 && <div className="mt-1 flex flex-wrap gap-1.5">{s.carpetas.map((c: any) => <Pill key={c.carpeta}>{c.carpeta}: {fmtNum(c.mensajes)} msj · {fmtNum(c.nuevos)} nuevos</Pill>)}</div>}
      </div>
      {(e?.log ?? []).length > 0 && <details className="md:col-span-4"><summary className="cursor-pointer text-xs text-muted-foreground">Bitácora de sincronización ({e.log.length})</summary>
        <pre className="mt-2 max-h-48 overflow-auto rounded-lg bg-[hsl(222_40%_6%)] p-3 font-mono text-[11px] text-slate-200 scrollbar-thin">{e.log.join('\n')}</pre></details>}
    </CardBody>
  </Card>;
}

function Kpi({ titulo, valor, sub, icon: I }: { titulo: string; valor: React.ReactNode; sub?: React.ReactNode; icon: any }) {
  return <Card className="p-5"><div className="flex items-start justify-between gap-3"><div className="min-w-0">
    <div className="text-xs font-medium uppercase tracking-wide text-muted-foreground">{titulo}</div>
    <div className="mt-2 truncate text-2xl font-semibold tabular-nums">{valor}</div>
    {sub && <div className="mt-1 text-xs text-muted-foreground">{sub}</div>}</div>
    <div className="rounded-lg bg-primary/10 p-2.5 text-primary"><I className="h-5 w-5" /></div></div></Card>;
}

function Correos() {
  const [f, setF] = useState({ q: '', estado: '' });
  const [d, setD] = useState<any>(null); const [page, setPage] = useState(1);
  const cargar = useCallback((p = 1, x = f) => { setD(null); setPage(p); api('buzon_correos', { ...x, page: p, size: 25 }).then(setD).catch(() => setD({ ok: false })); }, [f]);
  useEffect(() => { cargar(1); }, []); // eslint-disable-line
  const pags = d?.total ? Math.ceil(d.total / 25) : 1;
  return <Card className="overflow-hidden">
    <form className="flex flex-wrap items-end gap-3 border-b p-4" onSubmit={e => { e.preventDefault(); cargar(1); }}>
      <div className="min-w-[14rem] flex-1"><Label>Buscar</Label><Input value={f.q} onChange={e => setF({ ...f, q: e.target.value })} placeholder="Remitente o asunto" /></div>
      <div><Label>Estado</Label><Select value={f.estado} onChange={e => setF({ ...f, estado: e.target.value })}><option value="">Todos</option>
        {['ok', 'sin_documentos', 'propio', 'duplicado', 'muy_grande', 'error'].map(x => <option key={x} value={x}>{x.replace('_', ' ')}</option>)}</Select></div>
      <Button type="submit">Filtrar</Button>
    </form>
    <div className="overflow-x-auto"><table className="w-full text-sm">
      <thead className="bg-muted/60 text-left text-xs uppercase tracking-wide text-muted-foreground"><tr>{['Recibido', 'Remitente', 'Asunto', 'Adjuntos', 'Docs', 'Estado'].map(h => <th key={h} className="px-4 py-3 font-medium">{h}</th>)}</tr></thead>
      <tbody className="divide-y">{!d ? <tr><td colSpan={6} className="p-10 text-center"><Spinner /></td></tr> : !d.ok ? <tr><td colSpan={6}><Empty>{d.error || 'No disponible'}</Empty></td></tr> : !(d.rows ?? []).length ? <tr><td colSpan={6}><Empty>Sin correos procesados todavía.</Empty></td></tr> :
        (d.rows ?? []).map((r: any) => <tr key={r.id} className="hover:bg-muted/40">
          <td className="whitespace-nowrap px-4 py-2.5 tabular-nums text-muted-foreground">{r.recibido}</td>
          <td className="max-w-[16rem] truncate px-4 py-2.5" title={r.remitente}>{r.remitente}</td>
          <td className="max-w-[24rem] px-4 py-2.5"><div className="truncate" title={r.asunto}>{r.asunto}</div>{r.error && <div className="truncate text-xs text-danger" title={r.error}>{r.error}</div>}</td>
          <td className="px-4 py-2.5 tabular-nums">{r.adjuntos}</td><td className="px-4 py-2.5 tabular-nums">{r.documentos}</td><td className="px-4 py-2.5"><EstadoBadge estado={r.estado} /></td></tr>)}</tbody>
    </table></div>
    <Paginador page={page} pags={pags} total={d?.total} ir={p => cargar(p)} />
  </Card>;
}

function Paginador({ page, pags, total, ir }: { page: number; pags: number; total?: number; ir: (p: number) => void }) {
  return <div className="flex items-center justify-between border-t px-4 py-3 text-xs text-muted-foreground">
    <span>{total != null ? `${fmtNum(total)} registros` : ''}</span>
    <div className="flex items-center gap-2"><Button size="sm" variant="outline" disabled={page <= 1} onClick={() => ir(page - 1)}><ChevronLeft className="h-3.5 w-3.5" /></Button>
      <span>Página {page} de {pags}</span><Button size="sm" variant="outline" disabled={page >= pags} onClick={() => ir(page + 1)}><ChevronRight className="h-3.5 w-3.5" /></Button></div></div>;
}

function Detalle({ id, onClose }: { id: number | null; onClose: () => void }) {
  const toast = useToast();
  const ultimo = useRef(id); if (id) ultimo.current = id;
  const [d, setD] = useState<any>(null);
  const [tab, setTab] = useState<'resumen' | 'pdf' | 'xml' | 'resp' | 'correo'>('resumen');
  useEffect(() => { if (!id) return; setD(null); setTab('resumen'); api('buzon_doc', { id }).then(j => { if (j.ok) setD(j); else { toast('error', j.error); onClose(); } }); }, [id]); // eslint-disable-line
  const doc = d?.doc;
  const C = ({ k, v, mono }: { k: string; v: React.ReactNode; mono?: boolean }) => <div><div className="text-xs text-muted-foreground">{k}</div><div className={cn('mt-0.5 break-all text-sm', mono && 'font-mono text-xs')}>{v || '—'}</div></div>;
  const tot = doc ? [['Venta neta', 'TOTALVENTANETA'], ['Descuentos', 'TOTALDESCUENTOS'], ['Gravado', 'TOTALGRAVADO'], ['Exento', 'TOTALEXENTO'], ['Exonerado', 'TOTAL_EXONERADO'], ['Impuesto', 'TOTALIMPUESTO'], ['Otros cargos', 'TOTAL_OTROS_CARGOS'], ['IVA devuelto', 'TOTAL_IVA_DEVUELTO']].filter(([, k]) => doc[k] != null && Number(doc[k]) !== 0) : [];
  const archivo = (a: any) => apiUrl('buzon_archivo', { id: a.id, descargar: a.clase === 'pdf' ? '1' : undefined });
  return <Drawer open={!!id} onClose={onClose}
    title={<div className="flex min-w-0 items-center gap-3"><Receipt className="h-5 w-5 text-primary" /><div className="min-w-0">
      <div className="truncate font-semibold">{doc ? `${doc.TIPO_DOC || '?'} ${doc.CONSECUTIVO || ''}` : 'Documento recibido'}</div>
      {doc && <div className="truncate text-xs text-muted-foreground">{doc.NOMBRE} ({doc.NIT_EMISOR})</div>}</div>{doc && <EstadoBadge estado={doc.ESTADO_HACIENDA || 'sin_respuesta'} />}</div>}
    footer={d && doc && <>{(d.archivos ?? []).map((a: any) => <a key={a.id} href={archivo(a)} target={a.clase === 'pdf' ? '_blank' : undefined} rel="noreferrer">
      <Button size="sm" variant="outline"><FileDown className="h-3.5 w-3.5" />{{ xml: 'XML', respuesta: 'Respuesta MH', receptor: 'Mensaje receptor', pdf: 'PDF' }[a.clase as string] ?? a.clase}</Button></a>)}</>}>
    {!d || !doc ? <div className="flex justify-center py-20"><Spinner /></div> : <div className="space-y-5">
      <Tabs value={tab} onChange={setTab} items={[{ id: 'resumen', label: 'Resumen' }, { id: 'pdf', label: 'PDF' }, { id: 'xml', label: 'XML' }, { id: 'resp', label: 'Respuesta Hacienda' }, { id: 'correo', label: `Correo (${(d.correos ?? []).length})` }]} />
      {tab === 'resumen' && <div className="space-y-5">
        <div className="grid gap-4 sm:grid-cols-3">
          <Card className="p-4"><div className="text-xs text-muted-foreground">Total comprobante</div><div className="mt-1 text-xl font-semibold tabular-nums">{fmtMoneda(doc.TOTALCOMPROBANTE, doc.MONEDA)}</div><div className="text-xs text-muted-foreground">Impuesto {fmtMoneda(doc.TOTALIMPUESTO, doc.MONEDA)}{doc.TIPO_CAMBIO && doc.MONEDA !== 'CRC' ? ` · TC ${doc.TIPO_CAMBIO}` : ''}</div></Card>
          <Card className="p-4"><div className="text-xs text-muted-foreground">Emisión</div><div className="mt-1 font-medium">{doc.FECHA_EMISION_F || '—'}</div><div className="text-xs text-muted-foreground">{TIPOS[doc.TIPO_DOC] || doc.TIPO_DOC} · v{doc.VERSION || '?'}</div></Card>
          <Card className="p-4"><div className="text-xs text-muted-foreground">Receptor</div><div className="mt-1 text-sm font-medium">{doc.NOMBRE_RECEPTOR || '—'}</div><div className="text-xs">{doc.PARA_LOS_PATICAS ? <span className="text-success">Dirigido a Los Paticas</span> : <span className="text-muted-foreground">{doc.NIT_RECEPTOR || 'sin identificación'}</span>}</div></Card>
        </div>
        <div className="grid gap-4 sm:grid-cols-2">
          <C k="Clave" v={<span className="inline-flex items-center gap-2">{doc.CLAVE}<button onClick={() => { navigator.clipboard?.writeText(doc.CLAVE); toast('info', 'Copiado'); }} className="text-muted-foreground hover:text-foreground"><Copy className="h-3.5 w-3.5" /></button></span>} mono />
          <C k="Consecutivo" v={doc.CONSECUTIVO} mono />
          <C k="Emisor" v={`${doc.NOMBRE || ''} · ${doc.NIT_EMISOR || ''}`} /><C k="Correo emisor" v={doc.EMAIL_EMISOR} />
          <C k="Actividad económica" v={doc.CODIGO_ACTIVIDAD} /><C k="Ingresó al buzón" v={doc.FECHA_INGRESO_F} />
        </div>
        {tot.length > 0 && <div className="grid gap-3 rounded-lg border p-4 sm:grid-cols-4">{tot.map(([t, k]) => <div key={k}><div className="text-xs text-muted-foreground">{t}</div><div className="tabular-nums">{fmtMoneda(doc[k], doc.MONEDA)}</div></div>)}</div>}
        {doc.MENSAJE_HACIENDA && <div className={cn('rounded-lg border p-4 text-sm', doc.ESTADO_HACIENDA === 'rechazado' ? 'border-danger/40 bg-danger/10' : 'border-success/30 bg-success/5')}><div className="mb-1 font-medium">Mensaje de Hacienda</div><div className="whitespace-pre-wrap">{doc.MENSAJE_HACIENDA}</div></div>}
        {!doc.TIENE_XML && <div className="rounded-lg border border-warning/40 bg-warning/10 p-3 text-sm">Solo llegó la respuesta de Hacienda; el XML del comprobante no está en el buzón.</div>}
      </div>}
      {tab === 'pdf' && (d.pdf_id ? <><iframe title="PDF" src={apiUrl('buzon_archivo', { id: d.pdf_id })} className="h-[70vh] w-full rounded-lg border bg-white" />
        <a className="inline-flex items-center gap-1 text-xs text-primary hover:underline" target="_blank" rel="noreferrer" href={apiUrl('buzon_archivo', { id: d.pdf_id })}><ExternalLink className="h-3.5 w-3.5" />Abrir en otra pestaña</a></> : <Empty>El correo no traía PDF para este comprobante.</Empty>)}
      {tab === 'xml' && <pre className="max-h-[70vh] overflow-auto rounded-lg bg-[hsl(222_40%_6%)] p-4 font-mono text-xs text-slate-200 scrollbar-thin">{d.xml || 'Sin XML del comprobante.'}</pre>}
      {tab === 'resp' && <pre className="max-h-[70vh] overflow-auto rounded-lg bg-[hsl(222_40%_6%)] p-4 font-mono text-xs text-slate-200 scrollbar-thin">{d.xml_respuesta || 'Sin MensajeHacienda en el buzón.'}</pre>}
      {tab === 'correo' && <div className="space-y-3">{(d.correos ?? []).map((c: any) => <Card key={c.id} className="p-4 text-sm"><div className="grid gap-3 sm:grid-cols-2">
        <C k="De" v={c.remitente} /><C k="Para" v={c.para} /><div className="sm:col-span-2"><C k="Asunto" v={c.asunto} /></div>
        <C k="Fecha del correo" v={c.fecha} /><C k="Recibido (servidor)" v={c.recibido} /><C k="Carpeta" v={c.carpeta} /><C k="Message-ID" v={c.message_id} mono /></div>
        <div className="mt-3 flex flex-wrap gap-2">{(d.archivos ?? []).filter((a: any) => a.correo_id === c.id).map((a: any) =>
          <a key={a.id} href={archivo(a)} className="inline-flex items-center gap-1.5 rounded-md border px-2 py-1 text-xs hover:bg-muted"><Paperclip className="h-3 w-3" />{a.nombre}{a.zip ? ` (en ${a.zip})` : ''} · {fmtNum(Math.round(a.tamano / 1024))} KB</a>)}</div></Card>)}</div>}
    </div>}
  </Drawer>;
}

export default function BuzonFE() {
  const [vista, setVista] = useState<'docs' | 'correos'>('docs');
  const [f, setF] = useState(vacio);
  const [data, setData] = useState<any>(null);
  const [k, setK] = useState<any>(null);
  const [page, setPage] = useState(1);
  const [sel, setSel] = useState<number | null>(null);
  const fRef = useRef(f); fRef.current = f;
  const cargar = useCallback((p = 1) => { setData(null); setPage(p); api('buzon_docs', { ...fRef.current, page: p, size: 25 }).then(setData).catch(() => setData({ ok: false })); }, []);
  const cargarK = useCallback(() => api('buzon_kpis').then(setK).catch(() => {}), []);
  const refrescar = useCallback(() => { cargar(page); cargarK(); }, [cargar, cargarK, page]);
  useEffect(() => { cargar(1); cargarK(); }, [cargar, cargarK]);
  const set = (key: keyof typeof vacio) => (e: { target: { value: string } }) => setF({ ...f, [key]: e.target.value });
  const pags = data?.total ? Math.ceil(data.total / 25) : 1;
  const mes = k?.mes; const crc = (mes?.por_moneda ?? []).find((m: any) => m.moneda === 'CRC'); const otras = (mes?.por_moneda ?? []).filter((m: any) => m.moneda !== 'CRC');
  return <div className="space-y-6">
    <EstadoSync onCambio={refrescar} />
    <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <Kpi titulo="Recibidos este mes" valor={fmtNum(mes?.documentos)} sub={mes ? `${fmtNum(mes.para_los_paticas)} dirigidos a Los Paticas` : ''} icon={CalendarDays} />
      <Kpi titulo="Total del mes" valor={fmtMoneda(crc?.total ?? 0, 'CRC')} sub={otras.length ? otras.map((m: any) => fmtMoneda(m.total, m.moneda)).join(' · ') : 'Notas de crédito restan'} icon={Receipt} />
      <Kpi titulo="Impuestos del mes" valor={fmtMoneda(crc?.impuesto ?? 0, 'CRC')} sub={otras.length ? otras.map((m: any) => fmtMoneda(m.impuesto, m.moneda)).join(' · ') : ''} icon={Percent} />
      <Kpi titulo="En el buzón" valor={fmtNum(k?.total?.documentos)} sub={k?.total?.desde ? `${k.total.desde} a ${k.total.hasta} · ${fmtNum(mes?.rechazados)} rechazados este mes` : 'Sin documentos todavía'} icon={Inbox} />
    </div>
    <Tabs value={vista} onChange={setVista} items={[{ id: 'docs', label: 'Comprobantes recibidos' }, { id: 'correos', label: 'Correos procesados' }]} />
    {vista === 'correos' ? <Correos /> : <>
      <Card><CardBody>
        <form className="grid items-end gap-3 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-7 [&>*]:min-w-0" onSubmit={e => { e.preventDefault(); cargar(1); }}>
          <div className="sm:col-span-2 lg:col-span-1 2xl:col-span-2"><Label>Buscar</Label><div className="relative"><Search className="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
            <Input className="pl-9" value={f.q} onChange={set('q')} placeholder="Emisor, cédula, clave o consecutivo" /></div></div>
          <div><Label>Tipo</Label><Select value={f.tipo} onChange={set('tipo')}><option value="">Todos</option>{Object.entries(TIPOS).map(([k2, v]) => <option key={k2} value={k2}>{k2} · {v}</option>)}</Select></div>
          <div><Label>Estado Hacienda</Label><Select value={f.estado} onChange={set('estado')}><option value="">Todos</option><option value="aceptado">Aceptado</option><option value="aceptado_parcial">Aceptado parcial</option><option value="rechazado">Rechazado</option><option value="sin_respuesta">Sin respuesta</option></Select></div>
          <div><Label>Desde</Label><Input type="date" value={f.desde} onChange={set('desde')} /></div>
          <div><Label>Hasta</Label><Input type="date" value={f.hasta} onChange={set('hasta')} /></div>
          <div className="flex items-center justify-between gap-3"><Switch checked={f.para === '1'} onChange={v => setF({ ...f, para: v ? '1' : '' })} label="Solo Los Paticas" /><Button type="submit">Filtrar</Button></div>
        </form>
      </CardBody></Card>
      <Card className="overflow-hidden">
        <div className="overflow-x-auto"><table className="w-full text-sm">
          <thead className="bg-muted/60 text-left text-xs uppercase tracking-wide text-muted-foreground"><tr>{['Emisión', 'Documento', 'Emisor', 'Total', 'Estado', ''].map(h => <th key={h} className={cn('px-4 py-3 font-medium', h === 'Total' && 'text-right')}>{h}</th>)}</tr></thead>
          <tbody className="divide-y">{!data ? <tr><td colSpan={6} className="p-10 text-center"><Spinner /></td></tr> : !data.ok ? <tr><td colSpan={6}><Empty>{data.error || 'No disponible'}</Empty></td></tr> : !(data.rows ?? []).length ? <tr><td colSpan={6}><Empty>No hay comprobantes recibidos con esos filtros.</Empty></td></tr> :
            (data.rows as Fila[]).map(r => <tr key={r.id} className="cursor-pointer hover:bg-muted/40" onClick={() => setSel(r.id)}>
              <td className="whitespace-nowrap px-4 py-3 tabular-nums text-muted-foreground">{r.fecha?.slice(0, 16) || '—'}</td>
              <td className="px-4 py-3"><div className="flex items-center gap-2"><Pill>{r.tipo || '?'}</Pill><span className="font-mono text-xs">{r.consecutivo}</span></div><div className="mt-0.5 max-w-[18rem] truncate font-mono text-[11px] text-muted-foreground" title={r.clave}>{r.clave}</div></td>
              <td className="px-4 py-3"><div className="max-w-[18rem] truncate font-medium" title={r.emisor}>{r.emisor || '—'}</div><div className="text-xs text-muted-foreground">{r.nit_emisor}{r.para && <span className="ml-2 text-success">→ Los Paticas</span>}</div></td>
              <td className="whitespace-nowrap px-4 py-3 text-right tabular-nums">{fmtMoneda(r.total, r.moneda)}</td>
              <td className="px-4 py-3"><EstadoBadge estado={r.estado} /></td>
              <td className="px-4 py-3">{r.pdf ? <FileText className="h-4 w-4 text-primary" /> : null}</td></tr>)}</tbody>
        </table></div>
        <Paginador page={page} pags={pags} total={data?.total} ir={cargar} />
      </Card>
    </>}
    <Detalle id={sel} onClose={() => setSel(null)} />
  </div>;
}
