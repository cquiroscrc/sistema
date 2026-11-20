<?php
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

if($_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "caja" || $_SESSION["perfil"] == "bodega" || $_SESSION["perfil"] == "mensajeria"){

  echo '<script>

    window.location = "inicio";

  </script>';

  return;

}


?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Articulos sin movimientos
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Articulos sin movimientos</li>
    </ol>
  </section>
  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <div class="input-group mb-3">
         Fecha de estudio: <input type="date"  id="fecha_mov"  name="fecha_mov" >&nbsp;&nbsp;

          <button class="btn btn-primary"  onclick="consultarSM()"><i class="fa fa-repeat"></i> Cargar</button>&nbsp;
          <button class="btn btn-success" onclick="Excel_mov()"><i class="fa fa-file-excel-o"></i> Excel</button>&nbsp;

            <button class="btn btn-warning"  onclick="consultarSM0()"><i class="fa fa-repeat"></i> Cargar en 0</button>&nbsp;
            <button class="btn btn-danger" onclick="Excel_mov0()"><i class="fa fa-file-excel-o"></i> Excel en 0</button>  &nbsp;

         </div>
          
         <h4 id="movimiento"></h4>
      <div class="box-body">
          <center>
          <div id="resultados">
              <!-- AQUI VA EL AJAX -->
          </div>
          </center>

      </div>

    </div>

  </section>

</div>


<!-- ///**** SCRIPT -->
<script>
  
  $(document).ready(function(){


  });

function consultarSM(){
    var fechaSM= $("#fecha_mov").val();
    $('#resultados').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
    $.ajax('vistas/modulos/promedio/promedio_movimientos.php?fecha='+fechaSM)
        .done(function(response){
            $('#resultados').html(response);
        });
}

  function consultarSM0(){
      var fechaSM= $("#fecha_mov").val();
      $('#resultados').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
      $.ajax('vistas/modulos/promedio/promedio_movimientos0.php?fecha='+fechaSM)
          .done(function(response){
              $('#resultados').html(response);
          });
  }
  function Excel_mov() {
      var fechaSM= $("#fecha_mov").val();
      if (fechaSM=='') {
          swal({
              type: "error",
              title: "La fecha no puede estar vacio",
              showConfirmButton: true,
              confirmButtonText: "Cerrar"
          }).then(function(result){

          });

      }else{
          window.open("vistas/modulos/promedio/promedio_excel.php?fecha="+fechaSM);
      }
  }
  function Excel_mov0() {
      var fechaSM= $("#fecha_mov").val();
      if (fechaSM=='') {
          swal({
              type: "error",
              title: "La fecha no puede estar vacio",
              showConfirmButton: true,
              confirmButtonText: "Cerrar"
          }).then(function(result){

          });

      }else{
          window.open("vistas/modulos/promedio/promedio_excel0.php?fecha="+fechaSM);
      }
  }

</script>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Promedio';
</script>
