<?php
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
include "modelos/procesos_promedio.php";

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
      
      Promedio para Pedidos
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Promedio Ventas</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
  
        
        <div class="input-group mb-3">
         Nombre Pedido: <input type="text" placeholder="Pedido" autocomplete="off" id="nombre_pedido"  style="text-transform: uppercase;" name="nombre_pedido" >&nbsp;&nbsp;

          <select   id="seleccionarPedido" name="seleccionarPedido" required>
            <option value="">SEL PEDIDO</option>
            <?php
                $sql = "SELECT DISTINCT pedido FROM hacer_pedido";
                $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($ejecutar as $key => $value) {
                    $p_pedido=$value['pedido'];
                    echo '<option value="'.$p_pedido.'">'.$p_pedido.'</option>';
                }

            ?>
          </select> &nbsp;

         <select   id="seleccionarProveedor" name="seleccionarProveedor" required>
            <?php
                $sql = "SELECT * FROM Proveedores  ORDER BY nombre";
                $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                foreach ($ejecutar as $key => $value) {
                    echo '<option value="'.$value["codigo"].'">'.$value["nombre"].'</option>';
                }

            ?>
          </select> &nbsp; 
          <button class="btn btn-defaul" style="color:white;background: grey;" onclick="myFunctionCodigo()"><i class="fa fa-repeat"></i> Cargar</button>&nbsp;
          <input type="text" id="selector"  placeholder="Articulo" aria-label="Username" autocomplete="off" style="text-transform: uppercase;"> &nbsp;
          <button onclick="myFunctionCodigo()" id="consulta" class="btn btn-primary" data-toggle ="modal"  ><i class="fa fa-search-plus"></i> Buscar Código!     </button>&nbsp;
          <?php
              $sql = "SELECT Valor FROM tbl_Utiles WHERE IDUtilidad=5";
              $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
              foreach ($ejecutar as $key => $value) {
                  $mesesact=$value["Valor"];
              }
          ?>
          <button class="btn btn-warning" onclick="Rev_pedido()"><i class="fa fa-files-o"></i> Revisar</button>&nbsp;
          <button class="btn btn-success" onclick="Exp_pedido()"><i class="fa fa-file-excel-o"></i> Excel</button>&nbsp;
          <button class="btn btn-danger" onclick="Limpiar_pedido()"><i class="fa fa-eraser"></i> Borrar</button>&nbsp;
          <button class="btn btn-default" onclick="Meses_Estudio()"><i class="fa fa-calendar"></i> Meses <?php echo $mesesact; ?></button>
         </div>
          
         <h4 id="nuevo_pedido"></h4>      
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
<!--=====================================
MODAL CAMBIO DE MESES
======================================-->

<div class="modal fade" id="ModalMeses" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background:#3c8dbc; color:white">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title">Cambiar Meses</h4>

            </div>
            <center>
                <div style="max-height:120px" class="modal-body">
                    <h3 style="color:red;" >Cambiar Meses de estudio sistema</h3>
                    <select  id="estudio" name="estudio">
                        <optgroup >
                            <option value="1">1 Mes</option>';
                            <option value="2">2 Meses</option>';
                            <option value="3">3 Meses</option>';
                            <option value="4">4 Meses</option>';
                            <option value="5">5 Meses</option>';
                            <option value="6">6 Meses</option>';
                            <option value="7">7 Meses</option>';
                            <option value="8">8 Meses</option>';
                            <option value="9">9 Meses</option>';
                            <option value="10">10 Meses</option>';
                            <option value="11">11 Meses</option>';
                            <option value="12">12 Meses</option>';
                        </optgroup>
                    </select>
                </div>
            </center>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

                <button type="submit" class="btn btn-primary" onclick="CambiarMeses()">Cambiar</button>

            </div>
        </div>
    </div>
</div>

<!-- FIN MODAL -->
<!--=====================================
MODAL MOVIMIENTOS
======================================-->

<div id="modalMovimientoProducto" class="modal fade" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-lg" >

        <div class="modal-content">



            <!--=====================================
            CABEZA DEL MODAL MOVIMIENTOS
            ======================================-->

            <div class="modal-header" style="background:#3c8dbc; color:white">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title">Movimiento de Codigo</h4>

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

<!--=====================================
MODAL AGREGAR EGRESO
======================================-->

<div id="modalRevisar" class="modal fade" role="dialog">
  
  <div class="modal-dialog modal-lg">

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

        <div style="max-height:800px" class="modal-body">

          <div class="box-body">

            <center>
            <div id=rev_pedido></div>
            <!-- AQUI VA EL AJAX -->
          </div>
        </center>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          

        </div>

    </div>

  </div>

</div>


<!-- ///**** SCRIPT -->
<script>
  
  $(document).ready(function(){


  });

  document.getElementById('selector').addEventListener('keydown', 
        function (event) {
          //console.log(document.getElementById("selector").value);
          
            if (event.which == 13 || event.keyCode == 13) {
                myFunctionCodigo();
                return false;
            }
        return true;
          
        }); 
          /*=============================================
          SELECCIONAR PEDIDO
          =============================================*/
          $('select#seleccionarPedido').on('change',function(){
              var valor = $(this).val();
              $("#nuevo_pedido").text(valor);

          });

</script>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Promedio';
</script>
