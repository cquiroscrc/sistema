<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

$fechas="SELECT * FROM fechas";
        $fecha = ControladorUtilitaria::ctrMostrarUtilitariaVar($fechas);
        foreach ($fecha as $key => $value) {               
             $fechaActual=$value["Fecha"];
            }
            $fechaActual=strtotime($fechaActual);
            $fechaActual = date("Y/m/d", $fechaActual);


?>
<style>
    table.dataTable tr.odd { background-color:#D6D6D6; }
    table.dataTable tr.even { background-color: white; }
</style>


<div class="box box-primary">
	
	<div class="box-header with-border">
    <h3 class="box-title">Factura Electronica</h3>
    
    <div class="box-tools pull-right">

      <button type="button" class="btn btn-box-tool" data-widget="collapse">

        <i class="fa fa-minus"></i>

      </button>

      <button type="button" class="btn btn-box-tool" data-widget="remove">

        <i class="fa fa-times"></i>

      </button>

    </div>
   </div> 
  <div class="box-body">  	
        <div>
            <input id="fechaFE" type="date">
            <button onclick="RecargarFE()">Recargar</button>
        </div>
      <center>
      <div id="listado_FE">

      </div>
      </center>
  	</div>

  	<div class="box-body">
  		
		

  	</div>

</div>

<script>
    function RecargarFE(){
        var fechaFE= $("#fechaFE").val()
        CargaFE(fechaFE);

    }
    function CargaFE(fecha){
        $('#listado_FE').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        var param = {
            fecha: fecha
        };

        $.ajax({
            data: param,
            url: "ajax/consulta_FE.ajax.php",
            method: "post",
            success: function(data) {
                $('#listado_FE').html(data);
            }
        });
    }

    $(document).ready(function() {
        CargaFE('<?php echo $fechaActual; ?>')



    } );

</script>