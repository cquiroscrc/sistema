import { useEffect, useId, useState } from 'react';
import { CalendarClock, Play, Square, Zap, AlertTriangle, HardDrive, Settings, Send, Info, Receipt, MessageSquare, Eraser, Briefcase, Database, FolderGit2, type LucideIcon } from 'lucide-react';
import { api } from '../lib/api';
import { cn } from '../lib/utils';
import { Button, Card, CardBody, Input, Modal, Select, useToast } from '../components/ui';
import RespaldosModal from '../components/RespaldosModal';
import CajaConfigModal from '../components/CajaConfigModal';
import CajaTestModal from '../components/CajaTestModal';

type Accion = 'enable' | 'disable' | 'start';
export type Horario = {
  tipo: 'diaria' | 'intervalo' | 'semanal' | 'otro';
  hora?: string;
  cada?: number;
  unidad?: 'minutos' | 'horas';
  dia?: string;
  oncalendar?: string;
  texto?: string;
};
export type Tarea = {
  id: string; nombre: string; descripcion: string; hora: string; servidor: string;
  enabled: boolean; corriendo: boolean; timer: Record<string, string>; servicio: Record<string, string>;
  log: string[]; log_error?: string;
  horario?: Horario; resultado?: string; ultima?: string; proxima?: string;
};

const PALABRA: Record<Accion, string> = { enable: 'ACTIVAR', disable: 'DESACTIVAR', start: 'EJECUTAR' };
export const CLAVE_TAB = 'panel.tareas.tab';
const ICONO: Record<string, LucideIcon> = { caja_correo: Receipt, mensajeria_update: MessageSquare, prefacturas_update: Eraser, actividades_comerciales: Briefcase, backup_inventario: Database, codigo_respaldo: FolderGit2 };
const iconoDe = (id: string): LucideIcon => ICONO[id] ?? CalendarClock;
const DIAS: { id: string; label: string }[] = [
  { id: 'Mon', label: 'lunes' }, { id: 'Tue', label: 'martes' }, { id: 'Wed', label: 'miércoles' },
  { id: 'Thu', label: 'jueves' }, { id: 'Fri', label: 'viernes' }, { id: 'Sat', label: 'sábado' }, { id: 'Sun', label: 'domingo' },
];
const sel = 'w-auto min-w-0';

function filtrarLog(lineas: string[], fecha: string, consulta: string): string[] {
  let bloque = '';
  const conFecha = lineas.map(linea => {
    const marca = linea.match(/^(\d{4}-\d{2}-\d{2})\b/);
    if (marca) bloque = marca[1];
    return { linea, fecha: marca ? marca[1] : bloque };
  });
  const q = consulta.trim().toLowerCase();
  return conFecha
    .filter(x => (!fecha || x.fecha === fecha) && (!q || x.linea.toLowerCase().includes(q)))
    .map(x => x.linea);
}

function resaltar(linea: string, consulta: string): React.ReactNode {
  const n = consulta.trim();
  if (!n) return linea;
  const bajo = linea.toLowerCase();
  const aguja = n.toLowerCase();
  const partes: React.ReactNode[] = [];
  let desde = 0;
  let nro = 0;
  while (desde <= linea.length) {
    const en = bajo.indexOf(aguja, desde);
    if (en < 0) { partes.push(linea.slice(desde)); break; }
    partes.push(linea.slice(desde, en));
    partes.push(<mark key={nro} className="rounded bg-[#d3c43b] px-0.5 text-black">{linea.slice(en, en + n.length)}</mark>);
    nro += 1;
    desde = en + n.length;
  }
  return partes;
}

