import { useEffect, useState } from 'react';
import { Mail, Save, PlugZap, KeyRound, HardDrive, FolderSync } from 'lucide-react';
import RutasDEData from './RutasDEData';
import { api } from '../lib/api';
import { Button, Card, CardBody, CardHeader, CardTitle, Input, Label, Select, Tabs, Spinner, useToast } from '../components/ui';

export type Pestana = 'correo' | 'respaldos' | 'rutas';
type Smtp = { host: string; puerto: number; cifrado: 'starttls' | 'ssl' | 'ninguno'; usuario: string; remitente: string; remitente_nombre: string; password_guardada: boolean; configurado: boolean; actualizado?: string | null; actualizado_por?: string | null };
const PUERTO: Record<Smtp['cifrado'], number> = { starttls: 587, ssl: 465, ninguno: 25 };

function Correo() {
  const toast = useToast();
  const [f, setF] = useState<Smtp | null>(null);
  const [pw, setPw] = useState('');
  const [borrarPw, setBorrarPw] = useState(false);
  const [guardando, setGuardando] = useState(false);
  const [probando, setProbando] = useState(false);
  const [prueba, setPrueba] = useState<{ ok: boolean; texto: string } | null>(null);
  useEffect(() => { api('config_correo').then(j => { if (j.ok) setF(j); else toast('error', j.error); }).catch(() => {}); }, []);
  if (!f) return <div className="flex h-40 items-center justify-center"><Spinner /></div>;
  const set = (k: keyof Smtp, v: any) => setF({ ...f, [k]: v });
  const datos = () => ({ host: f.host, puerto: String(f.puerto), cifrado: f.cifrado, usuario: f.usuario, remitente: f.remitente, remitente_nombre: f.remitente_nombre, password: borrarPw ? '' : pw });
  const guardar = async () => {
    setGuardando(true);
    try {
      const j = await api('config_correo_guardar', {}, { ...datos(), borrar_password: borrarPw ? '1' : '0' });
      if (j.ok) { setF(j); setPw(''); setBorrarPw(false); toast('ok', 'Configuración de correo guardada'); } else toast('error', j.error);
    } finally { setGuardando(false); }
  };
  const probar = async () => {
    setProbando(true); setPrueba(null);
    try { const j = await api('config_correo_probar', {}, datos()); setPrueba({ ok: !!j.ok, texto: j.ok ? j.mensaje : j.error }); }
    finally { setProbando(false); }
  };
  return <Card>
    <CardHeader><CardTitle className="flex items-center gap-2"><Mail className="h-4 w-4 text-primary" />Servidor de correo saliente (SMTP)</CardTitle>
      <span className="text-xs text-muted-foreground">{f.configurado ? `Guardado${f.actualizado ? ` ${f.actualizado}` : ''}${f.actualizado_por ? ` por ${f.actualizado_por}` : ''}` : 'Sin configurar'}</span></CardHeader>
    <CardBody>
      <form autoComplete="off" onSubmit={e => { e.preventDefault(); guardar(); }} className="grid gap-4 md:grid-cols-6">
        <div className="md:col-span-3"><Label htmlFor="h">Servidor (host)</Label><Input id="h" value={f.host} onChange={e => set('host', e.target.value)} placeholder="smtp.ejemplo.com" required /></div>
        <div className="md:col-span-1"><Label htmlFor="p">Puerto</Label><Input id="p" type="number" min={1} max={65535} value={f.puerto} onChange={e => set('puerto', Number(e.target.value))} required /></div>
        <div className="md:col-span-2"><Label htmlFor="c">Cifrado</Label>
          <Select id="c" value={f.cifrado} onChange={e => { const c = e.target.value as Smtp['cifrado']; setF({ ...f, cifrado: c, puerto: Object.values(PUERTO).includes(f.puerto) ? PUERTO[c] : f.puerto }); }}>
            <option value="starttls">STARTTLS</option><option value="ssl">SSL/TLS</option><option value="ninguno">Ninguno</option>
          </Select></div>
        <div className="md:col-span-3"><Label htmlFor="u">Usuario</Label><Input id="u" value={f.usuario} onChange={e => set('usuario', e.target.value)} autoComplete="off" placeholder="usuario@ejemplo.com" /></div>
        <div className="md:col-span-3"><Label htmlFor="w">Contraseña</Label>
          <div className="relative"><KeyRound className="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
            <Input id="w" type="password" className="pl-9" value={pw} disabled={borrarPw} onChange={e => setPw(e.target.value)} autoComplete="new-password"
              placeholder={f.password_guardada ? '•••••••• guardada (dejar vacío para no cambiar)' : 'Sin contraseña guardada'} /></div>
          {f.password_guardada && <label className="mt-1 inline-flex items-center gap-2 text-xs text-muted-foreground"><input type="checkbox" checked={borrarPw} onChange={e => setBorrarPw(e.target.checked)} />Eliminar la contraseña guardada</label>}
        </div>
        <div className="md:col-span-3"><Label htmlFor="r">Remitente (From)</Label><Input id="r" type="email" value={f.remitente} onChange={e => set('remitente', e.target.value)} placeholder="info@lospaticas.com" required /></div>
        <div className="md:col-span-3"><Label htmlFor="n">Nombre del remitente</Label><Input id="n" value={f.remitente_nombre} onChange={e => set('remitente_nombre', e.target.value)} placeholder="Los Paticas" /></div>
        <div className="flex flex-wrap items-center gap-3 md:col-span-6">
          <Button type="submit" loading={guardando}><Save className="h-4 w-4" />Guardar</Button>
          <Button type="button" variant="outline" loading={probando} onClick={probar} disabled={!f.host}><PlugZap className="h-4 w-4" />Probar conexión</Button>
          {prueba && <span className={prueba.ok ? 'text-sm text-success' : 'text-sm text-danger'}>{prueba.texto}</span>}
        </div>
      </form>
      <p className="mt-4 text-xs text-muted-foreground">La contraseña se guarda cifrada en el servidor y nunca se muestra. «Probar conexión» solo se conecta y se autentica (EHLO/AUTH); no envía correos. Estos datos aún no se usan para el cierre de caja: hoy ese correo lo envía otro servidor.</p>
    </CardBody>
  </Card>;
}

