<?php
session_start();


?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1"> 
  </head>

  <style>
    table.dataTable tr.odd { background-color:#D6D6D6; }
    table.dataTable tr.even { background-color: white; }
  </style>

    
  <?php

  
    require("../cnx/conexion.php");
    require("../cnx/utilitaria.controlador.php");
    require("../cnx/utilitaria.modelo.php");
  
      
?>
<button onclick="Pagos()" class="btn btn-warning">Agregar Pago</button> <button onclick="Estado()" class="btn btn-primary">Listado de Pagos</button> 
<br><br>
<table id="resultado" style="color:black" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

        <thead>
                <th style="width:10px">#</th>
                <th>Nombre</th>
                <th>Total Pagado</th>
                <th>Ultimo Pago</th>
                <th>Saldo</th>
        </thead>
        <tbody>
<?php 
        $sql="SELECT * FROM ASO_Asociados  WHERE Activo ='1' ORDER BY Id";
        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

        foreach ($ejecutar as $key => $value) {
          $codigos=$value["Id"];
          $nombre=$value["Nombre"];
          $inicio=$value["Inicio"];
          $fechainicial = new DateTime($inicio);
          $fechafinal = new DateTime(date("Y-m-d"));
          $diferencia = $fechainicial->diff($fechafinal);
          $meses = ( $diferencia->y * 12 ) + $diferencia->m;
          $meses=$meses+1;
          $total_cuota=$meses*2000;  

          $sql_pagos="SELECT * FROM ASO_Cuotas WHERE Id=$codigos ORDER BY Fecha";
          $ejecutar1 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_pagos);

          $pagado=0;
          foreach ($ejecutar1 as $key => $arr) {
            $pago=$arr['Monto'];
            $pagado=$pagado+$pago;
            $fecha=$arr['Fecha'];
          }
          $saldo=$total_cuota-$pagado;  
          echo '<tr><td>'.$codigos.'</td>';
          echo '<td>'.$nombre.'</td>';
          echo '<td style="color:green"><b>'.number_format($pagado,2).'</b></td>';
          echo '<td>'.$fecha.'</td>';
          if ($saldo>0) {
            echo '<td style="color:red"><b>'.number_format($saldo,2).'</b></td>';  
          }else{
            echo '<td style="color:blue"><b>'.number_format($saldo,2).'</b></td>';
          }
          
          echo '</tr>';
        }

    ?>

  </tbody>
        <tfoot>
            <tr>
                <th style="width:10px">#</th>
                <th>Nombre</th>
                <th>Total Pagado</th>
                <th>Ultimo Pago</th>
                <th>Saldo</th>
               
            </tr>
        </tfoot>
    </table>

    <script>
    $(document).ready(function() {

      var table = $('#resultado').DataTable({
        "deferRender": true,
        "retrieve": true,
        "processing": true,
        "lengthMenu": [[10,20, -1],[10,20,50,"Mostrar Todo"]],
        "language": {

            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
            "sFirst":    "Primero",
            "sLast":     "Último",
            "sNext":     "Siguiente",
            "sPrevious": "Anterior"
            },
            "oAria": {
              "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
              "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
       } 
      });


  //for table row
    
 
    } );

  </script>
  
  