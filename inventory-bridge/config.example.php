<?php

/**
 * Copiá este archivo a config.php en el nodo con IP pública
 * y ajustá credenciales / token.
 */
return [
    // Debe coincidir con INVENTORY_SYNC_TOKEN del portal (SiteGround).
    'token' => 'cambia-este-token',

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
];