function Respaldos() {
  const toast = useToast();
  const [d, setD] = useState<{ local: number; mega: number; actualizado?: string | null } | null>(null);
  const [err, setErr] = useState('');
  const [local, setLocal] = useState('');
  const [mega, setMega] = useState('');
  const [guardando, setGuardando] = useState(false);
  const cargar = () => api('config_respaldos').then(j => {
    if (j.ok) { setD(j); setLocal(String(j.local)); setMega(String(j.mega)); setErr(''); } else setErr(j.error);
  }).catch(() => {});
  useEffect(() => { cargar(); }, []);
  const ok = (v: string) => /^\d{1,3}$/.test(v) && +v >= 1 && +v <= 365;
  const guardar = async () => {
    setGuardando(true);
    try {
      const j = await api('config_respaldos_guardar', {}, { local, mega });
      if (j.ok) { setD(j); setLocal(String(j.local)); setMega(String(j.mega)); toast('ok', j.mensaje || 'Guardado'); } else toast('error', j.error);
    } finally { setGuardando(false); }
  };
  if (err) return <Card><CardBody><p className="text-sm text-danger">{err}</p><Button className="mt-3" variant="outline" onClick={cargar}>Reintentar</Button></CardBody></Card>;
  if (!d) return <div className="flex h-40 items-center justify-center"><Spinner /></div>;
  const cambio = String(d.local) !== local || String(d.mega) !== mega;
  return <Card>
    <CardHeader><CardTitle className="flex items-center gap-2"><HardDrive className="h-4 w-4 text-primary" />Retención de respaldos de inventario</CardTitle>
      <span className="text-xs text-muted-foreground">{d.actualizado ? `Modificado ${d.actualizado}` : 'Valores por defecto'}</span></CardHeader>
    <CardBody>
      <form onSubmit={e => { e.preventDefault(); guardar(); }} className="grid gap-4 md:grid-cols-6">
        <div className="md:col-span-3"><Label htmlFor="rl">Días a mantener en local (pat-sql-web01)</Label>
          <Input id="rl" type="number" min={1} max={365} step={1} value={local} onChange={e => setLocal(e.target.value)} required />
          <p className="mt-1 text-xs text-muted-foreground">Carpeta /var/opt/mssql/backup. Actual: {d.local} días.</p></div>
        <div className="md:col-span-3"><Label htmlFor="rm">Días a mantener en MEGA</Label>
          <Input id="rm" type="number" min={1} max={365} step={1} value={mega} onChange={e => setMega(e.target.value)} required />
          <p className="mt-1 text-xs text-muted-foreground">Carpeta /Respaldos/inventario. Actual: {d.mega} días.</p></div>
        {(!ok(local) || !ok(mega)) && <p className="text-xs text-danger md:col-span-6">Use números enteros entre 1 y 365.</p>}
        <div className="md:col-span-6"><Button type="submit" loading={guardando} disabled={!cambio || !ok(local) || !ok(mega)}><Save className="h-4 w-4" />Guardar</Button></div>
      </form>
      <p className="mt-4 text-xs text-muted-foreground">Guardar no borra nada. La limpieza se aplica en la próxima corrida del respaldo (18:00) y solo afecta archivos <code>inventario_AAAAMMDD_HHMM.bak</code>; nunca <code>inventario.bak</code> ni otros archivos.</p>
    </CardBody>
  </Card>;
}

export default function Configuracion({ tab, onTab }: { tab: Pestana; onTab: (t: Pestana) => void }) {
  return <div className="space-y-4">
    <Tabs value={tab} onChange={onTab} items={[
      { id: 'correo', label: <span className="inline-flex items-center gap-1.5"><Mail className="h-3.5 w-3.5" />Correo</span> },
      { id: 'respaldos', label: <span className="inline-flex items-center gap-1.5"><HardDrive className="h-3.5 w-3.5" />Respaldos</span> },
      { id: 'rutas', label: <span className="inline-flex items-center gap-1.5"><FolderSync className="h-3.5 w-3.5" />Rutas</span> },
    ]} />
    {tab === 'correo' && <Correo />}
    {tab === 'respaldos' && <Respaldos />}
    {tab === 'rutas' && <RutasDEData />}
  </div>;
}
