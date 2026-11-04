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
<button onclick="Add_Socio()" class="btn btn-warning">Agregar Asociado</button>
<br><br>

<table id="resultado" style="color:black" class="table table-bordered table-striped dt-responsive tablaProductos" width="100%">

        <thead>
               <th style="width:10px">#</th>
                <th>Nombre</th>
                <th>Cedula</th>
                <th>Telefono</th>
                <th>Correo</th>
                <th>Inicio</th>
                <th>Acciones</th>
        </thead>
        <tbody>
<?php 
        $sql="SELECT * FROM ASO_Asociados";           
        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

        foreach ($ejecutar as $key => $value) {

      //ingresa datos en cuadro
          $codigos=$value["Id"];
          $nombre=$value["Nombre"];
          $cedula=$value["Cedula"];
          $telefono=$value["Telefono"];
          $correo=$value["Correo"];
          $inicio=$value["Inicio"];
            
            
            echo '<tr><td>'.$codigos.'</td>';
            echo '<td>'.$nombre.'</td>';
            echo '<td>'.$cedula.'</td>';
            echo '<td>'.$telefono.'</td>';
            echo '<td>'.$correo.'</td>';
            echo '<td>'.$inicio.'</td>';
            echo '<td><button class="btn btn-success" onclick="EditAsociado('."'".$codigos."'".')"><i class="ion ion-android-create"></i></button>&nbsp;';
            echo '<button class="btn btn-danger" onclick="DelAsociado('."'".$codigos."'".')"><i class="ion ion-close-circled"></i></button></td>';
            echo '</tr>';
        }

    ?>

  </tbody>
        <tfoot>
            <tr>
               <th style="width:10px">#</th>
                <th>Nombre</th>
                <th>Cedula</th>
                <th>Telefono</th>
                <th>Correo</th>
                <th>Inicio</th>
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
        "lengthMenu": [[5,10,20, -1],[5,10,20,50,"Mostrar Todo"]],
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
  
  