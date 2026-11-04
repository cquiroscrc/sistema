<?php

/**
 * Copiá este archivo a config.php en el nodo con IP pública
 * y ajustá credenciales / token.
 */
return [
    // Debe coincidir con INVENTORY_SYNC_TOKEN del portal (SiteGround).
    'token' => '46e4be69765ea2f88050cfd49fb2b4ee',

    'sql' => [
        'host' => '127.0.0.1',
        'port' => '1433',
        'database' => 'inventario',
        'username' => 'sa',
        'password' => 'PLUMA',
        'table' => 'inventario',
        'sku_column' => 'CODIGO',
        'name_column' => 'DESCRIPCION',
        'qty_column' => 'EXISTENCIA',
        'unit_column' => 'UNIDAD_MEDIDA',
        'price_column' => 'VENTA',
    ],
	
    // Carpeta local con subcarpetas de un carácter (0-9 / letra) y las fotos dentro.
    // Debe coincidir con la estructura que usa Odoo (p. ej. D:\fotos\subida_cat).
    'images_root' => 'C:\\xampp\\htdocs\\web paticas\\sistema\\subida_cat',
];
