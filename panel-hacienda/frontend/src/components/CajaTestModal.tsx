import { useEffect, useState } from 'react';
import { Send } from 'lucide-react';
import { api } from '../lib/api';
import { Button, Input, Modal, useToast } from './ui';

const RE = /^[^\s@,;<>]+@[^\s@,;<>]+\.[^\s@,;<>]+$/;
const hoyCR = () => new Date().toLocaleDateString('en-CA', { timeZone: 'America/Costa_Rica' });

export default function CajaTestModal({ open, onClose }: { open: boolean; onClose: () => void }) {
  const toast = useToast();
  const [para, setPara] = useState('');
  const [modo, setModo] = useState<'real' | 'muestra'>('real');
  const [fecha, setFecha] = useState('');
  const [ocupado, setOcupado] = useState(false);
  const [res, setRes] = useState<{ ok: boolean; texto: string } | null>(null);
  useEffect(() => { if (open) setRes(null); }, [open]);
  const valido = RE.test(para.trim());
  const enviar = async () => {
    setOcupado(true); setRes(null);
    try {
      const j = await api('caja_correo_test', {}, { para: para.trim(), modo, fecha: modo === 'real' ? fecha : '' });
      setRes({ ok: !!j.ok, texto: j.ok ? j.mensaje : j.error });
      toast(j.ok ? 'ok' : 'error', j.ok ? 'Correo de prueba enviado' : j.error);
    } catch { /* sesión */ } finally { setOcupado(false); }
  };
  const opcion = (v: 'real' | 'muestra', t: string) => (
    <label className="flex cursor-pointer items-center gap-2">
      <input type="radio" name="caja-test-modo" checked={modo === v} onChange={() => setModo(v)} />{t}
    </label>
  );
  return <Modal open={open} onClose={onClose}
    title={<span className="flex items-center gap-2"><Send className="h-5 w-5" />Cierre de caja · correo de prueba</span>}
    footer={<><Button variant="outline" onClick={onClose}>Cerrar</Button>
      <Button loading={ocupado} disabled={!valido} onClick={enviar}><Send className="h-4 w-4" />Enviar prueba</Button></>}>
    <div className="space-y-3">
      <p className="text-muted-foreground">Envía un correo marcado como <b className="text-foreground">PRUEBA</b> con el formato nuevo del cierre, por el servidor de Configuración → Correo. No ejecuta la tarea ni modifica datos.</p>
      <div className="space-y-1">
        <label className="text-sm font-medium" htmlFor="caja-test-para">Correo destino</label>
        <Input id="caja-test-para" type="email" value={para} onChange={e => setPara(e.target.value)} placeholder="nombre@dominio.com" autoComplete="email" />
        {para.trim() !== '' && !valido && <p className="text-xs text-danger">Correo inválido</p>}
      </div>
      <div className="space-y-1 text-sm">
        <div className="font-medium">Datos</div>
        {opcion('real', 'Reales (solo lectura)')}
        {modo === 'real' && <div className="pl-6"><Input type="date" value={fecha} max={hoyCR()} onChange={e => setFecha(e.target.value)} /><p className="pt-1 text-xs text-muted-foreground">Vacío = hoy.</p></div>}
        {opcion('muestra', 'Muestra (cifras del 5 de noviembre de 2026)')}
      </div>
      <p className="text-xs text-muted-foreground">Asunto: «Prueba – Cierre de caja (panel-hacienda)». Máximo una prueba cada 30 s y 10 por hora. Queda en la bitácora.</p>
      {res && <div className={res.ok ? 'rounded-lg bg-success/10 p-3 text-success' : 'rounded-lg bg-danger/10 p-3 text-danger'}>{res.texto}</div>}
    </div>
  </Modal>;
}
