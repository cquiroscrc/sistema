import { useEffect, useRef, useState } from 'react';
import { BadgeCheck, Upload, KeyRound, AlertTriangle, FileKey2 } from 'lucide-react';
import { api, apiUpload } from '../lib/api';
import { fmtBytes } from '../lib/bytes';
import { Button, Card, CardBody, CardHeader, CardTitle, Input, Label, Modal, Spinner, useToast } from './ui';

type Archivo = { existe: boolean; nombre?: string; tamano?: number; modificado?: string; legible?: boolean; aviso?: string;
  sujeto?: string; emisor?: string; desde?: string; hasta?: string; dias?: number | null; vencido?: boolean };
type Amb = { ambiente: 'PROD' | 'STAG'; activo: boolean; url: string; ruta: string; pin_configurado: boolean; archivo?: Archivo; error?: string };
type Datos = { ambiente: string; max: number; ambientes: Amb[] };

const fila = (k: string, v: React.ReactNode) => <div className="flex justify-between gap-4 py-1.5 text-sm"><span className="shrink-0 text-muted-foreground">{k}</span><span className="min-w-0 break-all text-right font-medium">{v || '—'}</span></div>;

export default function Certificados() {
  const toast = useToast();
  const [d, setD] = useState<Datos | null>(null);
  const [err, setErr] = useState('');
  const [sub, setSub] = useState<Amb | null>(null);
  const cargar = () => api('certificados').then(j => { if (j.ok) { setD(j); setErr(''); } else setErr(j.error); }).catch(() => {});
  useEffect(() => { cargar(); }, []);
  if (err) return <Card><CardBody><p className="text-sm text-danger">{err}</p><Button className="mt-3" variant="outline" onClick={cargar}>Reintentar</Button></CardBody></Card>;
  if (!d) return <div className="flex h-40 items-center justify-center"><Spinner /></div>;
  return <div className="space-y-4">
    <div className="grid gap-4 xl:grid-cols-2">
      {d.ambientes.map(a => {
        const f = a.archivo;
        return <Card key={a.ambiente}>
          <CardHeader>
            <CardTitle className="flex items-center gap-2"><FileKey2 className="h-4 w-4 text-primary" />Certificado {a.ambiente === 'PROD' ? 'producción' : 'pruebas (staging)'}</CardTitle>
            {a.activo && <span className="rounded-full bg-success/10 px-3 py-1 text-xs font-semibold text-success ring-1 ring-inset ring-success/30">EN USO</span>}
          </CardHeader>
          <CardBody>
            <div className="divide-y">
              {fila('Archivo configurado', <code className="text-xs">{a.url}</code>)}
              {fila('Carpeta', <code className="text-xs">{a.ruta}</code>)}
              {fila('PIN', a.pin_configurado ? 'configurado (no se muestra)' : 'sin configurar')}
              {f?.existe && <>
                {fila('Archivo actual', `${f.nombre} · ${fmtBytes(f.tamano ?? 0)} · modificado ${f.modificado}`)}
                {f.legible ? <>
                  {fila('Titular', f.sujeto)}
                  {fila('Emisor', f.emisor)}
                  {fila('Vigencia', <span className={f.vencido ? 'text-danger' : (f.dias ?? 99) < 30 ? 'text-warning' : ''}>{f.desde} → {f.hasta}{f.dias != null && (f.vencido ? ' · VENCIDO' : ` · ${f.dias} días`)}</span>)}
                </> : fila('Lectura', <span className="text-warning">{f.aviso}</span>)}
              </>}
              {f && !f.existe && fila('Archivo actual', <span className="text-warning">No existe todavía</span>)}
            </div>
            {a.error && <p className="mt-3 flex items-center gap-2 text-sm text-danger"><AlertTriangle className="h-4 w-4" />{a.error}</p>}
            <div className="mt-4"><Button variant="outline" disabled={!!a.error} onClick={() => setSub(a)}><Upload className="h-4 w-4" />Subir certificado…</Button></div>
          </CardBody>
        </Card>;
      })}
    </div>
    <p className="text-xs text-muted-foreground">La facturación electrónica firma con el archivo <code>IDP_&lt;AMBIENTE&gt;_CERT_URL</code> y el PIN <code>IDP_&lt;AMBIENTE&gt;_PIN</code> de COMPAÑIAS (ambiente actual: {d.ambiente}). Al subir, el archivo actual se respalda en la misma carpeta con la fecha en el nombre.</p>
    <SubirModal amb={sub} max={d.max} onClose={() => setSub(null)} onOk={j => { setD(j); setSub(null); toast('ok', j.mensaje); if (j.aviso) toast('aviso', j.aviso); }} />
  </div>;
}