/** Registro completo de la tarea dentro de la pestaña (se refresca cada 5 s), con filtro por fecha y búsqueda. */
function Registro({ tarea }: { tarea: Tarea }) {
  const [lineas, setLineas] = useState<string[]>([]);
  const [fecha, setFecha] = useState('');
  const [consulta, setConsulta] = useState('');
  const [truncado, setTruncado] = useState(false);
  const [error, setError] = useState('');
  const [cargado, setCargado] = useState(false);
  useEffect(() => {
    let vivo = true;
    setFecha(''); setConsulta(''); setLineas([]); setCargado(false); setError('');
    const leer = () => {
      api('tarea_log', { id: tarea.id }).then(j => {
        if (!vivo) return;
        if (j.ok) { setLineas(j.lineas || []); setTruncado(!!j.truncado); setError(''); }
        else setError(j.error || 'No se pudo leer el registro');
        setCargado(true);
      }).catch(() => { if (vivo) { setError('No se pudo leer el registro'); setCargado(true); } });
    };
    leer();
    const id = window.setInterval(leer, 5000);
    return () => { vivo = false; window.clearInterval(id); };
  }, [tarea.id]);
  const visibles = filtrarLog(lineas, fecha, consulta);
  return <div className="flex min-h-0 flex-1 flex-col">
    <div className="flex flex-wrap items-end gap-3">
      <label className="text-xs font-medium text-muted-foreground">Fecha
        <Input type="date" value={fecha} onChange={e => setFecha(e.target.value)} className="mt-1 w-44" />
      </label>
      <label className="min-w-[14rem] flex-1 text-xs font-medium text-muted-foreground">Buscar
        <Input value={consulta} onChange={e => setConsulta(e.target.value)} placeholder="Texto dentro del registro" className="mt-1" />
      </label>
      <Button variant="outline" onClick={() => { setFecha(''); setConsulta(''); }}>Limpiar</Button>
    </div>
    <p className="mt-2 text-xs text-muted-foreground">
      {visibles.length} de {lineas.length} líneas
      {truncado ? ' · se muestran las más recientes' : ''}
      {tarea.corriendo ? ' · la tarea está en curso' : ''} · se actualiza cada 5 s
    </p>
    {(error || tarea.log_error) && <p className="mt-1 text-sm text-danger">{error || tarea.log_error}</p>}
    <pre className="mt-2 min-h-[16rem] flex-1 overflow-auto rounded-lg bg-[hsl(222_40%_6%)] p-4 font-mono text-[13px] leading-6 text-slate-100 scrollbar-thin">
      {!cargado ? 'Cargando registro…' : visibles.length ? visibles.map((l, i) => <div key={i}>{resaltar(l, consulta)}</div>) : lineas.length ? 'No hay líneas para ese filtro.' : 'Sin registro todavía.'}
    </pre>
  </div>;
}

function avisoCorrida(t: Tarea): string {
  const hora = t.horario?.texto || t.hora;
  switch (t.id) {
    case 'caja_correo':
      return `Se envía ahora el correo de cierre de caja. El horario (${hora}) no cambia. Si la tarea de Windows también lo envía hoy, el destinatario lo recibe dos veces.`;
    case 'mensajeria_update':
      return `Se restablecen ahora las existencias de mensajería. El horario (${hora}) no cambia.`;
    case 'prefacturas_update':
      return `Se borran ahora las prefacturas temporales. El horario (${hora}) no cambia.`;
    case 'actividades_comerciales':
      return `Se ejecuta ahora una corrida del actualizador de actividades comerciales. Si ya hay una en curso se omite. El horario (${hora}) no cambia.`;
    case 'backup_inventario':
      return `Se inicia ahora el respaldo de la base inventario en pat-sql-web01. Puede tardar y, al terminar, sube el archivo a MEGA. El horario (${hora}) no cambia.`;
    case 'codigo_respaldo':
      return `Se revisa ahora el código del sistema. Si cambió desde el último envío, se publica en GitHub y se sube un paquete a MEGA. Si no cambió, no se envía nada. El horario (${hora}) no cambia.`;
    default:
      return `Se ejecuta ahora. El horario programado (${hora}) no cambia.`;
  }
}

