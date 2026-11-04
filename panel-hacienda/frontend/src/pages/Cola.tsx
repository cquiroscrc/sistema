import { useEffect, useState } from 'react';
import { RefreshCw, Info } from 'lucide-react';
import { api } from '../lib/api';
import { Button, Card, CardHeader, CardTitle, Empty, Pill, Spinner } from '../components/ui';

export default function Cola() {
  const [rows, setRows] = useState<any[] | null>(null);
  const cargar = () => { setRows(null); api('cola').then(j => setRows(j.ok ? j.rows : [])); };
  useEffect(cargar, []);
  return <Card>
    <CardHeader><div><CardTitle>Documentos que tomaría el próximo lote</CardTitle>
      <p className="mt-0.5 flex items-center gap-1.5 text-xs text-muted-foreground"><Info className="h-3.5 w-3.5" />Misma consulta que <code>ProcesarPendientesEnvio</code> (solo lectura). El servicio procesa DOCS_LOTE por corrida.</p></div>
      <Button size="sm" variant="outline" onClick={cargar}><RefreshCw className="h-3.5 w-3.5" />Actualizar</Button></CardHeader>
    <div className="overflow-x-auto"><table className="w-full text-sm">
      <thead className="bg-muted/60 text-left text-xs uppercase tracking-wide text-muted-foreground"><tr>{['Fecha', 'Tipo', 'Documento', 'Cédula receptor', 'Estado previo'].map(h => <th key={h} className="px-4 py-3 font-medium">{h}</th>)}</tr></thead>
      <tbody className="divide-y">{!rows ? <tr><td colSpan={5} className="p-8 text-center"><Spinner /></td></tr> : !rows.length ? <tr><td colSpan={5}><Empty>Cola vacía.</Empty></td></tr> :
        rows.map(r => <tr key={r.DOCUMENTO} className="hover:bg-muted/40"><td className="px-4 py-2.5 tabular-nums">{r.FECHA}</td><td className="px-4 py-2.5"><Pill>{r.TIPO_DOCUMENTO}</Pill></td>
          <td className="px-4 py-2.5 font-medium">{r.DOCUMENTO}</td><td className="px-4 py-2.5">{r.NIT}</td><td className="px-4 py-2.5 text-muted-foreground">{r.ESTADO || 'nuevo'}</td></tr>)}</tbody>
    </table></div>
  </Card>;
}
