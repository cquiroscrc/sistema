<?php
date_default_timezone_set("America/Costa_Rica");

include "modelos/procesos_banco.php";

if($_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "cajas*" || $_SESSION["perfil"] == "bodega"|| $_SESSION["perfil"] == "mensajeria"){
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
      Mantenimiento de Bancos
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Mantenimiento de Bancos</li>
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
             <button onclick="AgregarBanco()" id="add_banco" class="btn btn-warning" data-toggle ="modal"  ><i class="ion ion-ios-redo"></i>   Agregar Movimiento</button>
             <button onclick="BuscarMovimiento()" id="buscar_movimiento" class="btn btn-info" data-toggle ="modal"  ><i class="ion ion-ios-redo"></i>   Buscar Movimiento</button>
             <button onclick="ModificarCuenta()" id="mov_cuenta" class="btn btn-default" data-toggle ="modal"  ><i class="ion ion-ios-redo"></i>   Modificar Cuenta</button>
             <button onclick="AgregarCuenta()" id="add_cuenta" class="btn btn-danger" data-toggle ="modal"  ><i class="ion ion-ios-redo"></i>   Agregar Cuenta</button>
            </div>
      <center>
      <h2 style="color:blue;"><b>Cuentas Bancarias</b></h2>
      <p id="codigobanco" style="display:block;"></p>
      <?php
        $sql="SELECT * FROM tbl_Cuentas WHERE Descripcion ='TAICOSTA'";
        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
      ?>
        <select  id="seleccionarbacos" name="seleccionarbancos" onChange="habilitar(this.form)">
              <optgroup >
          <?php
            foreach ($ejecutar as $key => $value) {
                $cuenta=$value["Cuenta"];
                $banco=$value["Banco"];
                echo "<option value='".$cuenta."'>".$banco." - ".$cuenta."</option>";
            }
           ?>
            </optgroup>
          </select>
          <button class="btn btn-primary" onclick="Cargarbanco()">Cargar</button>
      <div id="listado_bancos" class="box-body">
            <!-- AQUI VA LA CONSULTA -->
      </div>
      </center>
    </div>

  </section>

</div> 
<!--=====================================
MODAL DEPOSITO
======================================-->

<div id="modalAgregarDeposito" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content">
            <!--=====================================
            CABEZA DEL MODAL DEPOSITO
            ======================================-->
            <div class="modal-header" style="background:#3c8dbc; color:white">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Agregar Deposito</h4>
            </div>
            <!--=====================================
            CUERPO DEL MODAL DEPOSITO
            ======================================-->
            <div  class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="box-body">
                        <div id="resultadoMandado">
                            <form class="form-horizontal">
                                <div class="form-group">
                                  <label class="control-label col-sm-2 " for="tipo">Movimiento:</label>
                                  <div class="col-sm-8">
                                      <select class="form-control redondeado confondo" onkeydown="uniKeyCodeT(event)" id="tipo" name="tipo" required>
                                          <optgroup >
                                              <option value="tingreso">Transferencia Ingreso</option>
                                              <option value="tegreso">Transferencia Salida</option>
                                              <option value="oingreso">Otro Ingreso</option>
                                              <option value="oegreso">Otro Egreso</option>
                                              </optgroup>
                                      </select>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="control-label col-sm-2" for="nuevo_fecha">Fecha:</label>
                                  <div class="col-sm-8">
                                    <input type="date" class="form-control redondeado confondo" onkeydown="uniKeyCodeF(event)" min="2018-01-01" id="nuevo_fecha" placeholder="Seleccione la fecha" name="nuevo_fecha" required>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="control-label col-sm-2" for="nuevo_cuenta">Cuenta:</label>
                                  <div class="col-sm-8">
                                    <select class="form-control redondeado confondo" onkeydown="uniKeyCodeC(event)" id="nuevo_cuenta" name="nuevo_cuenta">
                                        <optgroup >
                                          <?php
                                            $sql="SELECT * FROM tbl_Cuentas WHERE Descripcion ='TAICOSTA'";
                                            $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                                            foreach ($ejecutar as $key => $value) {
                                              echo '<option value='.$value["Cuenta"].'>'.$value["Banco"].' - '.$value["Cuenta"].'</option>';
                                            }
                                           ?>
                                        </optgroup>
                                    </select>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="control-label col-sm-2" for="nuevo_documento">Documento:</label>
                                  <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" onkeydown="uniKeyCodeD(event)" id="nuevo_documento" placeholder="Ingrese # documento" name="nuevo_factura" required>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="control-label col-sm-2" for="nuevo_monto">Monto:</label>
                                  <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" onkeydown="uniKeyCodeM(event)" id="nuevo_monto" placeholder="Ingrese el monto" name="nuevo_monto" required>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="control-label col-sm-2" for="nuevo_lugar">Descripcion:</label>
                                  <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" onkeydown="uniKeyCodeDE(event)" id="nuevo_descripcion" placeholder="Ingrese la descripción" name="nuevo_lugar" required>
                                  </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="nuevo_cuenta">Cuenta Ingreso:</label>
                                    <div class="col-sm-8">
                                        <select class="form-control redondeado confondo" onkeydown="uniKeyCodeC2(event)" id="nuevo_cuenta2" name="nuevo_cuenta2">
                                            <optgroup >
                                                <option value='ninguna'>Ninguna cuenta</option>
                                                <?php
                                                $sql="SELECT * FROM tbl_Cuentas WHERE Descripcion ='TAICOSTA'";
                                                $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                                                foreach ($ejecutar as $key => $value) {
                                                    echo '<option value='.$value["Cuenta"].'>'.$value["Banco"].' - '.$value["Cuenta"].'</option>';
                                                }
                                                ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="nuevo_monto">Monto Ingreso:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control redondeado confondo"  id="nuevo_montoI" placeholder="Ingrese el monto Ingreso" name="nuevo_montoI">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!--=====================================
                PIE DEL MODAL DEPOSITO
                ======================================-->
                <div class="modal-footer">
                  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                  <button  onclick="AgregarDeposito()" type="button" class="btn btn-success pull-right" >Agregar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--=====================================
MODAL ADD CUENTA
======================================-->

<div id="modalAddCuenta" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content">
            <!--=====================================
            CABEZA DEL MODAL ADD CUENTA
            ======================================-->
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Agregar Cuenta Bancaria</h4>
            </div>
            <!--=====================================
            CUERPO DEL MODAL ADD CUENTA
            ======================================-->
            <div style="max-height:400px;" class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="box-body">
                        <div id="resultadoMandado">
                            <form class="form-horizontal">
                                <div class="form-group">
                                    <label class="control-label col-sm-2 " for="nuevo_banco">Nombre:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control redondeado confondo" onkeydown="uniKeyCodeADDN(event)"  id="nuevo_banco" placeholder="Nombre de la cuenta" name="nuevo_banco" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="nuevo_cuenta"># Cuenta:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control redondeado confondo" onkeydown="uniKeyCodeADDC(event)"  id="nuevo_cuentaB" placeholder="Número cuenta" name="nuevo_cuenta" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="nuevo_descripcion">Adiccional:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control redondeado confondo" onkeydown="uniKeyCodeADDD(event)" id="nuevo_descripcionB" placeholder="Adicional (TAICOSTA)" name="nuevo_descripcion" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="nuevo_moneda">Moneda:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control redondeado confondo"  id="nuevo_moneda" placeholder="Ingrese la moneda" name="nuevo_moneda" required>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!--=====================================
                PIE DEL MODAL ADD CUENTA
                ======================================-->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    <button  onclick="AgregarCuentaB()" type="button" class="btn btn-success pull-right" >Agregar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--=====================================
MODAL MOD CUENTA
======================================-->

<div id="modalModCuenta" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content">
            <!--=====================================
            CABEZA DEL MODAL MOD CUENTA
            ======================================-->
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Actualizar Cuenta Bancaria</h4>
            </div>
            <!--=====================================
            CUERPO DEL MODAL MOD CUENTA
            ======================================-->
            <div style="max-height:400px;" class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="box-body">
                        <div id="resultadoMandado">
                            <form class="form-horizontal">
                                <div class="form-group">
                                    <label class="control-label col-sm-2 " for="nuevo_banco">Nombre:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control redondeado confondo" onkeydown="uniKeyCodeADDN(event)"  id="editar_banco" placeholder="Nombre de la cuenta" name="editar_banco" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="nuevo_cuenta"># Cuenta:</label>
                                    <div class="col-sm-8">
                                        <input type="text" disabled class="form-control redondeado confondo" onkeydown="uniKeyCodeADDC(event)"  id="editar_cuenta" placeholder="Número cuenta" name="editar_cuenta" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="nuevo_descripcion">Adiccional:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control redondeado confondo" onkeydown="uniKeyCodeADDD(event)" id="editar_descripcion" placeholder="Adicional (TAICOSTA)" name="editar_descripcion" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="nuevo_moneda">Moneda:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control redondeado confondo"  id="editar_moneda" placeholder="Ingrese la moneda" name="editar_moneda" required>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!--=====================================
                PIE DEL MODAL MOD CUENTA
                ======================================-->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    <button  onclick="ActualizarCuentaB()" type="button" class="btn btn-success pull-right" >Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--=====================================
MODAL BUSCAR MOVIMIENTO
======================================-->
<div class="modal fade" id="ModalBuscar" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Buscar Movimiento</h4>
            </div>
            <center>
                <div style="max-height:150px" class="modal-body">
                    <input class="form-control-static redondeado confondo" type="text" id="busqueda" name="busqueda" placeholder="Ingrese la busqueda" >
                    <hr>
                    <select class="form-control-static redondeado confondo" onkeydown="uniKeyCodeT(event)" id="tbusqueda" name="tBusqueda" required>
                        <optgroup >
                            <option value="documento">Documento</option>
                            <option value="descripcion">Descripción</option>
                            <option value="monto">Monto</option>
                        </optgroup>
                    </select>
                </div>
            </center>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

                <button type="submit" class="btn btn-primary" onclick="HacerBusqueda()">Buscar</button>

            </div>
        </div>
    </div>
</div>
<!-- FIN MODAL -->
<!--=====================================
MODAL BUSQUEDA
======================================-->
<div id="modalBusqueda" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <!--=====================================
            CABEZA DEL MODAL MOVIMIENTOS
            ======================================-->
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Busqueda movimientos</h4>
            </div>
            <!--=====================================
            CUERPO DEL MODAL MOVIMIENTOS
            ======================================-->
            <div class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="box-body">
                        <div class="form-group">
                            <center>
                                <div id="resultadoM">
                                    <!-- AQUI VA EL AJAX -->
                                </div>
                            </center>
                        </div>
                    </div>
                </div>
                <!--=====================================
                PIE DEL MODAL MOVIMIENTOS
                ======================================-->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
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

    });
        


    /*=============================================
     FIN DOCUMENT READY
    =============================================*/

</script>

 <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Bancos';
</script>
