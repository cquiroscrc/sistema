import { useEffect, useState } from 'react';
import { Settings, Save } from 'lucide-react';
import { api } from '../lib/api';
import { Button, Modal, useToast } from './ui';

const RE = /^[^\s@,;]+@[^\s@,;]+\.[^\s@,;]+$/;

export default function CajaConfigModal({ open, onClose }: { open: boolean; onClose: () => void }) {
  const toast = useToast();
  const [texto, setTexto] = useState('');
  const [info, setInfo] = useState<{ actualizado?: string; usuario?: string; habilitada?: boolean }>({});
  const [ocupado, setOcupado] = useState(false);
  useEffect(() => {
    if (!open) return;
    api('caja_config').then(j => {
      if (j.ok) { setTexto((j.destinatarios || []).join('\n')); setInfo({ actualizado: j.actualizado, usuario: j.usuario, habilitada: j.tarea_habilitada }); }
      else toast('error', j.error);
    }).catch(() => {});
  }, [open]);
  const lista = texto.split(/[\s,;]+/).map(s => s.trim()).filter(Boolean);
  const malos = lista.filter(e => !RE.test(e));
  const guardar = async () => {
    setOcupado(true);
    try {
      const j = await api('caja_config_guardar', {}, { destinatarios: lista.join('\n') });
      if (j.ok) { toast('ok', 'Destinatarios guardados'); setTexto(j.destinatarios.join('\n')); setInfo(i => ({ ...i, actualizado: j.actualizado, usuario: j.usuario })); onClose(); }
      else toast('error', j.error);
    } finally { setOcupado(false); }
  };
  return <Modal open={open} onClose={onClose}
    title={<span className="flex items-center gap-2"><Settings className="h-5 w-5" />Cierre de caja · destinatarios</span>}
    footer={<><Button variant="outline" onClick={onClose}>Cancelar</Button><Button loading={ocupado} disabled={!lista.length || malos.length > 0} onClick={guardar}><Save className="h-4 w-4" />Guardar</Button></>}>
    <div className="space-y-3">
      <p className="text-muted-foreground">Correos que reciben el cierre de caja diario. Uno por línea (máximo 20).</p>
      <textarea className="h-40 w-full rounded-md border bg-background p-3 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-ring" value={texto} onChange={e => setTexto(e.target.value)} placeholder="nombre@lospaticas.com" />
      {malos.length > 0 && <p className="text-xs text-danger">Correos inválidos: {malos.join(', ')}</p>}
      <p className="text-xs text-muted-foreground">
        Guardar no envía ningún correo{info.habilitada ? '' : ' (la tarea está deshabilitada)'}.
        {info.actualizado && ` Última modificación: ${info.actualizado}${info.usuario ? ` por ${info.usuario}` : ''}.`}
      </p>
    </div>
  </Modal>;
}
