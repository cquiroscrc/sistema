<?php
date_default_timezone_set("America/Costa_Rica");

include "modelos/procesos_mensajeria.php";

$usuario= $_SESSION["nombre"];
$perfil= $_SESSION["perfil"];
$id_mensajero='3';

$sql="SELECT * FROM mensajeros WHERE nombre= '$usuario'";
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
foreach ($ejecutar as $key => $value) {
  $id_mensajero=$value["id"];
}

$sql="SELECT * FROM mensajeros ";
$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
$x=0;
foreach ($ejecutar as $key => $value) {
  $listado[$x][0]=$value["id"];
  $listado[$x][1]=$value["nombre"];
  $x=$x+1;
}



if($_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "caja" || $_SESSION["perfil"] == "bodega"){


  echo '<script>

    window.location = "inicio";

  </script>';

  return;

}

?>

<style>
   
   .redondeado {
     border-radius: 5px;
   }
   .confondo {
     background-color: #def;
   }
   .sinborde {
     border: 0;
   }
   .sinbordefondo {
     background-color: #eee;
     border: 0;
   }
   .outlinenone {
     outline: none;
     background-color: #dfe;
     border: 0;
   }
   .redondeadonorelieve {
     border-radius: 5px;
     border: 1px solid #39c;
   }
    

    
</style>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Administrar Mensajería <?php echo $usuario; ?>
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Administrar Mensajería</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
        <div class="col-sm-5">
          <div class="input-group mb-3">
                 
          </div>
        </div>
        
            <div class="box-tools pull-right">
             <button onclick="myAgregarMandado()" id="add_mandado" class="btn btn-warning" data-toggle ="modal"  ><i class="ion ion-ios-redo"></i>   Agregar Mandado</button>
             <?php 

              if($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial" ){
              ?>
                 <button onclick="myImprimirMandado()" id="imp_mandado" class="btn btn-info" data-toggle ="modal"  ><i class="ion ion-ios-redo"></i>   Imprimir comprobante</button>
                 <button onclick="myAplicarMandado()" id="imp_mandado" class="btn btn-success" data-toggle ="modal"  ><i class="ion ion-android-checkbox-outline"></i>   Aplicar Mandados</button>
                
              <?php 
              }
               ?>  

             
        </div>   
      
      <center>
      
      <h2 style="color:blue;"><b>Listado de mandados</b></h2>
      <div id="listado_mandados" class="box-body">
            <!-- AQUI VA LA CONSULTA -->
      </div>
      </center>
    </div>

  </section>

</div> 
<!--=====================================
MODAL MANDADO
======================================-->

<div id="modalAgregarMandado" class="modal fade" tabindex="-1" role="dialog">
  
    <div class="modal-dialog" >

        <div class="modal-content">

          

            <!--=====================================
            CABEZA DEL MODAL MANDADO
            ======================================-->

            <div class="modal-header" style="background:#3c8dbc; color:white">

              <button type="button" class="close" data-dismiss="modal">&times;</button>

              <h4 class="modal-title">Agregar Mandado</h4>

            </div>

            <!--=====================================
            CUERPO DEL MODAL MANDADO
            ======================================-->

            <div class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">  

                    <div class="box-body">

                                
                                <div id="resultadoMandado">
                                    <form class="form-horizontal">
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="nuevo_motorizado">Mensajero:</label>
                                          <div class="col-sm-8">          
                                            <select class="form-control redondeado confondo" id="nuevo_motorizado" name="nuevo_motorizado">
                                                <optgroup >
                                                  <?php 
                                                    $sql="SELECT * FROM mensajeros";
                                                    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                                                    foreach ($ejecutar as $key => $value) {
                                                      echo '<option value='.$value["id"].'>'.$value["nombre"].'</option>';
                                                    }
                                                   ?>
                                                </optgroup>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="nuevo_fecha">Fecha:</label>
                                          <div class="col-sm-8">
                                            <input type="date" class="form-control redondeado confondo" min="2018-01-01" id="nuevo_fecha" placeholder="Seleccione la fecha" name="nuevo_fecha" required>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="nuevo_envia">Envia:</label>
                                          <div class="col-sm-8">          
                                            <select class="form-control redondeado confondo" id="nuevo_envia" name="nuevo_envia">
                                                <optgroup >
                                                  <?php 
                                                    $sql="SELECT * FROM vendedores";
                                                    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                                                    foreach ($ejecutar as $key => $value) {
                                                      echo '<option value='.$value["codigo"].'>'.$value["nombre"].'</option>';
                                                    }
                                                   ?>
                                                </optgroup>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="nuevo_factura">Factura:</label>
                                          <div class="col-sm-8">
                                            <input type="text" class="form-control redondeado confondo" id="nuevo_factura" placeholder="Ingrese la factura" name="nuevo_factura" required>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="nuevo_monto">Monto:</label>
                                          <div class="col-sm-8">          
                                            <input type="text" class="form-control redondeado confondo" id="nuevo_monto" placeholder="Ingrese el monto" name="nuevo_monto" required>
                                          </div>
                                        </div>
                                       
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="nuevo_lugar">Lugar:</label>
                                          <div class="col-sm-8">
                                            <input type="text" class="form-control redondeado confondo" id="nuevo_lugar" placeholder="Ingrese el lugar" name="nuevo_lugar" required>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="nuevo_cantidad">Cantidad:</label>
                                          <div class="col-sm-8">          
                                            <input type="number" class="form-control redondeado confondo" id="nuevo_cantidad" placeholder="Ingresar la cantidad" name="nuevo_cantidad" required>
                                          </div>
                                        </div>
                                      
                                        
                                    </form>
                                    
                                </div>   
                            
                             
                    </div>
                </div>
        


                <!--=====================================
                PIE DEL MODAL MANDADO
                ======================================-->

                <div class="modal-footer">

                  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                  <button  onclick="AgregarMandado()" type="button" class="btn btn-success pull-right" >Agregar</button>

                </div>

            </div>
        </div>
    </div>
</div>



<script>

    /*=============================================
     DOCUMENT READY
    =============================================*/
    $(document).ready(function(){
        
        myMandado(<?php echo $id_mensajero; ?>);
        
    });

    /*=============================================
     FIN DOCUMENT READY
    =============================================*/

</script>

 <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Mensajeria';
</script>
