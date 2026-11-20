<?php 

include "modelos/procesos_caja.php";
include("db.php");


if($_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "bodega" || $_SESSION["perfil"] == "mensajeria"){
  echo '<script>
    window.location = "inicio";
  </script>';
  return;

}



 ?>
<style>
  table{border-collapse:collapse;}
  th,tr,td{
    border:1px solid #000;
    width:150px;
    height:45px;
    vertical-align:middle;
    text-align:center;
  }
  th{
    color:#fff;
    background-color: #252525;
  }

  tr:nth-child(odd) td{
    background-color:#eee;
  }

  .parpadea {

      animation-name: parpadeo;
      animation-duration: 1s;
      animation-timing-function: linear;
      animation-iteration-count: infinite;

      -webkit-animation-name:parpadeo;
      -webkit-animation-duration: 1s;
      -webkit-animation-timing-function: linear;
      -webkit-animation-iteration-count: infinite;
  }

  @-moz-keyframes parpadeo{
      0% { opacity: 1.0; }
      50% { opacity: 0.0; }
      100% { opacity: 1.0; }
  }

  @-webkit-keyframes parpadeo {
      0% { opacity: 1.0; }
      50% { opacity: 0.0; }
      100% { opacity: 1.0; }
  }

  @keyframes parpadeo {
      0% { opacity: 1.0; }
      50% { opacity: 0.0; }
      100% { opacity: 1.0; }
  }

