<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Menu Paticas">
    <meta name="author" content="hhernandezv">
    <script type="text/javascript" src="js/jquery-latest.min.js"></script> <!--  Imagenes fancy -->
    <script type="text/javascript" src="js/jquery.fancybox.pack.js?v=2.1.5"></script> <!--  Imagenes fancy -->
    
    <title>Sistema Los Paticas</title>

    <!-- Bootstrap Core CSS -->
    <link rel="icon" href="img/favicon.ico">

    <link href="css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
    body {
        padding-top: 70px;
        
    }
    </style>

   

</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <?php
                  if(isset($_SESSION['profile']['nombre'])){
                     $acceso=$_SESSION['profile']['tipo'];
                     $id_acceso=$_SESSION['profile']['id'];
                     $admin=true;

                  }else {
                
                       header("location: login.php");
                      $admin=false;
                  }
                ?>
                <?php 
                //  include("db.php");
                 // $sql="SELECT * FROM fechas WHERE movimiento ='Facturacion'" ;  
                 // if(($res = odbc_exec($id,$sql)) === false)
                 //   die("Error en Select: ". odbc_errormsg($id));
                 // while($arr = odbc_fetch_array($res)){
                  //  $fecha_sis=strtotime($arr['Fecha']);

                 // }
                 ?>
               
                <a style="color:orange" class="navbar-brand" href="index.php"><?php echo $_SESSION['profile']['nombre'] ?></a>
                 <a style="color:red" class="navbar-brand">
                     <?php //echo date("d-m-Y", $fecha_sis);?>    
                    
                    </a>

            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <?php
                    if ($acceso=='vochofest') {   /// ************* Menu de Vocho Fests  ****/////
                        
                         echo '<li><a style="color:cyan" href="consulta_simple.php">Consulta de codigos</a></li>';
                    }elseif ($acceso=='ventas') {   /// ************* Menu de Ventas  ****/////
                        echo '<li><a style="color:cyan" href="rep_articulos.php">Reporte Inventario</a></li>';
                         echo '<li><a style="color:red" href="consulta_simple.php">Consulta</a></li>';
                    }elseif ($acceso=='mensajeria') {  ///********************  Mensajeria *****//// 
                        echo '<li><a style="color:cyan" href="ingreso_mandado.php">Agregar mandado</a></li>';
                        echo '<li><a style="color:red" href="reporte_mandados.php">Reporte mandado</a></li>';
                    }elseif ($acceso=='eventos') {  ///********************  Mensajeria *****//// 
                        echo '<li><a style="color:red" href="consulta_evento.php">Consultas</a></li>';
                    }elseif ($acceso=='caja') {  ///********************  Menu de caja *****////
                        echo '<li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Cierre de Caja<span class="caret"></span></a>
                              <ul class="dropdown-menu">';
                              echo '<li><a style="color:cyan" href="caja_cierre_dia.php">Egresos de Caja</a></li>';
                              echo '<li><a style="color:lime" href="cambio_fecha.php">Cambio de Fecha</a></li>';
                        echo '</ul></li>';
                        
                        echo '<li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Facturas<span class="caret"></span></a>
                              <ul class="dropdown-menu">';
                              echo '<li><a style="color:cyan" href="anular_factura.php">Anular Facturas</a></li>'; /// *****Anular factura
                        echo '</ul></li>';
                                              
                      
                        echo '<li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Clientes<span class="caret"></span></a>
                              <ul class="dropdown-menu">';
                              echo '<li><a style="color:lime" href="carnet_ingreso.php">Clientes Frecuente</a></li>';  /// ************ Clientes frecuentes      
                              echo '<li><a style="color:blue" href="clientes_resultado.php">Clientes CXC</a></li>';
                              echo '<li><a style="color:red" href="telefono_resultado.php">Telefonos</a></li>';
                        echo '</ul></li>';

                        echo '<li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Compras / Ajustes<span class="caret"></span></a>
                              <ul class="dropdown-menu">';
                              echo '<li><a style="color:blue" href="reserva_consultas.php">Reserva</a></li>';
                              echo '<li><a style="color:green" href="compras_ingreso.php">Compras</a></li>';
                              echo '<li><a style="color:red" href="ajuste.php">Ajustes de Inventario</a></li>';
                              echo '<li><a style="color:green" href="pase_consulta_cambios.php">Pase de Nuevo Codigo</a></li>';
                        echo '</ul></li>';                        
                        echo '<li><a href="proveedores_pagos.php">Hacer Cheques</a></li>';  /// ************Cheques
                    }elseif ($acceso=='bodega') {  ///********************  Menu de Bodega *****////
                        echo '<li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Compras / Ajustes / Ubicaciones<span class="caret"></span></a>
                              <ul class="dropdown-menu">';
                        echo '<li><a style="color:red" href="compras_ingreso.php">Compras</a></li>';
                        echo '<li><a style="color:blue" href="ajuste.php">Ajustes</a></li>';
                        echo '<li><a style="color:green" href="ubicaciones_seleccion.php">Ubicaciones</a></li>';
                        echo '<li><a style="color:red" href="pase_consulta_simple.php">Pase de codigo</a></li>';
                        echo '<li><a style="color:black" href="pase_consulta_cambios.php">Pase de Nuevo Codigo</a></li>';
                        echo '</ul></li>';
                        
                        echo '<li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Inventario<span class="caret"></span></a>
                              <ul class="dropdown-menu">';
                        echo '<li><a style="color:red" href="consulta_simple.php">Consulta</a></li>';
                        echo '<li><a style="color:blue" href="inventario_consulta.php">Reporte con Promedio</a></li>';
                        echo '<li><a style="color:green" href="promedio_mes.php">Promedio Mensual para Inventario</a></li>';
                        echo '<li><a style="color:black" href="sucursal_consultas.php">Sucursal</a></li>';
                        echo '<li><a style="color:red" href="reserva_consultas.php">Reserva</a></li>';
                        echo '<li><a style="color:blue" href="eventos_consultas.php">Listado Eventos</a></li>';
                        echo '<li><a style="color:green" href="ventas_dia.php">Listado Ventas diarias</a></li>';
                        echo '<li><a style="color:green" href="catalogos_ingreso.php">Catalogos</a></li>';
                        echo '</ul></li>';
                        echo '<li><a href="pedido_cargar.php">Pedidos</a></li>';
                        echo '<li><a style="color:lime" href="telefono_resultado.php">Telefonos</a></li>';
                        echo '<li><a style="color:red" href="clientes_resultado_excel.php">C Frecuente</a></li>';
                        echo '<li><a style="color:blue" href="metas_visitas.php">Visitas Pagina</a></li>';
                    
                    }else{
                    ?>
                    
                      <!--/.Inventario -->
                      <li class="dropdown">
                        
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Inventario <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                                <li><a style="color:blue" href="consulta_simple.php">Consulta Codigo</a></li>
                                <li><a style="color:red" href="ajuste.php">Ajustes de Inventario</a></li>
                                <li><a style="color:green" href="mantenimiento.php">Mantenimiento de articulos</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="descripcion.php">Cambio de Descripciones</a></li>
                                <li><a style="color:blue" href="ubicaciones_seleccion.php">Ubicaciones de Articulos</a></li>
                                <li><a style="color:red" href="pase_consulta_simple.php">Pase de codigo</a></li>
                                <li><a style="color:green" href="pase_consulta_cambios.php">Pase de Nuevo Codigo</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a style="color:black" href="inventario_consulta.php">Reporte para Inventario</a></li>
                                <li><a style="color:blue" href="promedio_mes.php">Promedio Mensual para Inventario</a></li>
                                <li><a style="color:red" href="sucursal_consultas.php">Articulos en Sucursal</a></li>
                                <li><a style="color:green" href="reserva_consultas.php">Articulos en Reserva</a></li>
                                <li><a style="color:black" href="sobre_stock_consulta.php">Sobre Stock de Articulos</a></li>
                                <li><a style="color:blue" href="ventas_dia.php">Listado Ventas diarias</a></li>
                                <li><a style="color:red" href="eventos_consultas.php">Listado Eventos</a></li>
                                <li><a style="color:green" href="sin_movimiento_dia.php">Listado Movimientos</a></li>
                                <li role="separator" class="divider"></li>
                                
                                <li><a style="color:blue" href="fotos_ingresar.php">Ingresar Fotos</a></li>
                                <li><a style="color:green" href="catalogos_ingreso.php">Catalogos</a></li>
                                <li><a style="color:black" href="fotos_camara.php">Fotos</a></li> <!--desabilitado -->
                        </ul>
                      </li>
                      <!--/.Compras y Pedidos -->
                      <li class="dropdown">
                        
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Compras<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li><a style="color:green" href="compras_ingreso.php">Compras</a></li>
                          <li><a style="color:red" href="dev_compras_ingreso.php">Devolucion de Compras</a></li>
                          <li><a style="color:blue" href="temp_compras.php">Temporal Compras</a></li>
                          <li><a href="pedido_cargar.php">Pedidos</a></li>
                        </ul>
                      </li>
                      <!--/.Administrativo -->
                      <li class="dropdown">
                        
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Administrativo <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li><a href="metas.php">Reporte Metas</a></li>
                          <li><a style="color:#80299f" href="metas_piezas.php">Reporte Metas Piezas</a></li>
                          <li><a style="color:green"href="metas_agregar.php">Agregar Metas</a></li>
                          <li><a style="color:red" href="cierre_dia.php">Cierre diario</a></li>
                          <li><a style="color:blue" href="cambio_fecha.php">Cambio de fecha</a></li>
                          <li role="separator" class="divider"></li>
                          <li><a style="color:black" href="devolucion_ingreso.php">Devolucion Factura</a></li>
                          <li><a style="color:green"href="anular_devolucion.php">Anula Devolucion</a></li>
                          <li role="separator" class="divider"></li>
                          <li><a style="color:red" href="anular_factura.php">Anula Factura</a></li>
                          <li><a style="color:blue" href="anular_descuento.php">Anula Descuento</a></li>
                          <li role="separator" class="divider"></li>
                          <li><a style="color:black"href="telefono_resultado.php">Administrador de telefonos</a></li>

                          
                        <?php 
                              if ($acceso=='total') { 
                        ?>
                          <li role="separator" class="divider"></li>
                          <li><a style="color:green" href="planilla.php">Planilla</a></li>
                          <li role="separator" class="divider"></li>
                          <li><a href="proveedores_resultado.php">Proveedores CXP</a></li>
                          <li><a style="color:red" href="proveedores_pagos.php">Imprimir Cheques</a></li>
                          <li role="separator" class="divider"></li>
                          <li><a style="color:blue" href="usuarios_resultado.php">Administrador Usuarios</a></li>
                          <li><a style="color:red" href="ingreso_mandado.php">Agregar mandado</a></li>
                          <li><a style="color:green" href="reporte_mandados.php">Reporte mandado</a></li>
                          <li><a style="color:blue" href="rep_articulos_respaldos_sis.php">Reporte total Inventario</a>
                          <li><a style="color:red" href="espacios_blancos.php">Espacios en Blanco</a>  
                          </li>
                        <?php  
                          }  
                        ?>  
                          
                        </ul>
                      </li>
                       <!--/.Clientes -->
                      <li class="dropdown">
                        
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Clientes <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          
                          <li><a style="color:green" href="carnet_ingreso.php">Clientes Frecuente</a></li>
                          <li><a style="color:blue" href="clientes_resultado.php">Clientes CXC</a></li>
                          <li><a style="color:red" href="clientes_resultado_excel.php">Reporte Cliente Frecuente</a></li>
                          <li><a style="color:black" href="cumple.php">Cumpleaños</a></li>
                          <li><a style="color:green" href="clientes_resultado_saldo.php">Reporte Cliente Saldo</a></li>
                          <li><a style="color:blue" href="clientes_promedio.php">Reporte Cliente x Meses</a></li>
                          <li><a style="color:red" href="clientes_activar.php">Cliente Activacion</a></li>

                          
                        </ul>
                      </li>
                      <!--/.Consultas -->
                      <li class="dropdown">
                        
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Consultas <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          
                          <li><a style="color:green" href="gerencial_pedido_agregar.php">Consultas para pedidos</a></li>
                          <li><a style="color:blue" href="consultas_pedidos.php">Consultas de pedidos en proceso</a></li>
                          <li><a style="color:grey" href="promedio.php">Reporte excel pedido</a></li>
                          <li><a style="color:red" href="pedir_stock_consulta.php">Consultas de Bajos en Stock</a></li>
                          <li><a href="cambio_promedio.php">Cambio de meses de estudio</a></li>
                          <li><a style="color:green" href="eliminar_pedidos.php">Borrar Pedido</a></li>
                          <li><a style="color:blue" href="reporte_pedido.php">Reporte para pedidos</a></li>
                          <li><a style="color:red" href="minimos_rep_articulos.php">Reporte Stock Minimo</a></li>
                          <li role="separator" class="divider"></li>
                          <li><a href="consulta_factura.php">Consulta factura</a></li>
                          <li><a style="color:green" href="consulta_documento.php">Consulta documento</a></li>
                          
                        </ul>
                      </li>
                      <!--/.Reportes -->
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Reportes <span class="caret"></span></a>
                          <ul class="dropdown-menu">
                          <li><a  style="color:blue" href="cierre_ventas_mes.php">Cierre Mensual</a></li>
                          <li><a  style="color:red" href="cierre_compras_mes.php">Reporte de Compras</a></li>
                          <li><a  style="color:green" href="cierre_contables_mes.php">Cierres contables</a></li>
                          <li><a href="egresos_mes.php">Reporte Egresos</a></li>
                          <li role="separator" class="divider"></li>
                          <li><a style="color:red" href="cierre_financiero.php">Cierre Financiero</a></li>
                          <li role="separator" class="divider"></li>
                          <li><a  style="color:blue" href="rep_bancos.php">Reporte Bancos</a></li>
                          <li><a  style="color:red" href="rep_bancos.php">Busqueda Bancos</a></li>
                          <li role="separator" class="divider"></li>
                          <li><a style="color:blue" href="fechas_ventas_mes.php">Ventas Mensuales Productos</a></li>
                          <li><a href="Ventas_mes_clientes.php">Ventas x cliente</a></li>
                          <li><a style="color:red" href="totales_inventario.php">Totales de Inventario</a></li>
                          <li><a style="color:green" href="antiguedad_seleccion.php">Antiguedad de saldos</a></li>
                          <li role="separator" class="divider"></li>
                          <li><a  href="rep_articulos.php">Reporte de Articulos</a></li>
                          <li><a style="color:green" href="consulta_parecidos_resultado.php">Reporte parecidos</a></li>
                          <li><a style="color:red" href="metas_visitas.php">Metas Visita</a></li>
                          
                         
                        </ul>
                      </li>

                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Facturacion <span class="caret"></span></a>
                          <ul class="dropdown-menu">
                          <li><a  style="color:blue" href="factura_inicio.php">Facturar</a></li>
                          <li><a style="color:red" href="consulta_evento.php">Consultas</a></li>
                        </ul>
                      </li>
                    
                </ul>
                <?php } ?>
                <!--/.Salir -->
                  
                   
                 
                <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a style="color:#c20024"href="procesar.php?salir=si">Salir</a></li>
                </ul>
            </div>

            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Page Content -->
    <br>
    <br>