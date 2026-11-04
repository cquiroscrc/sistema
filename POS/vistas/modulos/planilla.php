<?php
date_default_timezone_set("America/Costa_Rica");

if($_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "caja" || $_SESSION["perfil"] == "bodega" || $_SESSION["perfil"] == "mensajeria" || $_SESSION["perfil"] == "parcial"){
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

</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Recursos Humanos
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Recursos Humanos</li>
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
             <button onclick="AgregarPlanilla()" id="add_mandado" class="btn btn-success" data-toggle ="modal"  ><i class="fa fa-cloud-upload"></i>   Agregar Planilla</button>
             <button onclick="Empleados()" id="add_mandado" class="btn btn-warning" data-toggle ="modal"  ><i class="fa fa-address-card-o"></i>   Empleados</button>
             <button onclick="Vacaciones()" id="imp_mandado" class="btn btn-info" data-toggle ="modal"  ><i class="fa fa-fighter-jet"></i>   Vacaciones</button>
             <button onclick="MAguinaldo()" id="imp_mandado" class="btn btn-danger" data-toggle ="modal"  ><i class="fa fa-gift"></i>   Aguinaldos</button>
        </div>   
      
      <center>
      <h2 style="color:blue;"><b>Listado de planillas</b></h2>
      <div id="listado_planillas" class="box-body">
            <!-- AQUI VA LA CONSULTA -->
      </div>
      <hr>
      <div id="planilla_mostrar" class="box-body">
          <!-- AQUI VA LA CONSULTA -->
      </div>
      </center>
    </div>
  </section>
</div> 
<!--=====================================
MODAL EMPLEADOS
======================================-->
<div id="modalEmpleados" class="modal fade" tabindex="-1" role="dialog">
    <div style="width: 655px" class="modal-dialog" >
        <div class="modal-content">
            <!--=====================================
            CABEZA DEL MODAL EMPLEADOS
            ======================================-->
            <div class="modal-header" style="background:#3c8dbc; color:white">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Mantenimiento Empleados</h4>
            </div>
            <!--=====================================
            CUERPO DEL MODAL EMPLEADOS
            ======================================-->

            <div  class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="box-body">
                        <form class="form-horizontal">
                            <div class="form-group">
                              <label class="control-label col-sm-2" for="mod_empleado">Empleado:</label>
                              <div class="col-sm-8">
                                <select class="form-control redondeado confondo" id="mod_empleado" name="mod_empleado">
                                    <optgroup >
                                      <?php
                                       echo '<option value="">Seleccione el Empleado</option>';
                                        $sql="SELECT * FROM rh_empleados order by id_empleado";
                                        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                                        foreach ($ejecutar as $key => $value) {
                                          echo '<option value='.$value["id_empleado"].'>'.$value["nombre"].' '.$value["apellido"].'</option>';
                                        }
                                       ?>
                                    </optgroup>
                                </select>
                                  <br>
                                  <button onclick="BuscarEmpleado()" type="button">Buscar</button>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-sm-2" for="mod_nombre">Nombre:</label>
                              <div class="col-sm-8">
                                <input type="text" class="form-control redondeado confondo" id="mod_nombre" placeholder="Digite el nombre" name="mod_nombre" required>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-sm-2" for="mod_apellido">Apellido:</label>
                              <div class="col-sm-8">
                                  <input type="text" class="form-control redondeado confondo" id="mod_apellido" placeholder="Digite el apellido" name="mod_apellido" required>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-sm-2" for="mod_cedula">Cédula:</label>
                              <div class="col-sm-8">
                                <input type="text" class="form-control redondeado confondo" id="mod_cedula" placeholder="Digite la cédula" name="mod_cedula" required>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-sm-2" for="mod_direccion">Dirección:</label>
                              <div class="col-sm-8">
                                <input type="text" class="form-control redondeado confondo" id="mod_direccion" placeholder="Digite la dirección" name="mod_direccion" required>
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="control-label col-sm-2" for="mod_telefono">Teléfono:</label>
                              <div class="col-sm-8">
                                <input type="text" class="form-control redondeado confondo" id="mod_telefono" placeholder="Digite el teléfono" name="mod_telefono" required>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-sm-2" for="mod_cuenta">Cuenta Bancaria:</label>
                              <div class="col-sm-8">
                                <input type="text" class="form-control redondeado confondo" id="mod_cuenta" placeholder="Digite la cuenta bancaria" name="mod_cuenta" required>
                              </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="mod_base">Salario Base:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" id="mod_base" placeholder="Digite el salario base" name="mod_base" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="mod_cuenta">Reajuste:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" id="mod_reajuste" placeholder="Digite el reajuste" name="mod_reajuste" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="mod_comision">Monto x comision:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" id="mod_comision" placeholder="Digite el monto por comisión" name="mod_comision" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="mod_mandado">Monto x mandado:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" id="mod_mandado" placeholder="Digite el monto por mandado" name="mod_mandado" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="mod_ingreso">Fecha ingreso:</label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control redondeado confondo" id="mod_ingreso" name="mod_ingreso" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="mod_comentario">Comentario:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" id="mod_comentario" placeholder="Digite el comentario" name="mod_comentario" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="mod_estado">Estado:</label>
                                <div class="col-sm-8">
                                    <select class="form-control redondeado confondo" id="mod_estado" name="mod_estado">
                                        <optgroup >
                                            <option value="i">Inactivo</option>
                                            <option value="a">Activo</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!--=====================================
                PIE DEL MODAL EMPLEADOS
                ======================================-->

                <div class="modal-footer">

                  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                  <button  onclick="Inc_Empleados()" type="button" class="btn btn-danger pull-right" >Agregar</button>
                  <button  onclick="Act_Empleados()" type="button" class="btn btn-success pull-right" >Modificar</button>

                </div>

            </div>
        </div>
    </div>
</div>
<!--=====================================
MODAL MODIFICAR EMPLEADOS
======================================-->
<div id="modalModEmpleados" class="modal fade" tabindex="-1" role="dialog">
    <div style="width: 655px" class="modal-dialog" >
        <div class="modal-content">
            <!--=====================================
            CABEZA DEL MODAL MODIFICAR EMPLEADOS
            ======================================-->
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modificar Empleado en Planilla</h4>
            </div>
            <!--=====================================
            CUERPO DEL MODAL MODIFICAR EMPLEADOS
            ======================================-->

            <div  class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="box-body">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="mod_empleado">Empleado:</label>
                                <div class="col-sm-8">
                                    <label class="control-label " id="Planilla_numero"></label>
                                    <label class="control-label " id="Planilla_fecha"></label>
                                    <label class="control-label " id="idPlanilla"></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" >Nombre:</label>
                                <div class="col-sm-8">
                                    <label class="control-label " id="nombrePlanilla"></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">Extras:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" id="mod_extras" placeholder="Digite la cantidad de extras">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" >Comisión:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" id="mod_comisionpla" placeholder="Digite la comision">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-2" >Mandados:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" id="mod_mandados" placeholder="Digite los mandados">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" >Otros Ingresos:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" id="mod_oingresos" placeholder="Digite otros ingresos">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" >Vales:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" id="mod_vales" placeholder="Digite vales">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" >Compras:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" id="mod_compras" placeholder="Digite compras">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" >Otros Egresos:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" id="mod_oegresos" placeholder="Digite otros egresos">
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <!--=====================================
                PIE DEL MODAL EMPLEADOS
                ======================================-->

                <div class="modal-footer">

                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    <button  onclick="Mod_Empleados()" type="button" class="btn btn-success pull-right" >Modificar</button>

                </div>

            </div>
        </div>
    </div>
</div>
<!--=====================================
GENERAR PLANILLA EMPLEADOS
======================================-->
<div id="modalGenerar" class="modal fade" tabindex="-1" role="dialog">
    <div style="width: 655px" class="modal-dialog" >
        <div class="modal-content">
            <!--=====================================
            CABEZA DEL MODAL MODIFICAR EMPLEADOS
            ======================================-->
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Generar Planilla</h4>
            </div>
            <!--=====================================
            CUERPO DEL MODAL GENERAR PLANILLA
            ======================================-->

            <div  style="max-height:300px" class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="box-body">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-2" >Numero Planilla:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" id="nueva_planilla" placeholder="Digite la identificacion de la planilla">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" >Fecha Planilla:</label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control redondeado confondo" id="nueva_fecha">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!--=====================================
                PIE DEL MODAL GENERAR
                ======================================-->

                <div class="modal-footer">

                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    <button  onclick="GenerarPlanilla()" type="button" class="btn btn-success pull-right" >Generar</button>

                </div>

            </div>
        </div>
    </div>
</div>
<!--=====================================
Agregar Vacaciones
======================================-->
<div id="modalADDVacaciones" class="modal fade" tabindex="-1" role="dialog">
    <div style="width: 655px" class="modal-dialog" >
        <div class="modal-content">
            <!--=====================================
            CABEZA DEL MODAL Agregar Vacaciones
            ======================================-->
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Agregar Vacaciones</h4>
            </div>
            <!--=====================================
            CUERPO DEL MODAL Agregar Vacaciones
            ======================================-->

            <div  style="max-height:320px" class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="box-body">
                        <form class="form-horizontal">
                            <div class="form-group">

                                <label class="control-label col-sm-2" >Empleados:</label>
                                <div class="col-sm-8">
                                    <select class="form-control redondeado confondo" id="vaca_empleados"  onChange="habilitar(this.form)">
                                        <optgroup >
                                            <?php
                                            $sql = "SELECT * FROM rh_empleados where estado ='a'";
                                            echo '<option value="todos">Todos los empleados</option>';
                                            $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                                            foreach ($ejecutar as $key => $row) {
                                                echo '<option value="'.$row["id_empleado"].'">'.$row["nombre"].' '.$row["apellido"].'</option>';
                                            }
                                            ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                                <div class="form-group">
                                <label class="control-label col-sm-2" >Cantidad:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control redondeado confondo" id="nueva_Vcantidad" placeholder="Digite la cantidad de vacaciones">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" >Fecha:</label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control redondeado confondo" id="nueva_Vfecha">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!--=====================================
                PIE DEL MODAL GENERAR
                ======================================-->

                <div class="modal-footer">

                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    <button  onclick="GrabarVacaciones()" type="button" class="btn btn-success pull-right" >Agregar</button>

                </div>

            </div>
        </div>
    </div>
</div>
<!--=====================================
Aguinaldos
======================================-->
<div id="modalAguinaldos" class="modal fade" tabindex="-1" role="dialog">
    <div style="width: 655px" class="modal-dialog" >
        <div class="modal-content">
            <!--=====================================
            CABEZA DEL MODAL Aguinaldos
            ======================================-->
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Aguinaldos</h4>
            </div>
            <!--=====================================
            CUERPO DEL MODAL Aguinaldos
            ======================================-->

            <div  style="max-height:330px;" class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="box-body">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-2" >Año:</label>
                                <div class="col-sm-8">
                                    <select class="form-control redondeado confondo" id="year_aguinaldos">
                                    <optgroup >
                                        <?php

                                        $sql = "SELECT DISTINCT YEAR(fecha) as ano FROM rh_planilla order by ano asc";
                                        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                                        foreach ($ejecutar as $key => $value) {
                                            $ano=$value['ano'];
                                            echo '<option value="'.$ano.'">'.$ano.'</option>';
                                        }
                                        ?>
                                    </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" >Empleados:</label>
                                <div class="col-sm-8">
                                    <select class="form-control redondeado confondo" id="empleados_aguinaldos"  onChange="habilitar(this.form)">
                                        <optgroup >
                                            <?php
                                            $sql = "SELECT * FROM rh_empleados where estado ='a'";
                                            echo '<option value="todos">Todos los empleados</option>';
                                            $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);
                                            foreach ($ejecutar as $key => $row) {
                                                echo '<option value="'.$row["id_empleado"].'">'.$row["nombre"].' '.$row["apellido"].'</option>';
                                            }
                                            ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div style="display:none" class="form-group">
                                <label class="control-label col-sm-2" >Tipo:</label>
                                <div class="col-sm-8">
                                    <select class="form-control redondeado confondo" id="tipo_aguinaldos"  onChange="habilitar(this.form)">
                                        <optgroup >
                                            <option value="parcial">Parcial</option>
                                            <option value="total">Total</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!--=====================================
                PIE DEL MODAL Aguinaldos
                ======================================-->

                <div class="modal-footer">

                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    <button  onclick="GenerarAguinaldo()" type="button" class="btn btn-success pull-right" >Generar</button>

                </div>

            </div>
        </div>
    </div>
