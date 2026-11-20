<?php
include "modelos/procesos_reportes.php";
if($_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "caja" || $_SESSION["perfil"] == "bodega" || $_SESSION["perfil"] == "mensajeria"){
  echo '<script>
    window.location = "inicio";
  </script>';
  return;
}
?>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Reportes de ventas
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Reportes</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
        Fecha Inicio: <input type="date" id="fecha_inicio_reporte" name="fecha_inicio"  min="2018-01-01" >
        Fecha Fin: <input type="date" id="fecha_fin_reporte" name="fecha_fin" min="2018-01-01" >
        <button class="btn btn-primary" onclick="cargarfecha()">Cargar</button>
          <p style="display: inline-block;" id="f_inicio"></p>
          <p style="display: inline-block;" id="f_fin"></p>
          <p style="display: inline-block;" id="tipo_rep"></p>
        <div class="box-tools pull-right">
            <button class="btn btn-danger" style="margin-top:5px" onclick="rep_pdf()"><i class="fa fa-file-pdf-o"></i> Descargar reporte en PDF</button>
            <button class="btn btn-success" style="margin-top:5px" onclick="rep_excel()"><i class="fa fa-file-excel-o"></i> Descargar reporte en Excel</button>
        </div>
      </div>

      <div class="box-body">
        <div class="row">
            <div id="panel">

                <div class="col-6 col-sm-4">
                    <button class="btn btn-success" style="margin-top:5px" onclick="repventas()"><i class="fa fa-calendar"></i> Reporte Ventas</button>
                    <button class="btn btn-danger" style="margin-top:5px" onclick="repcompras()"><i class="fa fa-calendar-check-o"></i> Reporte Compras</button>
                    <button class="btn btn-warning" style="margin-top:5px" onclick="repcontable()"><i class="fa fa-credit-card"></i> Reporte Contable</button>
                    <hr>
                </div>

                <div class="col-6 col-sm-4">
                    <button class="btn btn-success" style="margin-top:5px" onclick="repfinanciero()"><i class="fa fa-bar-chart"></i> Reporte Financiero</button>
                    <button class="btn btn-danger" style="margin-top:5px" onclick="repbancos()"><i class="fa fa-bank"></i> Reporte Bancos</button>
                    <button class="btn btn-warning" style="margin-top:5px" onclick="repegresos()"><i class="fa fa-edit"></i> Reporte Egresos</button>
                    <hr>
                </div>

                <div class="col-6 col-sm-4">
                    <button class="btn btn-primary" style="margin-top:5px" onclick="repd151()"><i class="fa fa-users"></i> Reporte D-151</button>
                    <button class="btn btn-default" style="margin-top:5px" onclick="repCabys()"><i class="fa fa-calendar-check-o"></i> Reporte Cabys</button>
                    <hr>
                </div>

            </div>
        </div>
        <div class="row">
              <div id="panel2">

                  <div class="col-6 col-sm-4">
                      <button class="btn btn-success" style="margin-top:5px" onclick="conteo()"><i class="fa fa-calendar"></i> Conteos</button>
                      <button class="btn btn-danger" style="margin-top:5px" onclick="vendido()"><i class="fa fa-calendar-check-o"></i> Mal Vendido</button>
                      <button class="btn btn-warning" style="margin-top:5px" onclick="garantia()"><i class="fa fa-credit-card"></i> Garantías</button>
                      <hr>
                  </div>

                  <div class="col-6 col-sm-4">
<!--                      <button class="btn btn-success" style="margin-top:5px" onclick="vendidoE()"><i class="fa fa-bar-chart"></i> Mal Vendido E</button>-->
<!--                      <button class="btn btn-danger" style="margin-top:5px" onclick="vendidoS()"><i class="fa fa-bank"></i> Mal Vendido S</button>-->
<!--                     <button class="btn btn-warning" style="margin-top:5px" onclick="repegresos()"><i class="fa fa-edit"></i> Reporte Egresos</button>-->
                      <br><br>
                      <hr>
                  </div>

                  <div class="col-6 col-sm-4">
                      <button class="btn btn-primary" style="margin-top:5px" onclick="woocommerce()"><i class="fa fa-users"></i> No Woocommerce</button>