/** Ícono de información con tooltip: hover, foco con teclado y toque en móvil; Esc lo cierra. */
function InfoTip({ texto }: { texto: React.ReactNode }) {
  const [abierto, setAbierto] = useState(false);
  const id = useId();
  return <span className="relative inline-flex" onMouseEnter={() => setAbierto(true)} onMouseLeave={() => setAbierto(false)}>
    <button type="button" aria-label="Más información" aria-describedby={abierto ? id : undefined} aria-expanded={abierto}
      className="rounded-full p-0.5 text-muted-foreground hover:text-foreground focus:outline-none focus-visible:ring-2 focus-visible:ring-ring"
      onClick={() => setAbierto(a => !a)} onFocus={() => setAbierto(true)} onBlur={() => setAbierto(false)}
      onKeyDown={e => { if (e.key === 'Escape') setAbierto(false); }}>
      <Info className="h-4 w-4" />
    </button>
    {abierto && <span role="tooltip" id={id} className="absolute left-0 top-full z-20 mt-2 w-80 max-w-[80vw] rounded-md border bg-card p-3 text-xs font-normal leading-relaxed text-card-foreground shadow-lg">{texto}</span>}
  </span>;
}

const estadoTexto = (t: Tarea) => (t.corriendo ? 'EN CURSO' : t.enabled ? 'HABILITADA' : 'DESHABILITADA');
const estadoClase = (t: Tarea) => (t.corriendo ? 'bg-info/10 text-info ring-info/30' : t.enabled ? 'bg-success/10 text-success ring-success/30' : 'bg-muted text-muted-foreground ring-border');

function Insignia({ t }: { t: Tarea }) {
  return <span className={cn('inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset', estadoClase(t))}>
    <span className={cn('h-2 w-2 rounded-full', t.corriendo || t.enabled ? 'bg-current animate-pulse' : 'bg-muted-foreground')} />{estadoTexto(t)}</span>;
}

type FormHorario = { tipo: 'diaria' | 'intervalo' | 'semanal'; hora: string; cada: string; unidad: 'minutos' | 'horas'; dia: string };

function deHorario(h?: Horario): FormHorario {
  const tipo = h?.tipo === 'intervalo' || h?.tipo === 'semanal' ? h.tipo : 'diaria';
  return {
    tipo,
    hora: h?.hora || '17:00',
    cada: String(h?.cada ?? (tipo === 'intervalo' ? 5 : 1)),
    unidad: h?.unidad === 'horas' ? 'horas' : 'minutos',
    dia: h?.dia && DIAS.some(d => d.id === h.dia) ? h.dia : 'Mon',
  };
}

function igualHorario(a: FormHorario, b: FormHorario): boolean {
  if (a.tipo !== b.tipo) return false;
  if (a.tipo === 'diaria') return a.hora === b.hora;
  if (a.tipo === 'intervalo') return a.cada === b.cada && a.unidad === b.unidad;
  return a.dia === b.dia && a.hora === b.hora;
}

