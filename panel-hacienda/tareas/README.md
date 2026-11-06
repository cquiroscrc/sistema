# Tareas programadas

Timers diarios (America/Costa_Rica). El respaldo de inventario se instala habilitado; las otras tres quedan deshabilitadas hasta el corte:

| id | hora | unidad | servidor |
|---|---|---|---|
| caja_correo | 17:15 | paticas-caja-correo.timer | pat-php-app01 |
| mensajeria_update | 17:28 | paticas-mensajeria-update.timer | pat-php-app01 |
| prefacturas_update | 17:48 | paticas-prefacturas-update.timer | pat-php-app01 |
| backup_inventario | 18:00 | paticas-backup-inventario.timer | pat-sql-web01 |

Control desde el panel (`#/tareas`) vía `paticas-tareas-ctl` (sudoers exacto).
El respaldo se controla desde .106 con la llave `/etc/lospaticas/ssh/paticas-backup-inventario`
y un comando forzado en .100 (`paticas-backup-ctl`: solo status/enable/disable).
No hay ejecución manual desde el panel.