</style>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1 style="display:inline;color: blue;">
      Cierre de Caja
    </h1> 
    <h1 style="display:inline;color:maroon" id="FechaCierre"></h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Cierre de Caja</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="row">
      &nbsp;&nbsp;&nbsp;&nbsp;Fecha: <input type="date" id="fecha_cierre_caja" name="fecha"  min="2018-01-01" >
        <button class="btn btn-primary" onclick="CargarFecha()">Cargar</button>&nbsp;&nbsp;&nbsp;
    <div class="box-tools pull-right">
        <button onclick="HacerRecibo()"  class="btn btn-info" data-toggle ="modal"  ><i class="fa fa-credit-card"></i> Hacer Recibo</button>
      <button onclick="CambioFecha()" id="consulta" class="btn btn-danger" data-toggle ="modal"  ><i class="ion ion-ios-calendar-outline"></i> Cambio de Fecha</button>
      <?php


        if($_SESSION["perfil"] == "total"  || $_SESSION["perfil"] == "especial" || $_SESSION["perfil"] == "parcial") { //** CAMBIO
       ?>
          <button onclick="ReporteFacturas()" id="consulta" class="btn btn-primary" data-toggle ="modal"  ><i class="ion ion-ios-list-outline"></i> Reporte Facturas</button>
          <button onclick="ReporteCierre()" id="consulta" class="btn btn-success" data-toggle ="modal"  ><i class="ion ion-android-print"></i> Reporte Cierre</button>
        <?php 
        }
         ?>
          <span>&nbsp;&nbsp;</span>
      <br>
    </div>

    </div> 
    <br>
   

      
        <div class="row"> <!--  INICIA FILA -->
         <!-- **** -->
            <div class="col-lg-6">
              <?php 
              if($_SESSION["perfil"] == "total"  || $_SESSION["perfil"] == "especial"){  // *** CAMBIO
             ?>
              <div class="box box-primary collapsed-box">
       <?php 
          }else{
            echo '<div class="box box-primary ">';
          }
         ?>       
                <div class="box-header with-border">
                  <h3 class="box-title">Recibos: <span style="color:red;" id="Enc_Recibos"></span></h3>
                <div class="box-tools pull-right">
                  <!-- Collapse Button -->
                  <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                  </button>
                </div>
                <!-- /.box-tools -->
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <div id="caja_recibos">
                  
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                
              </div>
              <!-- box-footer -->
            </div>
            <!-- /.box -->
          </div>
         
          <div class="col-lg-6">
             <?php 
          if($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "especial" ){  // *** CAMBIO
          ?>
              <div class="box box-warning collapsed-box">
                <div class="box-header with-border">
                  <h3 class="box-title">Total Caja: <span style="color:green;" id="Enc_Total"></span></h3>
                <div class="box-tools pull-right">
                  <!-- Collapse Button -->
                  <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                  </button>
                </div>
                <!-- /.box-tools -->
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <div id="caja_total">
                  
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                
              </div>
              <!-- box-footer -->
            </div>
            <!-- /.box -->
              <?php } ?>
              <?php 

                  if($_SESSION["perfil"] == "caja" || $_SESSION["perfil"] == "parcial" ||$_SESSION["perfil"] == "total"){
                    ?>
                    <div class="box box-warning">
                      <div class="box-header with-border">
                          <h3 class="box-title">Anular Factura: <span style="color:green;" ></span></h3>
                          <div class="box-tools pull-right">
                             <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                          </div>
                        </div>
                         <div class="box-body">
                            <div id="caja_anular">
                                Digite el número de Factura:
                                 <input type="text" onkeydown="anularKey(event)" id="anular_buscar"> 
                                 <button onclick="BuscarFactura()"  class="btn btn-primary"  ><i class="fa fa-address-card-o"></i> Consulta
                                 Factura</button>
                                 <table id="agregar-factura">
                                     <?php

                                     //$data=file_get_contents("http://apis.gometa.org/tdc/tdc.json");  // Go Meta
                                     //$data=file_get_contents("https://api.hacienda.go.cr/indicadores/tc/dolar");  
                                      $data=450;
                                     //Hacienda
                                     $tc = json_decode($data, true);
                                     //$compra= $tc['compra']['valor'];  // Hacienda
                                     $compra= $tc['compra'];  // Go Meta

                                     $compra=515;
                                     echo '<p style="color:#ff0000;"><b>Tipo de Cambio = '.number_format($compra,0).'</b></p>';

                                     $sql= "SELECT * FROM Consecutivos where tipo='01'";
                                     if(($res = odbc_exec($id,$sql)) === false)
                                         die("Error en Select: ". odbc_errormsg($id));

                                     while($arr = odbc_fetch_array($res)){
                                         $estado= $arr["Estado"];
                                     }
                                         echo '<p style="color:springgreen;"><b>Estado Facturas = '.$estado.'</b></p>';
                                     ?>

                                 </table>
                                 <br>
                                 <button onclick="AnularFactura()" class="btn btn-danger" data-toggle ="modal"  ><i class="fa fa-ban"></i> Anular Factura</button>
                                <button onclick="LiberarFactura()" class="btn btn-warning" data-toggle ="modal"  ><i class="fa fa-bank"></i> Liberar Factura</button>


                            </div>
                         </div>              
                         <div class="box-footer">
                          
                         </div>
                      </div>
                    
                  <?php }?>

          </div>

        </div><!--  FIN FILA -->
      
        <div class="row"> <!--  INICIA FILA -->
         <!-- **** -->
            <div class="col-lg-6">
               <?php 
              if($_SESSION["perfil"] == "total"  || $_SESSION["perfil"] == "especial"  ){  // *** CAMBIO
             ?>
              <div class="box box-success collapsed-box">
             <?php 
            }else{
            echo '<div class="box box-success">';
             }
            ?>  

                <div class="box-header with-border">
                  <h3 class="box-title">Depositos: <span style="color:blue;" id="Enc_Depositos"></span></h3>
                <div class="box-tools pull-right">
                  <!-- Collapse Button -->
                  <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                  </button>
                </div>
                <!-- /.box-tools -->
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <div id="caja_depositos">
                  
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                
              </div>
              <!-- box-footer -->
            </div>
            <!-- /.box -->
          </div>
          <div class="col-lg-6">
             <?php 
              if($_SESSION["perfil"] == "total"  || $_SESSION["perfil"] == "especial"  ){  // *** CAMBIO
             ?>
              <div class="box box-danger collapsed-box">
              <?php 
            }else{
            echo '<div class="box box-danger">';
             }
            ?>  
                <div class="box-header with-border">
                  <h3 class="box-title">Egresos: <span style="color:green;" id="Enc_Egresos"></span></h3>
                <div class="box-tools pull-right">
                  <!-- Collapse Button -->
                  <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                  </button>
                </div>
                <!-- /.box-tools -->
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <div id="caja_egresos">
                  
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
               
              </div>
              <!-- box-footer -->
            </div>
            <!-- /.box -->
          </div>
          
        </div><!--  FIN FILA -->
          <!-- ******* FOOTER -->
         <?php 
        if($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "caja" || $_SESSION["perfil"] == "especial"){
         ?> 
         <div class="col-lg-14">
           
             <div class="box box-default">

             <div class="box-header">
                <div id="caja_resultado">
                  
                </div>
             </div>

             </div>


         </div>
         <?php } ?>
  </div>
     

  </section>
 