function HorarioEditor({ tarea, onGuardado }: { tarea: Tarea; onGuardado: () => void }) {
  const toast = useToast();
  const original = deHorario(tarea.horario);
  const [form, setForm] = useState<FormHorario>(original);
  const [proxima, setProxima] = useState(tarea.proxima || '');
  const [texto, setTexto] = useState(tarea.horario?.texto || tarea.hora || '');
  const [error, setError] = useState('');
  const [cargando, setCargando] = useState(false);
  const [confirmar, setConfirmar] = useState(false);
  const sucio = !igualHorario(form, original);

  useEffect(() => { setForm(deHorario(tarea.horario)); setProxima(tarea.proxima || ''); setTexto(tarea.horario?.texto || tarea.hora || ''); setError(''); setConfirmar(false); }, [tarea.id, tarea.horario?.oncalendar, tarea.proxima]);

  useEffect(() => {
    let vivo = true;
    const id = window.setTimeout(() => {
      api('tarea_horario_preview', { tipo: form.tipo, hora: form.hora, cada: form.cada, unidad: form.unidad, dia: form.dia }).then(j => {
        if (!vivo) return;
        if (j.ok) { setProxima(j.proxima || ''); setTexto(j.texto || ''); setError(''); }
        else { setError(j.error || 'Horario inválido'); setProxima(''); }
      }).catch(() => { if (vivo) setError('No se pudo calcular la próxima ejecución'); });
    }, 250);
    return () => { vivo = false; window.clearTimeout(id); };
  }, [form.tipo, form.hora, form.cada, form.unidad, form.dia]);

  const guardar = async () => {
    setCargando(true);
    try {
      const j = await api('tarea_horario', {}, { id: tarea.id, tipo: form.tipo, hora: form.hora, cada: form.cada, unidad: form.unidad, dia: form.dia });
      if (j.ok) {
        toast('ok', `Horario guardado · próxima: ${j.proxima || '—'}`);
        setConfirmar(false);
        onGuardado();
      } else toast('error', j.error || j.salida || 'No se pudo guardar');
    } finally { setCargando(false); }
  };

  return <div className="rounded-lg border p-3">
    <div className="flex flex-nowrap items-end gap-2 overflow-x-auto">
        <label className="shrink-0 text-xs font-medium text-muted-foreground">Periodicidad
          <Select className={cn('mt-1', sel)} value={form.tipo} onChange={e => setForm(f => ({ ...f, tipo: e.target.value as FormHorario['tipo'] }))}>
            <option value="diaria">Diaria</option>
            <option value="intervalo">Cada intervalo</option>
            <option value="semanal">Semanal</option>
          </Select>
        </label>
        {form.tipo === 'semanal' && <label className="shrink-0 text-xs font-medium text-muted-foreground">Día
          <Select className={cn('mt-1', sel)} value={form.dia} onChange={e => setForm(f => ({ ...f, dia: e.target.value }))}>
            {DIAS.map(d => <option key={d.id} value={d.id}>{d.label}</option>)}
          </Select>
        </label>}
        {form.tipo === 'intervalo' ? <>
          <label className="shrink-0 text-xs font-medium text-muted-foreground">Cada
            <Input type="number" min={1} max={form.unidad === 'horas' ? 23 : 59} value={form.cada} onChange={e => setForm(f => ({ ...f, cada: e.target.value }))} className="mt-1 w-16" />
          </label>
          <label className="shrink-0 text-xs font-medium text-muted-foreground">Unidad
            <Select className={cn('mt-1', sel)} value={form.unidad} onChange={e => setForm(f => ({ ...f, unidad: e.target.value as 'minutos' | 'horas' }))}>
              <option value="minutos">minutos</option>
              <option value="horas">horas</option>
            </Select>
          </label>
        </> : <label className="shrink-0 text-xs font-medium text-muted-foreground">Hora
          <Input type="time" step={60} value={form.hora} onChange={e => setForm(f => ({ ...f, hora: e.target.value }))} className="mt-1 w-[7.5rem]" />
        </label>}
        <Button className="shrink-0" disabled={!sucio || !!error || cargando} onClick={() => setConfirmar(true)}>Guardar horario</Button>
        {sucio && <Button variant="ghost" className="shrink-0" onClick={() => setForm(original)}>Deshacer</Button>}
      </div>
    <p className="mt-2 text-xs text-muted-foreground">
      {error ? <span className="text-danger">{error}</span> : <>{texto || '—'}{proxima ? ` · próxima ejecución: ${proxima}` : ''}{!tarea.enabled ? ' · el timer está deshabilitado; el cambio queda guardado y no lo activa' : ''}</>}
    </p>
    <Modal open={confirmar} onClose={() => setConfirmar(false)} title={`Cambiar horario de ${tarea.nombre}`}
      footer={<><Button variant="outline" onClick={() => setConfirmar(false)}>Cancelar</Button>
        <Button loading={cargando} disabled={!!error} onClick={guardar}>Guardar</Button></>}>
      <div className="space-y-2 text-sm">
        <p>Quedará {texto || 'con el horario indicado'}{proxima ? ` (próxima ejecución: ${proxima})` : ''}.</p>
        <p className="text-muted-foreground">No se ejecuta la tarea ahora. El timer sigue {tarea.enabled ? 'habilitado' : 'deshabilitado'}.</p>
      </div>
    </Modal>
  </div>;
}

