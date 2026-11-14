import { useCallback, useEffect, useRef, useState } from 'react';
import { RefreshCw, Info, Search, ChevronLeft, ChevronRight } from 'lucide-react';
import { api } from '../lib/api';
import { fmtMonto, fmtNum } from '../lib/utils';
import { SortTh, siguienteOrden, type Orden } from '../components/SortTh';
import { Button, Card, Empty, Input, Label, Pill, Select, Spinner } from '../components/ui';

type F = { estado: string; tipo: string; q: string; desde: string; hasta: string };
type Fila = { TIPO_DOCUMENTO: string; DOCUMENTO: string; NIT: string; CLIENTE: string; CORREO: string; TOTAL: number | null; SAT: string; CLAVE: string; FECHA: string; ESTADO: string };
const VACIO: F = { estado: '', tipo: '', q: '', desde: '', hasta: '' };

export default function Cola() {
  const [f, setF] = useState<F>(VACIO);
  const [page, setPage] = useState(1);
  const [data, setData] = useState<{ total: number; size: number; rows: Fila[] } | null>(null);
  const [orden, setOrden] = useState<Orden>({ col: 'fecha', dir: 'asc' });
  const ordRef = useRef(orden);
  const cargar = useCallback(async (p: number, x: F, o: Orden = ordRef.current) => {
    setData(null);
    try { const j = await api('cola', { ...x, page: p, size: 25, orden: o.col, dir: o.dir }); setData(j.ok ? j : { total: 0, size: 25, rows: [] }); setPage(p); }
    catch { /* 401 manejado */ }
  }, []);
  useEffect(() => { cargar(1, VACIO); }, [cargar]);
  const set = (k: keyof F) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setF(s => ({ ...s, [k]: e.target.value }));
  const paginas = data ? Math.max(1, Math.ceil(data.total / data.size)) : 1;
  const ordenar = (c: string) => { const o = siguienteOrden(ordRef.current, c); ordRef.current = o; setOrden(o); cargar(1, f, o); };
  return <div className="space-y-4">
    <Card className="p-4">
      <form className="grid items-end gap-3 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-6 [&>*]:min-w-0" onSubmit={e => { e.preventDefault(); cargar(1, f); }}>
        <div><Label>Estado previo</Label><Select value={f.estado} onChange={set('estado')}><option value="">Todos</option><option value="nuevo">Nuevo (sin procesar)</option>
          <option value="reintentar">Marcado para reintento</option><option value="procesando">Enviando / procesando</option><option value="otro">Otro</option></Select></div>
        <div><Label>Tipo</Label><Select value={f.tipo} onChange={set('tipo')}><option value="">Todos</option><option value="FE">FE · Factura</option><option value="TE">TE · Tiquete</option><option value="NC">NC · Nota crédito</option><option value="ND">ND · Nota débito</option></Select></div>
        <div className="sm:col-span-2 lg:col-span-1 2xl:col-span-2"><Label>Buscar</Label><div className="relative"><Search className="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
          <Input className="pl-9" value={f.q} onChange={set('q')} placeholder="Documento, clave, consecutivo, cédula, cliente o correo" /></div></div>
        <div><Label>Desde</Label><Input type="date" value={f.desde} onChange={set('desde')} /></div>
        <div className="flex gap-2"><div className="flex-1"><Label>Hasta</Label><Input type="date" value={f.hasta} onChange={set('hasta')} /></div><Button type="submit" className="mt-auto">Filtrar</Button></div>
      </form>
    </Card>
    <Card className="overflow-hidden">
      <div className="flex flex-wrap items-center justify-between gap-3 border-b px-4 py-3">
        <p className="flex items-center gap-1.5 text-xs text-muted-foreground"><Info className="h-3.5 w-3.5" />Documentos que tomaría el servicio de envío. Misma consulta que <code>ProcesarPendientesEnvio</code> (solo lectura), por defecto del más antiguo al más reciente (clic en un encabezado para ordenar).</p>
        <div className="flex gap-2"><Button size="sm" variant="outline" onClick={() => { setF(VACIO); cargar(1, VACIO); }}>Limpiar</Button>
          <Button size="sm" variant="outline" onClick={() => cargar(page, f)}><RefreshCw className="h-3.5 w-3.5" />Actualizar</Button></div>
      </div>
      <div className="overflow-x-auto"><table className="w-full text-sm">
        <thead className="bg-muted/60 text-left text-xs uppercase tracking-wide text-muted-foreground"><tr>
          {([['Fecha', 'fecha'], ['Documento', 'documento'], ['Consecutivo / clave', 'consecutivo'], ['Receptor', 'receptor'], ['Total', 'total'], ['Estado previo', 'estado']] as const).map(([h, c]) =>
            <SortTh key={h} label={h} col={c} orden={orden} onSort={ordenar} className={h === 'Total' ? 'text-right' : undefined} />)}</tr></thead>
        <tbody className="divide-y">{!data ? <tr><td colSpan={6} className="p-10 text-center"><Spinner /></td></tr> : !data.rows.length ? <tr><td colSpan={6}><Empty>Sin documentos en cola para el filtro.</Empty></td></tr> :
          data.rows.map(r => <tr key={r.TIPO_DOCUMENTO + r.DOCUMENTO} className="hover:bg-muted/40">
            <td className="whitespace-nowrap px-4 py-2.5 tabular-nums text-muted-foreground">{r.FECHA.slice(0, 16)}</td>
            <td className="whitespace-nowrap px-4 py-2.5"><Pill className="mr-2">{r.TIPO_DOCUMENTO}</Pill><span className="font-medium">{r.DOCUMENTO}</span></td>
            <td className="px-4 py-2.5"><div className="font-mono text-xs">{r.CLAVE || '—'}</div><div className="max-w-[15rem] truncate font-mono text-[11px] text-muted-foreground" title={r.SAT}>{r.SAT}</div></td>
            <td className="px-4 py-2.5"><div>{r.NIT}{r.CLIENTE && <span className="text-muted-foreground"> · {r.CLIENTE}</span>}</div><div className="max-w-[14rem] truncate text-xs text-muted-foreground">{r.CORREO}</div></td>
            <td className="whitespace-nowrap px-4 py-2.5 text-right tabular-nums">{fmtMonto(r.TOTAL)}</td>
            <td className="px-4 py-2.5 text-muted-foreground">{r.ESTADO || 'nuevo'}</td>
          </tr>)}</tbody>
      </table></div>
      <div className="flex items-center justify-between border-t px-4 py-3 text-xs text-muted-foreground">
        <span>{data ? `${fmtNum(data.total)} documentos en cola` : '…'}</span>
        <div className="flex items-center gap-2"><Button size="sm" variant="outline" disabled={page <= 1} onClick={() => cargar(page - 1, f)}><ChevronLeft className="h-3.5 w-3.5" /></Button>
          Página {page} de {fmtNum(paginas)}<Button size="sm" variant="outline" disabled={page >= paginas} onClick={() => cargar(page + 1, f)}><ChevronRight className="h-3.5 w-3.5" /></Button></div>
      </div>
    </Card>
  </div>;
}
