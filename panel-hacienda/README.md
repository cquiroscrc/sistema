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
- Usuarios de `inventario.dbo.acc_usuarios` (la BD de `/etc/lospaticas/db.php`), con la misma lógica que el login del POS
  (`/POS/ingreso` → `ControladorUsuarios::ctrIngresoUsuario`): usuario solo `[a-zA-Z0-9]`, búsqueda `WHERE usuario = ?`
  (primera fila), usuario y contraseña comparados exactos (texto plano, sin recortar, distingue mayúsculas) y `estado = 1`.
  Para quitar el acceso a alguien: `estado = 0` o cambiar su contraseña en el POS.
- Restricción por perfil opcional (columna `acceso`, = `$_SESSION['perfil']` del POS): `PH_PERFILES_PERMITIDOS` en `lib.php`
  (vacío = todos los activos; propuesta `['total']`, el perfil administrador del POS).
- Sesión PHP `PANELHACIENDA`: cookie Secure + HttpOnly + SameSite=Strict (path `/panel-hacienda/`), `session_regenerate_id` al entrar,
  cierre por inactividad 30 min y máximo 12 h, CSRF en todo POST (login incluido).
  Las sesiones se guardan en `/var/lib/lospaticas/panel/sessions` (www-data 700), no en `/var/lib/php/sessions`, porque el cron
  `phpsessionclean` de Ubuntu borra ese directorio a los 24 min y dejaba inválido el token de un login abierto un rato.
  Si el token CSRF vence, la interfaz pide uno nuevo y reintenta una vez automáticamente.
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

## Buzón FE (comprobantes recibidos por correo)
- Cuenta: `facturaelectronica@lospaticas.com` en `mail.lospaticas.com:993` (IMAPS, certificado verificado).
  Credenciales SOLO en `/etc/lospaticas/buzon-fe.env` (root 600; `IMAP_PASS` o `IMAP_PASS_B64`). Si la clave está vacía,
  la corrida termina bien con estado **"sin credenciales"** (se ve en el panel).
- **Solo lectura**: EXAMINE + `BODY.PEEK` (no marca leídos, no mueve, no borra; el cliente bloquea STORE/COPY/MOVE/EXPUNGE/APPEND/DELETE/SELECT).
  No se envía ningún correo ni se manda nada a Hacienda.
- `paticas-buzon-fe.timer` cada 5 min → `paticas-buzon-fe.service` (www-data, `flock -n /run/lock/paticas-buzon-fe.lock`).
  "Sincronizar ahora" del panel hace `sudo systemctl start --no-block paticas-buzon-fe.service` (sudoers).
- Script: `panel-hacienda/buzon/buzon-sync.php` (`--listar` carpetas, `--schema` = migraciones, `--eml=archivo` analizar sin escribir,
  `--importar-eml=archivo` prueba, `--max=N`). Bitácora `/var/lib/lospaticas/panel/buzon-sync.log`, estado `buzon-estado.json`.
- Archivos en `/var/lib/lospaticas/fe/buzon/<aaaa>/<mm>/<clave>/` (`<clave>.xml`, `_respuesta.xml`, `.pdf`); PDFs sin clave en `sin-clave/`.
  No se publican por nginx: solo se sirven por `api.php?accion=buzon_archivo` (sesión + ruta validada con realpath).
- Deduplicación: (cuenta, carpeta, UIDVALIDITY, UID) + Message-ID; documentos por CLAVE (upsert). Los XML/ZIP se analizan
  con DOCTYPE rechazado y sin red; ZIP limitado (200 entradas, 30 MB c/u, 100 MB total).
- Se omiten por defecto los comprobantes emitidos por Los Paticas (`BUZON_IGNORAR_EMISORES=3101090358,310109035837`).
- Tablas `FE_BUZON_CORREOS`, `FE_BUZON_DOCUMENTOS`, `FE_BUZON_ARCHIVOS`: se crean con las migraciones (ver abajo).
  Si faltan, la sincronización no falla: termina con estado **"sin tablas"** y el panel lo indica.
- Pendiente / siguiente paso posible: enviar el MensajeReceptor (aceptación/rechazo) a Hacienda. **No implementado.**

## Migraciones de BD (objetos propios del panel en `inventario`)
- `panel-hacienda/db/migrations/NNNN_nombre.sql`, en orden e **idempotentes** (`IF OBJECT_ID(...) IS NULL`, `IF NOT EXISTS`; nunca DROP de datos):
  - `0001_panel_migraciones.sql` — `dbo.PANEL_MIGRACIONES` (control: nombre, SHA-256, fecha, duración, usuario, host).
  - `0002_fe_buzon_tablas.sql` — `FE_BUZON_CORREOS`, `FE_BUZON_DOCUMENTOS`, `FE_BUZON_ARCHIVOS` con PK/UNIQUE/DEFAULT e índices.
  - El panel no crea otros objetos en la BD (bloqueo de login, sesiones y bitácora del panel están en `/var/lib/lospaticas/panel`;
    la auditoría usa la tabla existente `dbo.BITACORA`). La corrección de rutas **no** es una migración: es la acción "Rutas" del panel.
- Runner: `sudo -u www-data php8.5 /var/www/lospaticas/sistema/panel-hacienda/db/migrar.php --estado | --aplicar`
  (usa `/etc/lospaticas/db.php`; una transacción por archivo; verifica los objetos de la línea `-- objetos:`; bitácora en
  `/var/lib/lospaticas/panel/migraciones.log`; `--estado` sale con código 2 si hay pendientes).
- El panel revisa las pendientes (caché de 2 min) y muestra **"Hay N migraciones pendientes"** con el botón **Aplicar migraciones**
  (escribir `MIGRAR`, CSRF, queda en la bitácora).
- Nueva migración: agregar `0003_...sql` idempotente con su línea `-- objetos:`; no editar las ya aplicadas (el panel las marca como modificadas).

## Después de la restauración final de `inventario` (en este orden)
1. Migraciones: `sudo -u www-data php8.5 /var/www/lospaticas/sistema/panel-hacienda/db/migrar.php --aplicar` (o el botón del panel).
2. Rutas: pestaña "Rutas y DEData" → Aplicar rutas.
3. Buzón FE: `sudo rm -f /var/lib/lospaticas/panel/buzon-estado.json && sudo systemctl start paticas-buzon-fe.service`
   (vuelve a escanear todo el INBOX; los archivos ya guardados se reutilizan por SHA-256; ~13 corridas de 5 min, unos 65 min, o una manual con `--max=5000`).
