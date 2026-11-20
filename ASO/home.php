<?php 
session_start();

date_default_timezone_set("America/Costa_Rica");

if(isset($_SESSION['Nombre'])){
    $admin=true;
}else {
    header("location: index.php");
    $admin=false;
}

require("cnx/conexion.php");
require("cnx/utilitaria.controlador.php");
require("cnx/utilitaria.modelo.php");

 ?>



<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>APAHNC</title>
		
		<link rel="shortcut icon" href="images/icons/favicon.ico">
		<!-- Bootstrap 3.3.7 -->
  		<link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.min.css">
		<!-- DataTables -->
  		<link rel="stylesheet" href="vendor/DataTables/datatables.min.css">
  		<!-- Ionicons -->
  		<link rel="stylesheet" href="css/Ionicons/css/ionicons.min.css">
		<link rel="stylesheet" type="text/css" href="css/normalize.css" />
		<link rel="stylesheet" type="text/css" href="css/demo.css" />
		<link rel="stylesheet" type="text/css" href="css/component.css" />
		
		<script src="js/modernizr.custom.js"></script>
	</head>
	<body>
		<div class="container">
			<ul id="gn-menu" class="gn-menu-main">
				<li class="gn-trigger">
					<a class="gn-icon gn-icon-menu"><span>Menu</span></a>
					<nav class="gn-menu-wrapper">
						<div class="gn-scroller">
							<ul class="gn-menu">
								<li>
									<a onclick="Inicio()" class="gn-icon gn-icon-cog">Inicio</a>
								</li>
								<li>
									<a onclick="Asociados()" class="gn-icon gn-icon-earth">Asociados</a>
								</li>
								<li>
									<a onclick="Cuotas()" class="gn-icon gn-icon-archive">Estado de Cuotas</a>
								</li>
							</ul>
						</div><!-- /gn-scroller -->
					</nav>
				</li>
				<span><h1 style="display: inline;">&nbsp;Asociación de Padres Adoptivos Hijos de Corazón&nbsp;</h1><img src="images/LogoV.png"  style="width:64px;height:64px;"></span>
				<li><a class="codrops-icon codrops-icon-drop" href="cnx/procesar.php?salir=si"><span>Salir</span></a></li>

			</ul>
			<header>
				
				<div id="contenido">
					
					
				</div>
				
			</header>
			 
		</div><!-- /container -->

<!--=====================================
MODAL Ingresar Pago
======================================-->
<div id="modalPago" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background:#3c8dbc; color:white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Ingresar Pago</h4>
      </div>
     <div style="max-height:250px" class="modal-body">
     		<select class="form-control " id="Select_Asociado">
     			<?php 
     				$sql = "SELECT * FROM ASO_Asociados ORDER BY Nombre";
     				$respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
     				foreach ($respuesta as $key => $arr) {
                        $socio=$arr['Id'];
                        $nombre=$arr['Nombre'];
                        echo '<option value="'.$socio.'">'.$nombre.'</option>';
                    }
     			 ?>
            </select>
        	<br>
            <input class="form-control" id="Monto_pago"  placeholder="Digite el monto del pago" autocomplete="nuevo" required autofocus>
            <br>
            <input class="form-control" type="date" id="Fecha_pago" required autofocus>
      </div>
      <div class="modal-footer">
        <button id="btn_salir" type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" class="btn btn-primary" onclick="AplicarPago()">Aplicar</button>
      </div>
    </div>

  </div>
</div>

<!--=====================================
MODAL Ingresar Asociado
======================================-->
<div id="modalAsociado" class="modal fade" tabindex="-1" role="dialog">
  <div  class="modal-dialog modal-dialog-centered">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background:#3c8dbc; color:white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Ingresar Asociado</h4>
      </div>
     <div style="height:200px" class="modal-body">

     		<div class="form-group">
              <label class="control-label col-sm-4" for="A_Nombre" >Nombre:</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="A_Nombre" placeholder="Digite el nombre" autocomplete="nuevo" required>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-sm-4"  for="A_Cedula" >Cédula:</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="A_Cedula" placeholder="Digite la cédula" autocomplete="nuevo" required>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-4"  for="A_Telefono">Teléfono:</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="A_Telefono" placeholder="Digite el teléfono" autocomplete="nuevo" required>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-4" for="A_Correo" >Correo:</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="A_Correo" placeholder="Digite el correo Electrónico" autocomplete="nuevo" required>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-4"  for="A_Inicio">Inicio:</label>
              <div class="col-sm-8">
                <input type="date" class="form-control" id="A_Inicio" required>
              </div>
            </div>
        	
      </div>
      <div class="modal-footer">
        <button id="btn_salir" type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" class="btn btn-primary" onclick="AgregarAsociado()">Agregar</button>
      </div>
    </div>

  </div>
