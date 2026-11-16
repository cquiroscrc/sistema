import { useEffect } from 'react';
import { api } from './api';

/** Mantiene viva la sesión PHP mientras la página esté montada (Resumen, Comprobantes, Logs), aunque la pestaña quede
 *  en segundo plano: un ping autenticado cada 4 min renueva la marca de inactividad (30 min). No evita el máximo de 12 h
 *  ni sirve sin sesión válida (api.php responde 401 y la app vuelve al login). */
export function useMantenerSesion(minutos = 4) {
  useEffect(() => {
    const ping = () => { void api('ping').catch(() => { /* 401 lo maneja api() */ }); };
    const t = setInterval(ping, minutos * 60000);
    return () => clearInterval(t);
  }, [minutos]);
}