function SubirModal({ amb, max, onClose, onOk }: { amb: Amb | null; max: number; onClose: () => void; onOk: (j: any) => void }) {
  const [archivo, setArchivo] = useState<File | null>(null);
  const [pin, setPin] = useState('');
  const [actualizar, setActualizar] = useState(false);
  const [pinDistinto, setPinDistinto] = useState(false);
  const [error, setError] = useState('');
  const [enviando, setEnviando] = useState(false);
  const input = useRef<HTMLInputElement>(null);
  useEffect(() => { setArchivo(null); setPin(''); setActualizar(false); setPinDistinto(false); setError(''); if (input.current) input.current.value = ''; }, [amb?.ambiente]);
  const enviar = async () => {
    if (!amb || !archivo) return;
    if (archivo.size > max) { setError(`El archivo debe pesar menos de ${Math.floor(max / 1024)} KB`); return; }
    setEnviando(true); setError('');
    try {
      const fd = new FormData();
      fd.append('ambiente', amb.ambiente); fd.append('archivo', archivo); fd.append('pin', pin); fd.append('actualizar_pin', actualizar ? '1' : '0');
      const j = await apiUpload('certificado_subir', fd);
      if (j.ok) { setPin(''); onOk(j); }
      else { setError(j.error || 'No se pudo subir'); if (j.codigo === 'pin_distinto') setPinDistinto(true); }
    } finally { setEnviando(false); }
  };
  return <Modal open={!!amb} onClose={onClose} title={<span className="flex items-center gap-2"><Upload className="h-5 w-5" />Subir certificado {amb?.ambiente}</span>}
    footer={<><Button variant="outline" onClick={onClose}>Cancelar</Button>
      <Button loading={enviando} disabled={!archivo || (pinDistinto && !actualizar)} onClick={enviar}><BadgeCheck className="h-4 w-4" />Validar y reemplazar</Button></>}>
    <form autoComplete="off" className="space-y-4" onSubmit={e => { e.preventDefault(); enviar(); }}>
      <p className="text-sm">Reemplaza <code className="break-all text-xs">{amb?.url}</code>. El archivo actual se respalda antes en la misma carpeta.</p>
      <div><Label htmlFor="cf">Archivo .p12 / .pfx (máx. {Math.floor(max / 1024)} KB)</Label>
        <Input id="cf" ref={input} type="file" accept=".p12,.pfx,application/x-pkcs12" onChange={e => { setArchivo(e.target.files?.[0] ?? null); setError(''); }} /></div>
      <div><Label htmlFor="cp">PIN del certificado</Label>
        <div className="relative"><KeyRound className="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
          <Input id="cp" type="password" className="pl-9" value={pin} onChange={e => { setPin(e.target.value); setPinDistinto(false); setActualizar(false); }} autoComplete="new-password"
            placeholder={amb?.pin_configurado ? 'Vacío = usar el PIN configurado' : 'PIN de 4 dígitos'} /></div>
        <p className="mt-1 text-xs text-muted-foreground">Se usa solo para validar el archivo; no se guarda en bitácoras.</p></div>
      {pinDistinto && <label className="flex items-start gap-2 rounded-lg bg-warning/10 p-3 text-sm"><input type="checkbox" className="mt-0.5" checked={actualizar} onChange={e => setActualizar(e.target.checked)} />
        <span>Actualizar también el PIN en COMPAÑIAS (<code>IDP_{amb?.ambiente}_PIN</code>). Sin esto la facturación no podrá firmar con el certificado nuevo.</span></label>}
      {error && <p className="text-sm text-danger">{error}</p>}
    </form>
  </Modal>;
}