</div>

<!--=====================================
MODAL CAMBIO DE FECHA
======================================-->

  <div class="modal fade" id="ModalFecha" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Cambiar Fecha</h4>

        </div>
        <center>  
        <div style="max-height:120px" class="modal-body">
          <h3 style="color:red;" >Cambiar fecha del sistema</h3>
          <input type="date" id="fecha_cambio_caja" name="fecha"  min="2018-01-01" >
        </div>
      </center>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary" onclick="CambiarFecha()">Cambiar fecha</button>

        </div>
      </div>   
    </div>
  </div>
  
<!-- FIN MODAL -->
<!--=====================================
MODAL HACER RECIBO
======================================-->

<div class="modal fade" id="ModalHacerRecibo" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Hacer Recibo</h4>
            </div>
            <center>
                <div style="max-height:250px" class="modal-body">
                    <h3 style="color:red;" >Generar recibos de dinero</h3>
                        <select class="form-control " name="recibo_cliente" onkeydown="uniKeyCodeCR(event)" id="recibo_cliente">
                            <?php
                            $sql = "SELECT * FROM Clientes WHERE comentario <> '**DESACTIVADO**' AND codigo>10000 OR codigo=0 ORDER BY codigo";

                            $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                            foreach ($respuesta as $key => $arr) {
                                $cliente=$arr['codigo'];
                                $nombre=$arr['nombre'];
                                echo '<option value="'.$cliente.'">'.$nombre.'</option>';
                            }
                            ?>
                        </select>
<!--                    <input class="form-control" id="recibo_nombre" name="nombre" placeholder="Nombre" required autofocus>-->
                    <br>
                    <input class="form-control" id="recibo_descripcion" name="descripcion" placeholder="Descripcion de recibo" required autofocus>
                    <br>
                    <input class="form-control" id="recibo_monto" name="monto" placeholder="Monto de recibo" required autofocus>
                    <br>
                </div>
            </center>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

                <button type="submit" class="btn btn-primary" onclick="ReciboPDF()">Hacer Recibo</button>

            </div>
        </div>
    </div>
</div>

<!-- FIN MODAL -->
<!--=====================================
MODAL AGREGAR EGRESO
======================================-->

