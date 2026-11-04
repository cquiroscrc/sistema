<?php
date_default_timezone_set("America/Costa_Rica");

include "modelos/procesos_pagos.php";

if($_SESSION["perfil"] == "ventas" ||  $_SESSION["perfil"] == "bodega"  || $_SESSION["perfil"] == "mensajeria"){


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
     background-color: #dddcdc;
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

</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Administrar Pagos a proveedores
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Administrar Pagos</li>
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
            <button onclick="AgregarPago()" id="add_mandado" class="btn btn-warning" data-toggle ="modal"  ><i class="fa fa-newspaper-o"></i>   Agregrar Pagos</button>
             <button onclick="ImprimirListado()" id="add_mandado" class="btn btn-info" data-toggle ="modal"  ><i class="fa fa-print"></i>   Imprimir Listado</button>
             <button onclick="ChequeManual()" id="add_mandado" class="btn btn-danger" data-toggle ="modal"  ><i class="fa fa-print"></i>   Cheque Manual</button>
             <button onclick="AplicarPago()" id="add_mandado" class="btn btn-success" data-toggle ="modal"  ><i class="fa fa-share"></i>   Aplicar Pagos</button>
        </div>
      <center>
      <br>
      <h2 style="color:blue;"><b>Listado de proveedores</b></h2>
       <select  class="confondo redondeado " id="datos" name="datos" >
            <optgroup >
                  <?php 
                   $sql="SELECT DISTINCT dbo.LPAD('0',5,rtrim(cxp.Proveedor)) + '-'+ Proveedores.nombre,Proveedores.codigo,Proveedores.nombre
                        FROM cxp INNER JOIN Proveedores ON cxp.Proveedor = Proveedores.codigo
                        WHERE (cxp.Estado = 'Pendiente') AND (cxp.Proveedor <> '1')";  
                      
                     $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);  
                      foreach ($ejecutar as $key => $arr) {
                        echo "<option value='".$arr['codigo']."'>".$arr['codigo']." - ".$arr['nombre']."</option>";
                      }

                   ?>
          </optgroup>
        </select>
       <hr>
          <div id="caja_anular">
              Digite el número de Factura a uscar:
              <input type="text"  id="compra_buscar">
              <button onclick="BuscarFacturaC()"  class="btn btn-primary"  ><i class="fa fa-address-card-o"></i> Consulta
                  Facura</button>
              <br><br>
              <table id="agregar-compra">

              </table>


          </div>
          <hr>
      <div id="listado_pendientes" class="box-body">
            <!-- AQUI VA LA CONSULTA -->
      </div>
      <hr>
      <button onclick="LimpiarSaldo()" id="add_mandado" class="btn btn-default pull-right"  ><i class="fa fa-newspaper-o"></i>   Limpiar Saldo</button>
      <h3 style='color:red;'>Pendientes de Pago</h3>
      <div id="listado_pagar" class="box-body">
            <!-- AQUI VA LA RESPUESTA -->
      </div>
      </center>
    </div>
  </section>
</div> 
<!--=====================================
MODAL PAGO
======================================-->