<!--                      <button class="btn btn-default" style="margin-top:5px" onclick="repCabys()"><i class="fa fa-calendar-check-o"></i> No está en woocommerce</button>-->
                      <hr>
                  </div>

              </div>
          </div>
<!--        adornos  -->
        <div class="row">
            <div class="col-12 col-sm-12">
                <h1>Mes Actual</h1>
            </div>
            <hr>
            <?php
                include "inicio/cajas-superiores.php";
            ?>

        </div>
          <hr>
        <div class="row">
              <div class="col-12 col-sm-12">
                  <?php
                  include "reportes/grafico-ventas.php";
                  ?>
              </div>
        </div>
      </div>
      
    </div>

  </section>
 
 </div>

<!--=====================================
MODAL AGREGAR EGRESO
======================================-->

<div id="modalFinanciero" class="modal fade" role="dialog">

    <div class="modal-dialog">

        <div class="modal-content">



            <!--=====================================
            CABEZA DEL MODAL
            ======================================-->

            <div class="modal-header" style="background:#3c8dbc; color:white">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title">Reporte Financiero</h4>

            </div>

            <!--=====================================
            CUERPO DEL MODAL
            ======================================-->

            <div style="max-height:300px" class="modal-body">
                <?php
                $sql = "SELECT TOP 1 * from tbl_Financiero ORDER BY Fecha DESC";
                $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($respuesta as $key => $arr) {
                    $ultima_fecha=strtotime($arr['Fecha']);
                    echo '<p>Ultimo Cierre efectuado '.date("d-m-Y", $ultima_fecha).'</p>';
                }
                ?>

                <div class="box-body">
                    <!-- ENTRADA COSTO FINANCIERO -->
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-money"></i></span>
                            <input type="text" class="form-control input-lg" style="text-transform: uppercase;" autocomplete="off" onkeydown="uniKeyCodeCF(event)" name="nuevoCosto" placeholder="Costo Financiero" id="nuevoCosto" required>
                        </div>
                    </div>
                    <!-- ENTRADA AGUA -->
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-tint"></i></span>
                            <input type="text" class="form-control input-lg" style="text-transform: uppercase;" autocomplete="off" onkeydown="uniKeyCodeAG(event)" name="nuevoAgua" placeholder="Agua" id="nuevoAgua" required>
                        </div>
                    </div>
                    <!-- ENTRADA LUZ -->
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-plug"></i></span>
                            <input type="text" class="form-control input-lg" style="text-transform: uppercase;" autocomplete="off" onkeydown="uniKeyCodeLZ(event)" name="nuevoLuz" placeholder="Luz" id="nuevoLuz" required>
                        </div>
                    </div>
                    <!-- ENTRADA TELEFONO -->
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                            <input type="text" class="form-control input-lg" style="text-transform: uppercase;" autocomplete="off" onkeydown="uniKeyCodeTL(event)" name="nuevoTel" placeholder="Teléfono" id="nuevoTel" required>
                        </div>
                    </div>
                    <!-- ENTRADA CCSS -->
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-thermometer"></i></span>
                            <input type="text" class="form-control input-lg" style="text-transform: uppercase;" autocomplete="off" onkeydown="uniKeyCodeCCSS(event)" name="nuevoCCSS" placeholder="C.C.S.S" id="nuevoCCSS" required>
                        </div>
                    </div>
                    <!-- ENTRADA TRIBUTACION -->
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-bank"></i></span>
                            <input type="text" class="form-control input-lg" style="text-transform: uppercase;" autocomplete="off" onkeydown="uniKeyCodeTRI(event)" name="nuevoTri" placeholder="Tributación" id="nuevoTRI" required>
                        </div>
                    </div>
                    <!-- ENTRADA T.C -->
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-balance-scale"></i></span>
                            <input type="text" class="form-control input-lg" style="text-transform: uppercase;" autocomplete="off" onkeydown="uniKeyCodeTC(event)" name="nuevoTC" placeholder="Tipo de Cambio" id="nuevoTC" required>
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
                <button type="submit" class="btn btn-success" onclick="GuardarFinanciero()"><i class="fa fa-pencil"></i> Guardar Datos</button>


            </div>

        </div>

    </div>

</div>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Reportes';
</script>