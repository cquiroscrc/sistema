# Panel Hacienda y corte del envío de comprobantes (api44)

## Componentes en pat-php-app01 (172.16.12.106)
- Código: `/var/www/lospaticas/sistema/fe/API/api44` (git). El envío es CLI: `php8.5 fe/API/api44/contrib/admin/admin.php`
  (en Windows lo ejecutaba una tarea programada cada ~1 minuto: hay una sesión de BITACORA cada ~63 s).
- Datos (fuera de git, solo www-data): `api44/files/` (certificados .p12, plantillas), `api44/logs/sender.log`, `fe/API/api/errors/_errors.log`.
- Certificado de producción: `api44/files/certificado/310109035837.p12` (abre con el PIN de producción guardado en la BD; vence 2027-10-20).
- XML/PDF generados: `/var/lib/lospaticas/fe/DEData`.
- systemd: `paticas-hacienda-sender.service` (oneshot, usuario www-data, flock) + `paticas-hacienda-sender.timer` (cada 60 s).
  Creados **deshabilitados y detenidos**. Ya no depende de ningún archivo de bloqueo: el estado lo define únicamente
  el usuario con **Iniciar / Detener** en el panel (o `systemctl enable|disable --now` como root).
- Variables de entorno opcionales: `/etc/lospaticas/api44.env` (root 600, solo nombres).
- SQL de rutas Linux para el corte: `/etc/lospaticas/corte-hacienda.sql` (NO ejecutado).
- Panel: `https://sistema.lospaticas.com/panel-hacienda/` (login con los usuarios del sistema, ver abajo).
  www-data solo puede `systemctl enable --now|disable --now paticas-hacienda-sender.timer` y `paticas-dedata-sync`
  (`/etc/sudoers.d/paticas-hacienda-panel`, argumentos exactos).

## Acceso (login)
- Usuarios de `inventario.dbo.tbl_SEC_Usuarios` (la BD de `/etc/lospaticas/db.php`), los mismos del sistema PATICAS de escritorio.
  La tabla guarda `Username`/`Password` en texto plano (char 30/15) y no tiene columna de "activo": se compara
  `RTRIM(Username)` (sin distinguir mayúsculas, collation de la BD) y la contraseña exacta con `hash_equals`.
  Para quitar el acceso a alguien, borrarlo o cambiarle la contraseña en el sistema.
- Restricción por grupo opcional: `PH_GRUPOS_PERMITIDOS` en `lib.php` (vacío = todos; propuesta `[1]` = 01-Administradores;
  hoy todos los usuarios son grupo 1).
- Sesión PHP `PANELHACIENDA`: cookie Secure + HttpOnly + SameSite=Strict (path `/panel-hacienda/`), `session_regenerate_id` al entrar,
  cierre por inactividad 30 min y máximo 12 h, CSRF en todo POST (login incluido).
- Bloqueo: 5 fallos por usuario o por IP (IP real vía Cloudflare) → 15 min (`/var/lib/lospaticas/panel/login-fallos.json`).
- Auditoría: login correcto/fallido/bloqueado, logout y cada acción quedan en `/var/lib/lospaticas/panel/acciones.log`
  y en `dbo.BITACORA` (`SESSION_ID='panel-hacienda'`) con usuario e IP. Sin sesión, `api.php` responde 401 (salvo `sesion`/`login`).

## Interfaz (React)
- Código fuente: `panel-hacienda/frontend/` (Vite + React 18 + TypeScript + Tailwind, componentes estilo shadcn, lucide, recharts).
  nginx no sirve esa carpeta.
- **El build se versiona**: `panel-hacienda/index.html` y `panel-hacienda/assets/` son la salida de `npm run build`
  (así el servidor no necesita Node). Para cambiar la interfaz: `cd panel-hacienda/frontend && npm ci && npm run build`
  y luego copiar `dist/*` a `panel-hacienda/` (borrando los `assets/index-*` anteriores) y hacer commit de ambos.
- Backend JSON: `api.php` + `lib.php`.

## Reintentar
Solo documentos con `ESTADO='error'`, `ENVIADO<>'S'`, `ACEPTADO<>'S'`: se marca `ESTADO='reintentar'` y el siguiente lote de
`ProcesarPendientesEnvio` lo vuelve a tomar (la consulta excluye solo 'rechazado' y 'error'). Es un cambio real en la BD y solo se
permite con el servicio **iniciado** (con el servicio detenido el botón está deshabilitado y la API lo rechaza).
Los rechazados por Hacienda no se reintentan (requieren documento nuevo / nota de crédito).

## Corte (un solo servidor enviando)
1. En 172.16.12.3: deshabilitar la tarea programada que ejecuta `admin.php` y esperar que termine la corrida en curso
   (BITACORA sin filas nuevas ≥ 2 min; `SELECT EN_PROCESO FROM PROCESO` = 'NO').
2. Respaldo final de `inventario` en .3 → restaurar en 172.16.12.100 (o apuntar `/etc/lospaticas/db.php` a la BD definitiva).
3. Ejecutar `/etc/lospaticas/corte-hacienda.sql` en esa BD (rutas del certificado, XML/PDF y plantillas a Linux).
4. Copiar históricos si se requieren: `C:\Backups\DEData` → `/var/lib/lospaticas/fe/DEData` (www-data).
5. Panel → Servicio → **Iniciar** (escribir INICIAR): habilita y arranca el timer (persiste tras reinicios).
6. Vigilar el panel (Registro en vivo / Comprobantes). Volver atrás: **Detener** en el panel
   (`systemctl disable --now paticas-hacienda-sender.timer`) y rehabilitar la tarea en Windows.
Nunca deben estar activos a la vez la tarea de Windows y el timer de .106.

## DEData (XML/PDF)
- Destino en .106: `/var/lib/lospaticas/fe/DEData` (www-data, 700/600) = `XML_PATH`/`PDF_PATH` de `corte-hacienda.sql`.
- Sincronización incremental (sin --delete): `/usr/local/sbin/paticas-dedata-sync` (flock, log `/var/log/paticas-dedata-sync.log`,
  config `/etc/lospaticas/dedata-sync.conf`). Con la cuenta cquiros: `.\rw.ps1 172.16.12.106 gk\dedata.sh` donde el script hace
  `WP="$WP" /usr/local/sbin/paticas-dedata-sync`. El botón del panel usa `/etc/lospaticas/smb-dedata.cred` (root 600) si existe.
- Los PDF también están en la BD (columna `DOC_ELECTRONICO_PROCESADO.PDF`); el panel usa el archivo de DEData si existe y si no la BD.

## Rutas (pestaña "Rutas y DEData")
Vista previa actual/nuevo, Aplicar (escribir APLICAR) en transacción con respaldo JSON en `/var/lib/lospaticas/panel/`,
Revertir (escribir REVERTIR), historial en `/var/lib/lospaticas/panel/acciones.log` y en BITACORA. No toca el servicio ni el archivo de corte.
Tras la restauración final de la BD en el corte hay que aplicarlo de nuevo.
