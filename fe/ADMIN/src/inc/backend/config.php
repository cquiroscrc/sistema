<?php
/**
 * backend/config.php
 *
 * Author: pixelcave
 *
 * Backend pages configuration file
 *
 */

// **************************************************************************************************
// INCLUDED VIEWS
// **************************************************************************************************

$cb->inc_side_overlay           = 'inc/backend/views/inc_side_overlay.php';
$cb->inc_sidebar                = 'inc/backend/views/inc_sidebar.php';
$cb->inc_header                 = 'inc/backend/views/inc_header.php';
$cb->inc_footer                 = 'inc/backend/views/inc_footer.php';


// **************************************************************************************************
// MAIN MENU
// **************************************************************************************************

$cb->main_nav                   = array(
    array(
        'name'  => '<span class="sidebar-mini-hide">Configuracion</span>',
        'type'  => 'heading',
    ),
                    array(
                        'name'  => '<span class="sidebar-mini-hide">Compañia</span>',
                        'icon'  => 'si si-globe',
                        'url'   => 'pages_compañia.php',
                    ),
                    array(
                        'name'  => '<span class="sidebar-mini-hide">Hacienda</span>',
                        'url'   => 'pages_hacienda.php',
                        'icon'  => 'fa fa-money',
                    ),
                    array(
                        'name'  => '<span class="sidebar-mini-hide">Base de Datos</span>',
                        'url'   => 'be_pages_db.php',
                        'icon'  => 'fa fa-database',
                    ),
                    array(
                        'name'  => '<span class="sidebar-mini-hide">Clientes</span>',
                        'url'   => 'be_pages_clientes.php',
                        'icon'  => 'si si-user',
                    ),



    array(
        'name'  => '<span class="sidebar-mini-visible">DOC</span><span class="sidebar-mini-hidden">Documentos</span>',
        'type'  => 'heading'
    ),
        array(
            'name'  => '<span class="sidebar-mini-hide">Mantenimiento</span>',
            'icon'  => 'si si-globe',
            'url'   => 'pages_man_docelec.php',
        ),
        array(
            'name'  => '<span class="sidebar-mini-hide">Enviados</span>',
            'icon'  => 'fa fa-handshake-o',
            'url'   => 'pages_env_docelec.php',
        ),
        array(
            'name'  => '<span class="sidebar-mini-hide">Pendientes</span>',
            'icon'  => 'fa fa-hashtag',
            'url'   => 'pages_pen_docelec.php',
        ),

    array(
        'name'  => '<span class="sidebar-mini-hide">Usuarios</span>',
        'icon'  => 'si si-lock',
        'type'  => 'heading',
    ),
        array(
            'name'  => '<span class="sidebar-mini-hide">Administracion</span>',
            'icon'  => 'fa fa-user-secret',

        )
);