<div id="modalAgregarPago" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content">
            <!--=====================================
            CABEZA DEL MODAL PAGO
            ======================================-->
            <div class="modal-header" style="background:#3c8dbc; color:white">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Agregar metodo de Pago</h4>
            </div>

            <!--=====================================
            CUERPO DEL MODAL PAGO
            ======================================-->

            <div class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">  

                    <div class="box-body">

                                
                                <div id="resultadoMandado">
                                    <form class="form-horizontal">
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="cuenta_bancaria">Cuenta Bancaria:</label>
                                          <div class="col-sm-8">          
                                            <select class="form-control redondeado confondo" id="cuenta_bancaria" name="cuenta_bancaria">
                                                <optgroup >
                                                  <?php 
                                                    $sql="SELECT * FROM tbl_Cuentas";
                                                    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                                                    foreach ($ejecutar as $key => $value) {
                                                      echo '<option value='.$value["Cuenta"].'>'.$value["Banco"].'-'.$value['Cuenta'].'</option>';
                                                    }
                                                   ?>
                                                </optgroup>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="cuenta_contable">Cuenta Contable:</label>
                                          <div class="col-sm-8">          
                                            <select class="form-control redondeado confondo" id="cuenta_contable" name="cuenta_contable">
                                                <optgroup >
                                                  <?php 
                                                    $sql="SELECT * FROM tbl_CuentasC";
                                                    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                                                    foreach ($ejecutar as $key => $value) {
                                                      echo '<option value='.$value["id"].'>'.$value["Descripcion"].'</option>';
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
                                          <label class="control-label col-sm-2" for="nuevo_cheque">Primer Cheque:</label>
                                          <div class="col-sm-8">
                                            <input type="text" class="form-control redondeado confondo" id="nuevo_cheque" placeholder="Ingrese el primer Cheque" name="nuevo_cheque" required>
                                          </div>
                                        </div>
                                    </form>
                                    
                                </div>   
                            
                             
                    </div>
                </div>
        


                <!--=====================================
                PIE DEL MODAL PAGO
                ======================================-->

                <div class="modal-footer">

                  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                  <button  onclick="AgregarPagoCheque()" type="button" class="btn btn-success pull-right" >Aplicar</button>

                </div>

            </div>
        </div>
    </div>
</div>

<!--=====================================
MODAL PAGO INDIVIDUAL
======================================-->

<div id="modalAgregarPagoIndividual" class="modal fade" tabindex="-1" role="dialog">
  
    <div class="modal-dialog" >

        <div class="modal-content">

          

            <!--=====================================
            CABEZA DEL MODAL PAGO INDIVIDUAL
            ======================================-->

            <div class="modal-header" style="background:#3c8dbc; color:white">

              <button type="button" class="close" data-dismiss="modal">&times;</button>

              <h4 class="modal-title">Agregar metodo de Pago</h4>

            </div>

            <!--=====================================
            CUERPO DEL MODAL PAGO INDIVIDUAL
            ======================================-->

            <div class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">  

                    <div class="box-body">

                                <div>
                                  <center>
                                    <h3 id="proveedorIND"></h3>
                                    <h3 id="proveedorNOM"></h3>
                                  </center>
                                </div>
                                <div id="resultadoMandado">
                                    <form class="form-horizontal">
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="cuenta_bancaria">Cuenta Bancaria:</label>
                                          <div class="col-sm-8">          
                                            <select class="form-control redondeado confondo" id="cuenta_bancariaI" name="cuenta_bancaria">
                                                <optgroup >
                                                  <?php 
                                                    $sql="SELECT * FROM tbl_Cuentas";
                                                    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                                                    foreach ($ejecutar as $key => $value) {
                                                      echo '<option value='.$value["Cuenta"].'>'.$value["Banco"].'-'.$value['Cuenta'].'</option>';
                                                    }
                                                   ?>
                                                </optgroup>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="cuenta_contable">Cuenta Contable:</label>
                                          <div class="col-sm-8">          
                                            <select class="form-control redondeado confondo" id="cuenta_contableI" name="cuenta_contable">
                                                <optgroup >
                                                  <?php 
                                                    $sql="SELECT * FROM tbl_CuentasC";
                                                    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                                                    foreach ($ejecutar as $key => $value) {
                                                      echo '<option value='.$value["id"].'>'.$value["Descripcion"].'</option>';
                                                    }
                                                   ?>
                                                </optgroup>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="nuevo_fecha">Fecha:</label>
                                          <div class="col-sm-8">
                                            <input type="date" class="form-control redondeado confondo" min="2018-01-01" id="nuevo_fechaI" placeholder="Seleccione la fecha" name="nuevo_fecha" required>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="nuevo_cheque"># Cheque:</label>
                                          <div class="col-sm-8">
                                            <input type="text" class="form-control redondeado confondo" id="nuevo_chequeI" placeholder="Ingrese el # Cheque" name="nuevo_cheque" required>
                                          </div>
                                        </div>
                                    </form>
                                    
                                </div>   
                            
                             
                    </div>
                </div>
        


                <!--=====================================
                PIE DEL MODAL PAGO INDIVIDUAL
                ======================================-->

                <div class="modal-footer">

                  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                  <button  onclick="AgregarPagoChequeIndividual()" type="button" class="btn btn-success pull-right" >Aplicar</button>

                </div>

            </div>
        </div>
    </div>
</div>
<!--=====================================
MODAL CHEQUE MANUAL
======================================-->

<div id="modalChequeManual" class="modal fade" tabindex="-1" role="dialog">
  
    <div class="modal-dialog" >

        <div class="modal-content">

          

            <!--=====================================
            CABEZA DEL MODAL CHEQUE MANUAL
            ======================================-->

            <div class="modal-header" style="background:#3c8dbc; color:white">

              <button type="button" class="close" data-dismiss="modal">&times;</button>

              <h4 class="modal-title">Hacer Cheque Manual</h4>

            </div>

            <!--=====================================
            CUERPO DEL MODAL CHEQUE MANUAL
            ======================================-->

            <div class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">  

                    <div class="box-body">

                                
                                <div id="resultadoMandado">
                                    <form class="form-horizontal">
                                      <div class="form-group">
                                          <label class="control-label col-sm-2" for="nuevo_fecha">A Nombre:</label>
                                          <div class="col-sm-8">
                                            <input type="text" class="form-control redondeado confondo" id="nuevo_nombre" placeholder="Escriba nombre del cheque" name="nuevo_nombre" required>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="nuevo_monto">Monto:</label>
                                          <div class="col-sm-8">
                                            <input type="text" class="form-control redondeado confondo" id="nuevo_monto" placeholder="Escriba el monto" name="nuevo_monto" required>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="nuevo_monto">Detalle:</label>
                                          <div class="col-sm-8">
                                            <input type="text" class="form-control redondeado confondo" id="nuevo_detalle" placeholder="Escriba el detalle" name="nuevo_detalle" required>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="cuenta_bancaria">Cuenta Bancaria:</label>
                                          <div class="col-sm-8">          
                                            <select class="form-control redondeado confondo" id="cuenta_bancariaM" name="cuenta_bancaria">
                                                <optgroup >
                                                  <?php 
                                                    $sql="SELECT * FROM tbl_Cuentas";
                                                    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                                                    foreach ($ejecutar as $key => $value) {
                                                      echo '<option value='.$value["Cuenta"].'>'.$value["Banco"].'-'.$value['Cuenta'].'</option>';
                                                    }
                                                   ?>
                                                </optgroup>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="cuenta_contable">Cuenta Contable:</label>
                                          <div class="col-sm-8">          
                                            <select class="form-control redondeado confondo" id="cuenta_contableM" name="cuenta_contable">
                                                <optgroup >
                                                  <?php 
                                                    $sql="SELECT * FROM tbl_CuentasC";
                                                    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                                                    foreach ($ejecutar as $key => $value) {
                                                      echo '<option value='.$value["id"].'>'.$value["Descripcion"].'</option>';
                                                    }
                                                   ?>
                                                </optgroup>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="nuevo_fecha">Fecha:</label>
                                          <div class="col-sm-8">
                                            <input type="date" class="form-control redondeado confondo" min="2018-01-01" id="nuevo_fechaM" placeholder="Seleccione la fecha" name="nuevo_fecha" required>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="nuevo_cheque">#Cheque:</label>
                                          <div class="col-sm-8">
                                            <input type="text" class="form-control redondeado confondo" id="nuevo_chequeM" placeholder="Ingrese el # Cheque" name="nuevo_cheque" required>
                                          </div>
                                        </div>
                                    </form>
                                    
                                </div>   
                            
                             
                    </div>
                </div>
        


                <!--=====================================
                PIE DEL MODAL CHEQUE MANUAL
                ======================================-->

                <div class="modal-footer">

                  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                  <button  onclick="ImprimirManual()" type="button" class="btn btn-danger pull-right" >Imprimir</button>
                  <button  onclick="AgregarPagoChequeIndividual()" type="button" class="btn btn-success pull-right" >Aplicar</button>

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
       ConsultaPagar()
       $('select#datos').on('change',function(){
          var valor = $(this).val();
          
          ConsultaPendientes(valor)
        });
        
        
        
    });

    /*=============================================
     FIN DOCUMENT READY
    =============================================*/

</script>

 <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Pagos';
</script>