</div>

<!--=====================================
MODAL Editar Asociado
======================================-->
<div id="modalAsociadoE" class="modal fade" tabindex="-1" role="dialog">
  <div  class="modal-dialog modal-dialog-centered">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background:#3c8dbc; color:white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Editar Asociado</h4>
      </div>
     <div style="height:200px" class="modal-body">
     		<p id=Aso_Edit_Id></p>
     		<div class="form-group">
              <label class="control-label col-sm-4" for="E_Nombre" >Nombre:</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="E_Nombre" placeholder="Digite el nombre" autocomplete="nuevo" required>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-sm-4"  for="E_Cedula" >Cédula:</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="E_Cedula" placeholder="Digite la cédula" autocomplete="nuevo" required>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-4"  for="E_Telefono">Teléfono:</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="E_Telefono" placeholder="Digite el teléfono" autocomplete="nuevo" required>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-4" for="E_Correo" >Correo:</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="E_Correo" placeholder="Digite el correo Electrónico" autocomplete="nuevo" required>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-4"  for="E_Inicio">Inicio:</label>
              <div class="col-sm-8">
                <input type="date" class="form-control" id="E_Inicio" required>
              </div>
            </div>
        	
      </div>
      <div class="modal-footer">
        <button id="btn_salir" type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" class="btn btn-success" onclick="ActualizarAsociado()">Modificar</button>
      </div>
    </div>

  </div>
</div>



