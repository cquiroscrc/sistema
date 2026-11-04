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

<table id="resultado" style="color:black" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

        <thead>
                <th>Fecha</th>
                <th>Nombre</th>
                <th>Monto</th>
                <th>Acciones</th>
        </thead>
        <tbody>
<?php 
        $sql="SELECT c.Id, c.Monto, c.Saldo, c.Fecha, a.Nombre,a.Inicio FROM ASO_Cuotas c, ASO_Asociados a WHERE c.Id = a.Id ORDER BY c.Fecha";
        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

        foreach ($ejecutar as $key => $value) {

      //ingresa datos en cuadro
          $codigos=$value["Id"];
          $nombre=$value["Nombre"];
          $saldo=$value["Saldo"];
          $monto=$value["Monto"];
          $fecha=$value["Fecha"];
          //$fecha = date("d/m/Y", strtotime($fecha));
          $inicio=$value["Inicio"];
            
            
            echo '<tr><td>'.$fecha.'</td>';
            echo '<td>'.$nombre.'</td>';
            echo '<td style="color:green"><b>'.number_format($monto,2).'</b</td>';
            echo '<td><button class="btn btn-danger" onclick="DelPago('."'".$codigos."'".','."'".$monto."'".','."'".$fecha."'".')"><i class="ion ion-close-circled"></i></button></td>';
            echo '</tr>';
        }

    ?>

  </tbody>
        <tfoot>
            <tr>
                <th>Fecha</th>
                <th>Nombre</th>
                <th>Monto</th>
                <th>Acciones</th>
               
            </tr>
        </tfoot>
    </table>

    <script>
    $(document).ready(function() {

      var table = $('#resultado').DataTable({
        "deferRender": true,
        "retrieve": true,
        "processing": true,
        "lengthMenu": [[5,10,20, -1],[5,10,50,"Mostrar Todo"]],
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
  
  