<div id="modalEgreso" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

     

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Egreso</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div style="max-height:300px" class="modal-body">

          <div class="box-body">

            <!-- ENTRADA PARA SELECCIONAR EGRESO -->

            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-pencil-square-o"></i></span> 

                <select class="form-control input-lg" name="nuevoTipo" onkeydown="uniKeyCodeTE(event)" onchange="ShowSelected()" id="nuevoTipo">
                  
                 <?php 
                      $sql = "SELECT * FROM tbl_TiposEgresos";

                      $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                      foreach ($respuesta as $key => $arr) {
                          $tipoE=$arr['Tipo'];
                          $descripcion=$arr['Descripcion'];
                          echo '<option value="'.$tipoE.'">'.$descripcion.'</option>';
                      }
                   ?>

                </select>

              </div>

            </div>

            <!-- ENTRADA PARA EL DOCUMENTO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-hashtag"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoDocumento" onkeydown="uniKeyCodeDE(event)" autocomplete="off" id="nuevoDocumento" placeholder="Ingresar número de documento" style="text-transform: uppercase;" required>

              </div>

            </div>

            <!-- ENTRADA PARA EL MONTO -->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-money"></i></span> 

                <input type="text" class="form-control input-lg" style="text-transform: uppercase;" autocomplete="off" onkeydown="uniKeyCodeME(event)" name="nuevoMonto" placeholder="Ingresar Monto" id="nuevoMonto" required>

              </div>

             </div>
              <!-- ENTRADA PARA EL SUBTOTAL -->

              <div class="form-group">

                  <div class="input-group">

                      <span class="input-group-addon"><i class="ion ion-edit"></i></span>

                      <input type="text" class="form-control input-lg" style="text-transform: uppercase;" autocomplete="off" onkeydown="uniKeyCodeST(event)" name="nuevoSub" placeholder="Ingresar Subtotal" id="nuevoSub" required>

                  </div>

              </div>
              <!-- ENTRADA PARA EL IVA -->

              <div class="form-group">

                  <div class="input-group">

                      <span class="input-group-addon"><i class="ion ion-ios-refresh-outline"></i></span>

                      <input type="text" class="form-control input-lg" style="text-transform: uppercase;" autocomplete="off" onkeydown="uniKeyCodeIVA(event)" name="nuevoIVA" placeholder="Ingresar IVA" id="nuevoIVA" required>

                  </div>

              </div>
            <!-- ENTRADA PARA LA DESCRIPICION -->

             <div id="gDescripcion" class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-newspaper-o"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoDescripcion" id="nuevoDescripcion" placeholder="Ingresar Descripcion"  style="text-transform: uppercase;" required>

              </div>

            </div>

            <!-- ENTRADA PARA EMPRESA -->

             <div id="gEmpresa" style="display:none" class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="  fa fa-institution"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoEmpresa" id="nuevoEmpresa"  onkeydown="uniKeyCodeEE(event)" placeholder="Ingresar Empresa de encomienda" style="text-transform: uppercase; " required>

              </div>

            </div> 
            <!-- ENTRADA PARA LA HORA -->

             <div id="gHora" style="display:none" class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="ion ion-android-alarm-clock"></i></span> 
                <input type="text" class="form-control input-lg" name="nuevoHora" id="nuevoHora"  onkeydown="uniKeyCodeHE(event)" placeholder="Ingresar Hora encomienda" style="text-transform: uppercase; " required>

              </div>

            </div>  
            <!-- ENTRADA PARA LUGAR -->

             <div id="gLugar" style="display:none" class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="  fa fa-map-marker"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoLugar" id="nuevoLugar"  onkeydown="uniKeyCodeLE(event)" placeholder="Ingresar lugar encomienda" style="text-transform: uppercase;" required>

              </div>

            </div>         
            <!-- ENTRADA PARA CLIENTE -->

             <div id="gCliente" style="display:none" class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-male"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoCliente" id="nuevoCliente"   placeholder="Ingresar nombre cliente" style="text-transform: uppercase; " required>

              </div>

            </div>

            <!-- HASTA AQUI -->
          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary" onclick="GuardarEgreso()">Guardar Egreso</button>

        </div>

    </div>

  </div>

</div>







<!--=====================================
MODAL AGREGAR RECIBOS
======================================-->