export default function Tareas({ onCambio }: { onCambio?: () => void }) {
  const toast = useToast();
  const [items, setItems] = useState<Tarea[] | null>(null);
  const [tab, setTab] = useState<string>(() => { try { return localStorage.getItem(CLAVE_TAB) || ''; } catch { return ''; } });
  const [modal, setModal] = useState<{ id: string; acc: Accion; nombre: string } | null>(null);
  const [palabra, setPalabra] = useState('');
  const [cargando, setCargando] = useState(false);
  const [archivos, setArchivos] = useState(false);
  const [configCaja, setConfigCaja] = useState(false);
  const [testCaja, setTestCaja] = useState(false);
  const cargar = () => { api('tareas').then(j => { if (j.ok) setItems(j.tareas); else toast('error', j.error); }).catch(() => {}); };
  useEffect(() => { cargar(); }, []);
  useEffect(() => {
    const id = window.setInterval(cargar, items?.some(t => t.corriendo) ? 4000 : 30000);
    return () => window.clearInterval(id);
  }, [items]);
  const elegir = (id: string) => { setTab(id); try { localStorage.setItem(CLAVE_TAB, id); } catch { /* sin almacenamiento */ } };
  const ejecutar = async () => {
    if (!modal) return;
    setCargando(true);
    try {
      const j = await api('tarea', {}, { id: modal.id, accion: modal.acc, confirmar: palabra });
      const okMsg = modal.acc === 'enable' ? 'Tarea habilitada' : modal.acc === 'disable' ? 'Tarea deshabilitada' : 'Corrida iniciada';
      toast(j.ok ? 'ok' : 'error', j.ok ? okMsg : (j.error || j.salida));
      if (j.ok) { setModal(null); setPalabra(''); cargar(); onCambio?.(); }
    } finally { setCargando(false); }
  };
  const dato = (k: string, v: React.ReactNode) => <div className="min-w-0"><div className="text-xs text-muted-foreground">{k}</div><div className="mt-0.5 text-sm font-medium">{v || '—'}</div></div>;
  const esperado = modal ? PALABRA[modal.acc] : '';
  const t = items ? (items.find(x => x.id === tab) ?? items[0]) : null;
  return <div className="flex h-[calc(100dvh-10.75rem)] min-h-[36rem] flex-col gap-4">
    <div role="tablist" aria-label="Tareas programadas" className="flex flex-wrap gap-1 rounded-lg bg-muted p-1">
      {(items ?? []).map(x => <button key={x.id} role="tab" type="button" aria-selected={t?.id === x.id} onClick={() => elegir(x.id)}
        className={cn('inline-flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-ring',
          t?.id === x.id ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground')}>
        {(() => { const I = iconoDe(x.id); return <I className="h-4 w-4 shrink-0" aria-hidden="true" />; })()}{x.nombre}</button>)}
      {!items && <span className="px-3 py-2 text-sm text-muted-foreground">Cargando tareas…</span>}
    </div>
    {t && <Card key={t.id} className="flex min-h-0 flex-1 flex-col">
      <CardBody className="flex min-h-0 flex-1 flex-col gap-4">
        <div className="flex flex-wrap items-center justify-between gap-3">
          <h2 className="flex items-center gap-2 text-base font-semibold">{(() => { const I = iconoDe(t.id); return <I className="h-4 w-4 text-primary" aria-hidden="true" />; })()}{t.nombre}
            <InfoTip texto={<><div>{t.descripcion}</div><div className="mt-1 text-muted-foreground">{t.hora} · {t.servidor}</div></>} /></h2>
          <Insignia t={t} />
        </div>
        <div className="grid gap-x-6 gap-y-3 rounded-lg border p-3 sm:grid-cols-3 xl:grid-cols-6">
          {dato('Timer', `${t.timer.ActiveState || '?'} · ${t.timer.UnitFileState || '?'}`)}
          {dato('Próxima corrida', t.timer.NextElapseUSecRealtime)}
          {dato('Última activación', t.timer.LastTriggerUSec)}
          {dato('Última corrida', `${t.servicio.ActiveState || '?'}/${t.servicio.SubState || '?'}`)}
          {dato('Resultado', `${t.servicio.Result || '—'} (código ${t.servicio.ExecMainStatus ?? '—'})`)}
          {dato('Inicio / fin', <>{t.servicio.ExecMainStartTimestamp || '—'}{t.servicio.ExecMainExitTimestamp ? <><br />{t.servicio.ExecMainExitTimestamp}</> : null}</>)}
        </div>
        <HorarioEditor tarea={t} onGuardado={() => { cargar(); onCambio?.(); }} />
        <div className="flex flex-wrap gap-3">
          <Button variant="outline" disabled={t.corriendo} onClick={() => { setPalabra(''); setModal({ id: t.id, acc: 'start', nombre: t.nombre }); }}><Zap className="h-4 w-4" />{t.corriendo ? 'En curso' : 'Ejecutar ahora'}</Button>
          <Button variant="success" disabled={t.enabled} onClick={() => { setPalabra(''); setModal({ id: t.id, acc: 'enable', nombre: t.nombre }); }}><Play className="h-4 w-4" />Activar</Button>
          <Button variant="danger" disabled={!t.enabled} onClick={() => { setPalabra(''); setModal({ id: t.id, acc: 'disable', nombre: t.nombre }); }}><Square className="h-4 w-4" />Desactivar</Button>
          {t.id === 'backup_inventario' && <Button variant="outline" onClick={() => setArchivos(true)}><HardDrive className="h-4 w-4" />Archivos de respaldo</Button>}
          {t.id === 'caja_correo' && <Button variant="outline" onClick={() => setConfigCaja(true)}><Settings className="h-4 w-4" />Configuración</Button>}
          {t.id === 'caja_correo' && <Button variant="outline" onClick={() => setTestCaja(true)}><Send className="h-4 w-4" />Test</Button>}
        </div>
        <Registro tarea={t} />
      </CardBody>
    </Card>}
    <Modal open={!!modal} onClose={() => setModal(null)} tone={modal?.acc === 'enable' ? 'danger' : undefined}
      title={modal?.acc === 'start'
        ? <span className="flex items-center gap-2"><Zap className="h-5 w-5" />Ejecutar {modal.nombre}</span>
        : modal?.acc === 'enable'
        ? <span className="flex items-center gap-2 text-danger"><AlertTriangle className="h-5 w-5" />Activar {modal?.nombre}</span>
        : `Desactivar ${modal?.nombre || ''}`}
      footer={<><Button variant="outline" onClick={() => setModal(null)}>Cancelar</Button>
        <Button variant={modal?.acc === 'start' ? 'primary' : 'danger'} loading={cargando} disabled={palabra !== esperado} onClick={ejecutar}>
          {modal?.acc === 'start' ? <Zap className="h-4 w-4" /> : modal?.acc === 'enable' ? <Play className="h-4 w-4" /> : <Square className="h-4 w-4" />}
          {modal?.acc === 'start' ? 'Ejecutar' : modal?.acc === 'enable' ? 'Activar' : 'Desactivar'}
        </Button></>}>
      {modal?.acc === 'start'
        ? <div className="space-y-3">
            <p>{t ? avisoCorrida(t) : ''}</p>
            <div><label className="mb-1 block text-xs font-medium">Escriba <b>EJECUTAR</b> para confirmar</label><Input value={palabra} onChange={e => setPalabra(e.target.value)} autoFocus placeholder="EJECUTAR" /></div>
          </div>
        : modal?.acc === 'enable'
        ? <div className="space-y-3">
            <div className="rounded-lg bg-danger/10 p-3 text-danger">Si la misma tarea sigue activa en otro servidor se duplicará el trabajo (correo de cierre de caja, escrituras en la BD o consultas a Hacienda).</div>
            <div><label className="mb-1 block text-xs font-medium">Escriba <b>ACTIVAR</b> para confirmar</label><Input value={palabra} onChange={e => setPalabra(e.target.value)} autoFocus placeholder="ACTIVAR" /></div>
          </div>
        : <div className="space-y-3">
            <p>Se detiene y deshabilita el timer. No volverá a ejecutarse hasta que lo active de nuevo.</p>
            <div><label className="mb-1 block text-xs font-medium">Escriba <b>DESACTIVAR</b> para confirmar</label><Input value={palabra} onChange={e => setPalabra(e.target.value)} autoFocus placeholder="DESACTIVAR" /></div>
          </div>}
    </Modal>
    <RespaldosModal open={archivos} onClose={() => setArchivos(false)} />
    <CajaConfigModal open={configCaja} onClose={() => setConfigCaja(false)} />
    <CajaTestModal open={testCaja} onClose={() => setTestCaja(false)} />
  </div>;
}
