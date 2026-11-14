import { useEffect, useState } from 'react';
import { HardDrive, Cloud, Database } from 'lucide-react';
import { api } from '../lib/api';
import { fmtBytes } from '../lib/bytes';
import { cn } from '../lib/utils';
import { Card, CardBody, CardHeader, CardTitle, Spinner } from './ui';

type Disco = { servidor: string; montaje?: string; total?: number; usado?: number; libre?: number; leido?: string; error?: string };
type Esp = { discos: Disco[]; respaldos: { local: any; mega: any } };

function Barra({ usado, total }: { usado?: number; total?: number }) {
  const pct = total ? Math.min(100, Math.round(((usado || 0) / total) * 1000) / 10) : 0;
  return <div className="mt-2 h-2 w-full overflow-hidden rounded-full bg-muted">
    <div className={cn('h-full rounded-full', pct >= 90 ? 'bg-danger' : pct >= 75 ? 'bg-warning' : 'bg-primary')} style={{ width: `${pct}%` }} />
  </div>;
}
const pct = (u?: number, t?: number) => (t ? `${(((u || 0) / t) * 100).toFixed(1)} %` : '');

// Se lee al abrir el Resumen y cada 5 min (no en cada refresco de 60 s). El servidor cachea .100 2 min y MEGA 10 min.
export default function Espacio() {
  const [d, setD] = useState<Esp | null>(null);
  const [err, setErr] = useState('');
  useEffect(() => {
    let vivo = true;
    const leer = () => { if (document.hidden) return; api('espacio').then(j => { if (!vivo) return; if (j.ok) { setD(j); setErr(''); } else setErr(j.error || 'Sin datos'); }).catch(() => {}); };
    leer();
    const t = window.setInterval(leer, 300000);
    return () => { vivo = false; window.clearInterval(t); };
  }, []);
  const loc = d?.respaldos.local, mega = d?.respaldos.mega;
  return <div className="grid gap-6 xl:grid-cols-2">
    <Card>
      <CardHeader><CardTitle className="flex items-center gap-2"><HardDrive className="h-4 w-4 text-primary" />Espacio en disco</CardTitle></CardHeader>
      <CardBody className="space-y-4">
        {!d ? (err ? <p className="text-sm text-warning">{err}</p> : <Spinner />) : d.discos.map(x => <div key={x.servidor}>
          <div className="flex items-baseline justify-between gap-3 text-sm"><span className="font-medium">{x.servidor}</span>
            <span className="text-xs text-muted-foreground">{x.montaje}{x.leido ? ` · ${x.leido}` : ''}</span></div>
          {x.error ? <p className="text-xs text-warning">{x.error}</p> : <>
            <Barra usado={x.usado} total={x.total} />
            <div className="mt-1 flex justify-between text-xs tabular-nums text-muted-foreground">
              <span>Usado {fmtBytes(x.usado)} ({pct(x.usado, x.total)})</span><span>Libre {fmtBytes(x.libre)} de {fmtBytes(x.total)}</span></div>
          </>}
        </div>)}
      </CardBody>
    </Card>
    <Card>
      <CardHeader><CardTitle className="flex items-center gap-2"><Database className="h-4 w-4 text-primary" />Espacio de respaldos (inventario)</CardTitle></CardHeader>
      <CardBody className="space-y-4">
        {!d ? <Spinner /> : <>
          <div>
            <div className="flex items-baseline justify-between gap-3 text-sm"><span className="font-medium">Locales · pat-sql-web01</span><span className="text-xs text-muted-foreground">{loc?.leido}</span></div>
            {loc?.error ? <p className="text-xs text-warning">{loc.error}</p> : <p className="mt-1 text-sm tabular-nums">{loc?.archivos ?? 0} archivos · <b>{fmtBytes(loc?.bytes)}</b>
              {loc?.carpeta_bytes != null && <span className="text-xs text-muted-foreground"> · carpeta /var/opt/mssql/backup completa {fmtBytes(loc.carpeta_bytes)}</span>}</p>}
          </div>
          <div>
            <div className="flex items-baseline justify-between gap-3 text-sm"><span className="flex items-center gap-1.5 font-medium"><Cloud className="h-3.5 w-3.5" />MEGA</span><span className="text-xs text-muted-foreground">{mega?.leido}</span></div>
            {!mega?.ok ? <p className="text-xs text-warning">{mega?.error || 'Sin datos'}</p> : <>
              <p className="mt-1 text-sm tabular-nums">{mega.archivos} archivos en /Respaldos/inventario · <b>{fmtBytes(mega.bytes)}</b></p>
              {mega.total ? <>
                <Barra usado={mega.usado} total={mega.total} />
                <div className="mt-1 flex justify-between text-xs tabular-nums text-muted-foreground">
                  <span>Cuota usada {fmtBytes(mega.usado)} ({pct(mega.usado, mega.total)})</span><span>Libre {fmtBytes(mega.libre)} de {fmtBytes(mega.total)}</span></div>
                {mega.versiones ? <p className="mt-1 text-xs text-muted-foreground">Versiones de archivos: {fmtBytes(mega.versiones)}</p> : null}
              </> : <p className="text-xs text-muted-foreground">Cuota no disponible.</p>}
            </>}
          </div>
        </>}
      </CardBody>
    </Card>
  </div>;
}