<div id="modalRecibo" class="modal fade" role="dialog">
  
  <div class="modal-dialog" >

    <div class="modal-content">

      

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Recibo</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div style="max-height:300px" class="modal-body">

          <div class="box-body">

            <!-- ENTRADA PARA SELECCIONAR EL CLIENTE -->

            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-user"></i></span> 

                <select class="form-control input-lg" name="nuevoClienteRec" onkeydown="uniKeyCodeCR(event)" id="nuevoClienteRec">

                  <?php 
                      $sql = "SELECT * FROM Clientes WHERE comentario <> '**DESACTIVADO**' ORDER BY codigo";

                      $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                      foreach ($respuesta as $key => $arr) {
                        $cliente=$arr['codigo'];
                        $nombre=$arr['nombre'];
                        echo '<option value="'.$cliente.'">'.$nombre.'</option>';
                      }
                   ?>

                 </select>

              </div>

            </div>

            <!-- ENTRADA PARA EL RECIBO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-pencil"></i></span> 

                <input type="text" class="form-control input-lg" onkeydown="uniKeyCodeRe(event)" autocomplete="off" name="nuevoRecibo" id="nuevoRecibo" placeholder="Ingresar Recibo" required>

              </div>

            </div>

            <!-- ENTRADA PARA EL MONTO-->

             <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="ion ion-cash"></i></span> 

                <input type="text" class="form-control input-lg" autocomplete="off"  name="nuevoMontoRec" placeholder="Ingresar Monto" id="nuevoMontoRec" required>

              </div>

            </div>
          </div>
             

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary" onclick="GuardarRecibo()">Guardar Recibo</button>

        </div>

    </div>

  </div>
</div>

</div>




<!--- -->
 
<!--=====================================
MODAL AGREGAR DEPOSITOS
======================================-->

<div id="modalDepositos" class="modal fade" role="dialog">
    <div class="modal-dialog" >
      <div class="modal-content">
          <div class="modal-header" style="background:#3c8dbc; color:white">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Agregar Deposito</h4>
          </div>
          <div style="max-height:300px" class="modal-body">
            <div class="box-body">
               <!-- ENTRADA PARA EL DEPOSITO -->
              <div class="form-group"> 
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-hashtag"></i></span> 
                  <input type="text" class="form-control input-lg" onkeydown="uniKeyCodeDep(event)" autocomplete="off" name="nuevoDeposito" id="nuevoDeposito" placeholder="Ingresar Depósito" required>
                </div>
              </div>  
            </div>
            <!-- ENTRADA PARA EL MONTO-->
             <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-money"></i></span> 
                <input type="text" class="form-control input-lg" autocomplete="off" onkeydown="uniKeyCodeMD(event)" name="nuevoMontoDep" placeholder="Ingresar Monto" id="nuevoMontoDep" required>
              </div>
            </div>
            <!-- ENTRADA PARA SELECCIONAR EL BANCO -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-bank"></i></span> 
                <select class="form-control input-lg" name="nuevoBanco" id="nuevoBanco">
                  <?php 
                      $sql = "SELECT * FROM tbl_Cuentas";
                      $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                      foreach ($respuesta as $key => $arr) {
                        $cuenta=$arr['Cuenta'];
                        $descripcion=$arr['Banco'];
                        echo '<option value="'.$cuenta.'">'.$descripcion.'</option>';
                      }
                   ?>
                 </select>
              </div>
            </div>
          <!--=====================================
        PIE DEL MODAL
        ======================================-->  
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
            <button type="submit" class="btn btn-primary" onclick="GuardarDeposito()">Guardar Deposito</button>
          </div>
      </div>
    </div>
  </div>   
</div>





<script>
   $(document).ready(function(){

       Acc();
       sleep(10);
       Recargar();
    });

   function sleep(milliseconds) {
       const date = Date.now();
       let currentDate = null;
       do {
           currentDate = Date.now();
       } while (currentDate - date < milliseconds);
   }

function Recargar(){

    var fechaInicial = $("#FechaReportes").val();
    //var dateString = moment(fechaInicial).format('MM/DD/Y');
    //fecha=dateString;
    fecha=fechaInicial;
    $("#FechaCierre").text(fecha);
    CargarInicial(fecha);
}
</script> 
<script type="text/javascript" language="JavaScript">
   
    document.title = 'Sistema / Cierre diario';
</script> 