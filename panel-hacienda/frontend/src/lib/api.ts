// Cliente del backend PHP (panel-hacienda/api.php). La sesion va en cookie HttpOnly; el token CSRF se envia en cada POST.
let csrf = '';
export const setCsrf = (t: string) => { csrf = t; };
export class NoAutorizado extends Error {}

export const apiUrl = (a: string, params: Record<string, string | number | undefined> | null | undefined = {}) => {
  const q = new URLSearchParams({ a });
  Object.entries(params ?? {}).forEach(([k, v]) => { if (v !== undefined && v !== '') q.set(k, String(v)); });
  return 'api.php?' + q.toString();
};

let onUnauthorized: () => void = () => {};
export const setOnUnauthorized = (f: () => void) => { onUnauthorized = f; };

export async function api<T = any>(a: string, params: Record<string, string | number | undefined> | null = {}, post?: Record<string, string>, reintento = false): Promise<T> {
  const opt: RequestInit = { credentials: 'same-origin', headers: { 'X-Requested-With': 'panel' } };
  if (post) {
    opt.method = 'POST';
    opt.body = new URLSearchParams({ ...post, csrf });
  }
  const r = await fetch(apiUrl(a, params), opt);
  let j: any;
  try { j = await r.json(); } catch { j = { ok: false, error: `Respuesta inválida del servidor (${r.status})` }; }
  if (r.status === 401 && a !== 'login' && a !== 'sesion') { onUnauthorized(); throw new NoAutorizado(j.error || 'Sesión expirada'); }
  if (j && j.csrf) csrf = j.csrf;
  // Token CSRF vencido (sesion expirada/renovada): el servidor no ejecuto nada; se pide un token nuevo y se reintenta una vez.
  if (r.status === 403 && j?.codigo === 'csrf' && post && !reintento) {
    await api('sesion');
    return api<T>(a, params, post, true);
  }
  return j as T;
}

export type Estado = 'aceptado' | 'rechazado' | 'error' | 'pendiente' | 'enviando' | 'reintentar' | string;

export interface Status {
  ok: boolean; ahora: string; usuario: string; servicio_activo: boolean;
  timer: Record<string, string>; servicio: Record<string, string>;
  bd: { host: string; database: string; restaurada: string | null };
  ultima_bitacora: string; ultimo_documento: string;
  conteos: { error: number; reintentar: number; pendiente_respuesta: number; rechazado_hoy: number; aceptado_hoy: number; enviados_hoy: number; cola: number };
}
export interface LogRow { f: string; nivel: string; doc: string; texto: string; sesion?: string }
export interface DocRow {
  tipo: string; numero: string; clave: string; consecutivo: string; fecha: string; fecha_emision: string;
  receptor: string; email: string; total: number | null; estado: Estado; estado_bd: string; correo: string; error: string; pdf: boolean;
}
