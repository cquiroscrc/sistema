# Tareas programadas

Timers systemd (OnCalendar en America/Costa_Rica). El horario vigente se guarda en un drop-in
`/etc/systemd/system/<unidad>.timer.d/horario.conf` y se cambia desde el panel
(`#/tareas`), sin alterar si el timer está habilitado o no.

| id | horario por defecto | unidad | servidor |
|---|---|---|---|
| caja_correo | diaria 17:15 | paticas-caja-correo.timer | pat-php-app01 |
| mensajeria_update | diaria 17:28 | paticas-mensajeria-update.timer | pat-php-app01 |
| prefacturas_update | diaria 17:48 | paticas-prefacturas-update.timer | pat-php-app01 |
| actividades_comerciales | cada 5 min | paticas-actividades-comerciales.timer | pat-php-app01 |
| backup_inventario | diaria 18:00 | paticas-backup-inventario.timer | pat-sql-web01 |
| codigo_respaldo | cada 6 horas, minuto 20 | paticas-codigo-respaldo.timer | pat-php-app01 |

Control desde el panel vía `paticas-tareas-ctl` (sudoers exacto, incluida la acción `schedule <token>`).
El respaldo se controla desde .106 con la llave `/etc/lospaticas/ssh/paticas-backup-inventario`
y el comando forzado en .100 (`paticas-backup-ctl`).
`schedule` solo escribe el drop-in y hace `daemon-reload` (si el timer ya está activo, recálcula la próxima ejecución).
No habilita, no deshabilita y no arranca el servicio.
Desde el panel (php-fpm con ProtectSystem=full) el drop-in se escribe con systemd-run, porque /etc es de solo lectura incluso tras sudo.