</body>
</html>





		<script src="js/classie.js"></script>
		<script src="js/gnmenu.js"></script>
		<!-- jQuery 3 -->
		<script src="vendor/moment/min/moment.min.js"></script>
		<script src="vendor/jquery/dist/jquery.min.js"></script>
  		<script src="js/jquery.min.js"></script>
  		<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  		<!-- DataTables -->
	    <script src="vendor/DataTables/datatables.min.js"></script>
		
		<script>

		    /*=============================================
		     DOCUMENT READY
		    =============================================*/
		    $(document).ready(function(){
		    	$('#contenido').html('<img src="images/Logo.png">');
		    });
		    
		    function Inicio(){
		    	$('#contenido').html('<img src="images/Logo.png">');
		    }

		    /*=============================================
		     Aplicar Pago
		    =============================================*/
		    function AplicarPago(){
		    	var asociado= $("#Select_Asociado").val();
		    	var pago = $("#Monto_pago").val();
		    	var fecha =$("#Fecha_pago").val();
		    	$("#modalPago").modal("hide");
		    	
    			var param = {
			        asociado: asociado,
			        pago: pago,
			        fecha: fecha
			    };

			    $.ajax({
			        data: param,
			        url: "mod/agregar_pago.php",
			        method: "post",
			        success: function(data) {
			            // Mensaje listo
			            if (data=="EXITO") {
			            	alert("Pago agregado correctamente")
			            	$("#Select_Asociado").val('');
					    	$("#Monto_pago").val('');
					    	$("#Fecha_pago").val('');
			            	 Cuotas();
			            }else{
			            	alert("Pago no se agregó")
			            }
			        }
			    });
		    }    

		    /*=============================================
		     Tabla de Cuotas
		    =============================================*/

		    function Cuotas(){
		    	$('#contenido').html('<div class="loading"><img src="images/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
		    	$.ajax('mod/cuota.php')
				  .done(function(response){
				    $('#contenido').html(response);
				  });
		    }

		    /*=============================================
		     Tabla de Estado
		    =============================================*/

		    function Estado(){
		    	$('#contenido').html('<div class="loading"><img src="images/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
		    	$.ajax('mod/estado.php')
				  .done(function(response){
				    $('#contenido').html(response);
				  });
		    }

		    /*=============================================
		     Tabla Asociados
		    =============================================*/
		    function Asociados(){
		    	$('#contenido').html('<div class="loading"><img src="images/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
		    	$.ajax('mod/asociados.php')
				  .done(function(response){
				    $('#contenido').html(response);
				  });
		    }

		    /*=============================================
		     Modal Pagos
		    =============================================*/
		    function Pagos(){
		    	$("#modalPago").modal();
		    }

		    /*=============================================
		     Modal Socio Nuevo
		    =============================================*/
		    function Add_Socio(){
		    	$("#modalAsociado").modal();
		    }
		    /*=============================================
		     Modal Modificar Socio
		    =============================================*/
		    function EditAsociado(asociado){
		    	var param= {
		    		asociado:asociado
		    	};
		    	$.ajax({
			        data: param,
			        url: "mod/buscar_asociado.php",
			        method: "post",
			        success: function(data) {
			            console.log(data);
			            var resultadoajax = JSON.parse(data)
                		var nombre =resultadoajax[0]["Nombre"];
                		var cedula =resultadoajax[0]["Cedula"];
                		var telefono =resultadoajax[0]["Telefono"];
                		var correo =resultadoajax[0]["Correo"];
                		var ID = resultadoajax[0]["Id"];
                		var dateString = moment(resultadoajax[0]["Inicio"]).format('YYYY-MM-DD');
                		var inicio =dateString;
						
						$("#Aso_Edit_Id").text(ID);
			            $("#E_Nombre").val(nombre);
		    			$("#E_Cedula").val(cedula);
		    			$("#E_Telefono").val(telefono);
		    			$("#E_Correo").val(correo);
		    			$("#E_Inicio").val(inicio);	
			            $("#modalAsociadoE").modal();
			        }
			    });
		    }
		    /*=============================================
		     Eliminar Asociado
		    =============================================*/
		    function DelAsociado(asociado){

		    	var opcion = confirm("Desea Eliminar el Asociado?");
			    if (opcion == true) {
			    	var param= {
				    		id:asociado
				    	};
				    	$.ajax({
					        data: param,
					        url: "mod/eliminar_asociado.php",
					        method: "post",
					        success: function(data) {
					            console.log(data);
					            if (data=="EXITO") {
					            	alert("Asociado eliminado correctamente")
					            	Asociados();
					            }else{
					            	alert("Asociado no se eliminó")
					            }
					            
					        }
					    });
			        
				} else {
				    
				}
		    	
		    }
		    /*=============================================
		     Eliminar Asociado
		    =============================================*/
		    function DelPago(asociado,monto,fecha){

		    	var opcion = confirm("Desea Eliminar este pago?");
			    if (opcion == true) {
			    	var param= {
				    		id:asociado,
				    		monto:monto,
				    		fecha:fecha
				    	};
				    	$.ajax({
					        data: param,
					        url: "mod/eliminar_pago.php",
					        method: "post",
					        success: function(data) {
					            console.log(data);
					            if (data=="EXITO") {
					            	alert("Pago eliminado correctamente")
					            	Estado();
					            }else{
					            	alert("Pago no se eliminó")
					            }
					            
					        }
					    });
			        
				} else {
				    
				}
		    	
		    }


		    /*=============================================
		     Agregar Asociado
		    =============================================*/
		    function AgregarAsociado(){
		    	var nombre= $("#A_Nombre").val();
		    	var cedula = $("#A_Cedula").val();
		    	var telefono =$("#A_Telefono").val();
		    	var correo =$("#A_Correo").val();
		    	var inicio =$("#A_Inicio").val();

		    	var param = {
			        nombre:nombre,
			        cedula:cedula,
			        telefono:telefono,
			        correo:correo,
			        inicio:inicio
			    };

			    $.ajax({
			        data: param,
			        url: "mod/agregar_asociado.php",
			        method: "post",
			        success: function(data) {
			            // Mensaje listo
			            console.log(data);
			            if (data=="EXITO") {
			            	alert("Asociado agregado correctamente")
			            	$("#A_Nombre").val("");
					    	$("#A_Cedula").val("");
					    	$("#A_Telefono").val("");
					    	$("#A_Correo").val("");
					    	$("#A_Inicio").val("");
					    	$("#modalAsociado").modal("hide");
			            	Asociados();
			            }else{
			            	alert("Asociado no se agregó")
			            }
			        }
			    });
		    }
		    /*=============================================
		     Actualizar Asociado
		    =============================================*/
		    function ActualizarAsociado(){
		    	var nombre= $("#E_Nombre").val();
		    	var cedula = $("#E_Cedula").val();
		    	var telefono =$("#E_Telefono").val();
		    	var correo =$("#E_Correo").val();
		    	var inicio =$("#E_Inicio").val();
		    	var ID = $("#Aso_Edit_Id").text();
		    	var param = {
		    		id:ID,
			        nombre:nombre,
			        cedula:cedula,
			        telefono:telefono,
			        correo:correo,
			        inicio:inicio
			    };

			    $.ajax({
			        data: param,
			        url: "mod/modificar_asociado.php",
			        method: "post",
			        success: function(data) {
			            // Mensaje listo
			            console.log(data);
			            if (data=="EXITO") {
			            	alert("Asociado actualizado correctamente")
			            	$("#E_Nombre").val("");
					    	$("#E_Cedula").val("");
					    	$("#E_Telefono").val("");
					    	$("#E_Correo").val("");
					    	$("#E_Inicio").val("");
					    	$("#Aso_Edit_Id").text("");
					    	$("#modalAsociadoE").modal("hide");
			            	Asociados();
			            }else{
			            	alert("Asociado no se modificó")
			            }
			        }
			    });
		    }


		    
			new gnMenu( document.getElementById( 'gn-menu' ) );
		</script>
	</body>
</html>