</div>
<!--=====================================
VACACIONES EMPLEADOS
======================================-->
<div id="modalVacaciones" class="modal fade" tabindex="-1" role="dialog">
    <div style="width: 655px" class="modal-dialog" >
        <div class="modal-content">
            <!--=====================================
            CABEZA DEL MODAL VACACIONES
            ======================================-->
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Vacaciones</h4>
            </div>
            <!--=====================================
            CUERPO DEL MODAL VACACIONES
            ======================================-->

            <div  class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="box-body">
                       <div id="listado_vacaciones">

                       </div>
                    </div>
                </div>

                <!--=====================================
                PIE DEL MODAL VACACIONES
                ======================================-->

                <div class="modal-footer">

                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    <button  onclick="AgregarVacaciones()" type="button" class="btn btn-success pull-right" >Agregar Vacaciones</button>

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
        Cargar();
    });

    /*=============================================
     FIN DOCUMENT READY
    =============================================*/

    /*=============================================
    CARGA PLANILLAS
    =============================================*/
    function Cargar() {

        $('#planilla_mostrar').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $.ajax('vistas/modulos/planilla/planillas_cargar.php')
            .done(function(response){
                //console.log(response);
                $('#planilla_mostrar').html(response);
            });

    }
    /*=============================================
    MODAL AGREGAR PLANILLA
    =============================================*/
    function AgregarPlanilla(){
        $("#modalGenerar").modal();
    }
    /*=============================================
    MODAL AGUINALDO
    =============================================*/
    function MAguinaldo(){
        $("#modalAguinaldos").modal();
    }
    function GenerarAguinaldo(){
        var year = $("#year_aguinaldos").val();
        var tipo = $("#tipo_aguinaldos").val();
        var empleado = $("#empleados_aguinaldos").val();
        window.open("extensiones/tcpdf/pdf/aguinaldos.php?year=" + year + "&tipo=" + tipo + "&empleado=" + empleado , "_blank");
    }
    /*=============================================
    MODIFICAR EMPLEADOS
    =============================================*/
    function GenerarPlanilla(){
        var planilla=$("#nueva_planilla").val();
        var fecha=$("#nueva_fecha").val();
        $("#modalGenerar").modal('hide');

        var param = {
            planilla:planilla,
            fecha:fecha
        };

        $.ajax({
            data: param,
            url: "vistas/modulos/planilla/planilla_generar.php",
            method: "post",
            success: function(data) {
                console.log(data);
                if (data=="exito") {
                    swal({
                        type: "success",
                        title: "la planilla ha sido creado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        window.location.replace('planilla');
                    });
                }
            }
        });

    }
    /*=============================================
    LLAMA MODAL EMPLEADOS
    =============================================*/
    function Empleados(){
        $("#mod_nombre").val("");
        $("#mod_apellido").val("");
        $("#mod_cedula").val("");
        $("#mod_direccion").val("");
        $("#mod_telefono").val("");
        $("#mod_cuenta").val("");
        $("#mod_base").val("");
        $("#mod_reajuste").val("");
        $("#mod_comision").val("");
        $("#mod_mandado").val("");
        $("#mod_ingreso").val("");
        $("#mod_comentario").val("");
        $("#mod_estado").val("");
        var dropDown = document.getElementById("mod_empleado");
        dropDown.selectedIndex = 0;
        $("#modalEmpleados").modal();
    }
    /*=============================================
    LLAMA MODAL VACACIONES
    =============================================*/
    function Vacaciones(){
        $.ajax('vistas/modulos/planilla/planillas_vacaciones.php')
            .done(function (response) {
                $('#listado_vacaciones').html(response);
                $("#modalVacaciones").modal();
            })

    }
    /*=============================================
    LLAMA MODAL  AGREGAR VACACIONES
    =============================================*/
    function AgregarVacaciones(){
                $("#modalVacaciones").modal('hide');
                $("#modalADDVacaciones").modal();

    }


    /*=============================================
    BUSCAR EMPLEADOS
    =============================================*/
    function BuscarEmpleado(){
        var empleado = $("#mod_empleado").val();
        $.ajax('vistas/modulos/planilla/planillas_buscar.php?id='+empleado)
            .done(function(response){
                var resultadoajax = JSON.parse(response)
                var nombre = utf8Decode(resultadoajax[0]["nombre"].trim())
                $("#mod_nombre").val(nombre);
                var apellido = utf8Decode(resultadoajax[0]["apellido"].trim())
                $("#mod_apellido").val(apellido);
                $("#mod_cedula").val(resultadoajax[0]["cedula"].trim());
                $("#mod_direccion").val(resultadoajax[0]["direccion"].trim());
                $("#mod_telefono").val(resultadoajax[0]["telefono"].trim());
                $("#mod_cuenta").val(resultadoajax[0]["cuenta"].trim());
                $("#mod_base").val(resultadoajax[0]["base"].trim());
                $("#mod_reajuste").val(resultadoajax[0]["reajuste"].trim());
                $("#mod_comision").val(resultadoajax[0]["comision"].trim());
                $("#mod_mandado").val(resultadoajax[0]["mandado"].trim());
                var dateString = moment(resultadoajax[0]["ingreso"]).format('YYYY-MM-DD');
                $("#mod_ingreso").val(dateString);
                $("#mod_comentario").val(resultadoajax[0]["comentario"].trim());
                $("#mod_estado").val(resultadoajax[0]["estado"].trim());
            });
    }

    /*=============================================
    MODIFICAR EMPLEADOS
    =============================================*/
    function Act_Empleados(){
        var nombre=$("#mod_nombre").val();
        var apellido=$("#mod_apellido").val();
        var cedula=$("#mod_cedula").val();
        var direccion=$("#mod_direccion").val();
        var telefono=$("#mod_telefono").val();
        var cuenta=$("#mod_cuenta").val();
        var base=$("#mod_base").val();
        var reajuste=$("#mod_reajuste").val();
        var comision=$("#mod_comision").val();
        var mandado=$("#mod_mandado").val();
        var ingreso=$("#mod_ingreso").val();
        var comentario=$("#mod_comentario").val();
        var estado=$("#mod_estado").val();
        var empleado = $("#mod_empleado").val();
        $("#modalEmpleados").modal('hide');

        var param = {
            empleado: empleado,
            nombre:nombre,
            apellido:apellido,
            cedula:cedula,
            direccion:direccion,
            telefono:telefono,
            cuenta:cuenta,
            base:base,
            reajuste:reajuste,
            comision:comision,
            mandado:mandado,
            ingreso:ingreso,
            comentario:comentario,
            estado:estado
        };

        $.ajax({
            data: param,
            url: "vistas/modulos/planilla/planilla_modificar.php",
            method: "post",
            success: function(data) {
                //console.log(data);
                if (data=="exito") {
                    swal({
                        type: "success",
                        title: "El empleado ha sido actualizado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                    });
                }
            }
        });

    }
    /*=============================================
        MODIFICAR EMPLEADOS
        =============================================*/
    function Inc_Empleados(){
        var nombre=$("#mod_nombre").val();
        var apellido=$("#mod_apellido").val();
        var cedula=$("#mod_cedula").val();
        var direccion=$("#mod_direccion").val();
        var telefono=$("#mod_telefono").val();
        var cuenta=$("#mod_cuenta").val();
        var base=$("#mod_base").val();
        var reajuste=$("#mod_reajuste").val();
        var comision=$("#mod_comision").val();
        var mandado=$("#mod_mandado").val();
        var ingreso=$("#mod_ingreso").val();
        var comentario=$("#mod_comentario").val();
        var estado=$("#mod_estado").val();

        $("#modalEmpleados").modal('hide');

        var param = {

            nombre:nombre,
            apellido:apellido,
            cedula:cedula,
            direccion:direccion,
            telefono:telefono,
            cuenta:cuenta,
            base:base,
            reajuste:reajuste,
            comision:comision,
            mandado:mandado,
            ingreso:ingreso,
            comentario:comentario,
            estado:estado
        };

        $.ajax({
            data: param,
            url: "vistas/modulos/planilla/planilla_agregar.php",
            method: "post",
            success: function(data) {
                //console.log(data);
                if (data=="exito") {
                    swal({
                        type: "success",
                        title: "El empleado ha sido actualizado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                    });
                }
            }
        });
    }
    /*=============================================
    CARGAR PLANILLA SELECCIONADA
    =============================================*/
    function myCarga(planilla,fecha,total) {
        var param = {
            planilla:planilla,
            fecha:fecha
        };

        $('#listado_planillas').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');

        $.ajax({
            data: param,
            url: "vistas/modulos/planilla/planilla_mostrar.php",
            method: "post",
            success: function(data) {
                //console.log(data);
                $('#listado_planillas').html(data);
            }
        });
    }
        /*=============================================
       REPORTE PLANILLA SELECCIONADA
       =============================================*/
        function myReporte(planilla,fecha,total) {
            swal({
                title: '¿Está seguro de generar el reporte de la planilla?',
                text: "¡Si no lo está puede cancelar la acción!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, generar!'
            }).then(function(result){
                if (result.value) {
                    window.open("extensiones/tcpdf/pdf/planilla.php?planilla=" + planilla + "&fecha=" + fecha , "_blank");
                }
            });
        }
    /*=============================================
    BORRAR PLANILLA SELECCIONADA
    =============================================*/
    function myBorrar(planilla,fecha,total) {
        swal({
            title: '¿Está seguro de borrar la planilla?',
            text: "¡Si no lo está puede cancelar la acción!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, borrar!'
        }).then(function(result){
            if (result.value) {
                var param = {
                    planilla:planilla,
                    fecha:fecha
                };
                $.ajax({
                    data: param,
                    url: "vistas/modulos/planilla/planilla_borrar.php",
                    method: "post",
                    success: function(data) {
                        console.log(data);
                        if (data=="exito") {
                            swal({
                                type: "success",
                                title: "La planilla completa ha sido eliminada correctamente",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                            }).then(function(result){
                                window.location.replace('planilla');
                            });
                        }
                    }
                });
            }
        });
    }
    /*=============================================
    BORRAR VACACIONES SELECCIONADA
    =============================================*/
    function EleVacaciones(fecha,empleado,cantidad) {
        swal({
            title: '¿Está seguro de borrar esta vacacion?',
            text: "¡Si no lo está puede cancelar la acción!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, borrar!'
        }).then(function(result){
            if (result.value) {
                var param = {
                    id_empleado:empleado,
                    fecha:fecha
                };
                $.ajax({
                    data: param,
                    url: "vistas/modulos/planilla/planilla_vacaciones_eliminar.php",
                    method: "post",
                    success: function(data) {
                        console.log(data);
                        if (data=="exito") {
                            swal({
                                type: "success",
                                title: "La vacacion ha sido eliminada correctamente",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                            }).then(function(result){
                                window.location.replace('planilla');
                            });
                        }
                    }
                });
            }
        });
    }
    /*=============================================
   AGREGAR VACACIONES SELECCIONADA
    =============================================*/
    function AddVacaciones(empleado,nombre,total) {
        $("#modalVacaciones").modal('hide');
        $("#nombre_vacaciones").text(nombre);
        $("#id_vacaciones").text(empleado);
        $("#modalADDVacaciones").modal();
    }
    /*=============================================
    GRABAR VACACIONES
    =============================================*/
    function GrabarVacaciones(){

        var id_empleado=$("#vaca_empleados").val();
        var fecha=$("#nueva_Vfecha").val();
        var cantidad=$("#nueva_Vcantidad").val();
        $("#modalADDVacaciones").modal('hide');

        var param = {
            id_empleado:id_empleado,
            fecha:fecha,
            cantidad:cantidad
        };
        //console.log(param);
        $.ajax({
            data: param,
            url: "vistas/modulos/planilla/planilla_vacaciones_agregar.php",
            method: "post",
            success: function(data) {
                console.log(data);
                if (data=="exito") {
                    swal({
                        type: "success",
                        title: "las vacaciones han sido creado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        window.location.replace('planilla');
                    });
                }
            }
        });
    }
    /*=============================================
    MODIFICAR EMPLEADO
    =============================================*/

    function myModificar(planilla,fecha,empleado) {
        var param = {
            id:empleado,
            planilla:planilla,
            fecha:fecha
        };


        $.ajax({
            data: param,
            url: "vistas/modulos/planilla/planillas_modificar.php",
            method: "post",
            success: function(data) {
                //console.log(data);
                var resultadoajax = JSON.parse(data)
                var extras =resultadoajax[0]["extras"];
                var hextras=resultadoajax[0]["salxhora"];
                var textras = extras/hextras/1.5;
                // Recalcula la comision para lo que es CCSS / Planilla_cambiar.php variable.
                var comision =resultadoajax[0]["comision"] / 1.1175;
                comision = comision.toFixed(2);
                $("#mod_extras").val(textras);
                $("#mod_comisionpla").val(comision);
                $("#mod_mandados").val(resultadoajax[0]["mandados"].trim());
                $("#mod_oingresos").val(resultadoajax[0]["otros_ingresos"].trim());
                $("#mod_vales").val(resultadoajax[0]["vales"].trim());
                $("#mod_compras").val(resultadoajax[0]["compras"].trim());
                $("#mod_oegresos").val(resultadoajax[0]["otros_egresos"].trim());
                $("#Planilla_numero").text(resultadoajax[0]["id_planilla"].trim());
                $("#Planilla_fecha").text(resultadoajax[0]["fecha"].trim());
                $("#idPlanilla").text(resultadoajax[0]["id_empleado"].trim());
                var nombre = resultadoajax[0]["nombre"].trim()+" "+resultadoajax[0]["apellido"].trim();
                $("#nombrePlanilla").text(nombre);
                $("#modalModEmpleados").modal();
            }
        });

    }
    /*=============================================
    RETENCION DEL SALARIO
    =============================================*/
    function myRetencion(planilla,fecha,empleado) {
        var param = {
            id_empleado:empleado,
            id_planilla:planilla,
            fecha:fecha
        };
        $.ajax({
            data: param,
            url: "vistas/modulos/planilla/planilla_retencion.php",
            method: "post",
            success: function(data) {
                //console.log(data);
                if (data=="exito") {

                    swal({
                        type: "success",
                        title: "El empleado ha sido actualizado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        Cargar();
                        myCarga(planilla,fecha,100);
                    });
                }
            }
        });
    }
    /*=============================================
    MODIFICAR EMPLEADOS
    =============================================*/
    function Mod_Empleados(){
        var id_planilla = $("#Planilla_numero").text();
        var fecha = $("#Planilla_fecha").text();
        var id_empleado = $("#idPlanilla").text()
        var extras=$("#mod_extras").val();
        var comision=$("#mod_comisionpla").val();
        var mandados=$("#mod_mandados").val();
        var oingresos=$("#mod_oingresos").val();
        var vales=$("#mod_vales").val();
        var compras=$("#mod_compras").val();
        var oegresos=$("#mod_oegresos").val();
        $("#modalModEmpleados").modal('hide');

        var param = {
            extras: extras,
            comision:comision,
            mandados:mandados,
            oingresos:oingresos,
            vales:vales,
            compras:compras,
            oegresos:oegresos,
            id_empleado:id_empleado,
            id_planilla:id_planilla,
            fecha:fecha
        };

        $.ajax({
            data: param,
            url: "vistas/modulos/planilla/planilla_cambiar.php",
            method: "post",
            success: function(data) {
                //console.log(data);
                if (data=="exito") {

                    swal({
                        type: "success",
                        title: "El empleado ha sido actualizado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        //window.location.replace('planilla');
                        var id_planilla = $("#Planilla_numero").text();
                        var fecha = $("#Planilla_fecha").text();
                        Cargar();
                        myCarga(id_planilla,fecha,100);
                    });
                }
            }
        });
    }

 // *******************************************************************
    /*=============================================
    CODIFICACION UTF8
    =============================================*/
    function utf8Encode(unicodeString) {
        if (typeof unicodeString != 'string') throw new TypeError('parameter ‘unicodeString’ is not a string');
        const utf8String = unicodeString.replace(
            /[\u0080-\u07ff]/g,  // U+0080 - U+07FF => 2 bytes 110yyyyy, 10zzzzzz
            function(c) {
                var cc = c.charCodeAt(0);
                return String.fromCharCode(0xc0 | cc>>6, 0x80 | cc&0x3f); }
        ).replace(
            /[\u0800-\uffff]/g,  // U+0800 - U+FFFF => 3 bytes 1110xxxx, 10yyyyyy, 10zzzzzz
            function(c) {
                var cc = c.charCodeAt(0);
                return String.fromCharCode(0xe0 | cc>>12, 0x80 | cc>>6&0x3F, 0x80 | cc&0x3f); }
        );
        return utf8String;
    }

    function utf8Decode(utf8String) {
        if (typeof utf8String != 'string') throw new TypeError('parameter ‘utf8String’ is not a string');
        // note: decode 3-byte chars first as decoded 2-byte strings could appear to be 3-byte char!
        const unicodeString = utf8String.replace(
            /[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g,  // 3-byte chars
            function(c) {  // (note parentheses for precedence)
                var cc = ((c.charCodeAt(0)&0x0f)<<12) | ((c.charCodeAt(1)&0x3f)<<6) | ( c.charCodeAt(2)&0x3f);
                return String.fromCharCode(cc); }
        ).replace(
            /[\u00c0-\u00df][\u0080-\u00bf]/g,                 // 2-byte chars
            function(c) {  // (note parentheses for precedence)
                var cc = (c.charCodeAt(0)&0x1f)<<6 | c.charCodeAt(1)&0x3f;
                return String.fromCharCode(cc); }
        );
        return unicodeString;
    }

</script>

 <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Personal';
</script>
