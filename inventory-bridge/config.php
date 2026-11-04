<?php

/**
 * Copiá este archivo a config.php en el nodo con IP pública
 * y ajustá credenciales / token.
 */
// Migracion 2026-11: conexion SQL desde /etc/lospaticas/db.php (172.16.12.100, login paticas_app)
$__lp_db = require '/etc/lospaticas/db.php';
return [
    // Debe coincidir con INVENTORY_SYNC_TOKEN del portal (SiteGround).
    'token' => '46e4be69765ea2f88050cfd49fb2b4ee',

    'sql' => [
        'host' => $__lp_db['host'], // antes: 127.0.0.1
        'port' => (string) $__lp_db['port'],
        'database' => 'inventario',
        'username' => $__lp_db['user'], // antes: sa
        'password' => $__lp_db['password'],
        'table' => 'inventario',
        'sku_column' => 'CODIGO',
        'name_column' => 'DESCRIPCION',
        'qty_column' => 'EXISTENCIA',
        'unit_column' => 'UNIDAD_MEDIDA',
        'price_column' => 'VENTA',
    ],
	
    // Carpeta local con subcarpetas de un carácter (0-9 / letra) y las fotos dentro.
    // Debe coincidir con la estructura que usa Odoo (p. ej. D:\fotos\subida_cat).
    'images_root' => '/var/www/lospaticas/sistema/subida_cat', // migracion: antes C:\\xampp\\htdocs\\web paticas\\sistema\\subida_cat
];
