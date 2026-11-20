<?php
$sql_utiles="SELECT * FROM tbl_Utiles";
$res_utiles=ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_utiles);


if($_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "bodega" || $_SESSION["perfil"] == "mensajeria"){

  echo '<script>

    window.location = "inicio";

  </script>';

  return;

}

foreach ($res_utiles as $key => $value) {
    if ($value["IDUtilidad"]=='1') {
        $meses=$value["Valor"];
    }
    if ($value["IDUtilidad"]=='2') {
        $descuento=$value["Valor"];
    }
    if ($value["IDUtilidad"]=='3') {
        $nombre=$value["Valor"];
    }
    if ($value["IDUtilidad"]=='4') {
        $IVA=$value["Valor"];
    }
    if ($value["IDUtilidad"]=='7') {
        $agregado=$value["Valor"];
    }
    if ($value["IDUtilidad"]=='8') {
        $planilla=$value["Valor"];
    }
    if ($value["IDUtilidad"]=='9') {
        $ubicacion=$value["Valor"];
    }
    if ($value["IDUtilidad"]=='10') {
        $cedula=$value["Valor"];
    }
    if ($value["IDUtilidad"]=='11') {
        $mensaje2=$value["Valor"];
        $mensaje=$value["Descripcion"];
    }
    if ($value["IDUtilidad"]=='12') {
        $ccss=$value["Valor"];
    }
    if ($value["IDUtilidad"]=='13') {
        $factor=$value["Valor"];
    }
    if ($value["IDUtilidad"]=='14') {
        $retencion1=$value["Valor"];
    }
    if ($value["IDUtilidad"]=='15') {
        $retencion2=$value["Valor"];
    }
    if ($value["IDUtilidad"]=='16') {
        $retencion3=$value["Valor"];
    }
    if ($value["IDUtilidad"]=='17') {
        $conyuge=$value["Valor"];
    }
    if ($value["IDUtilidad"]=='18') {
        $hijo=$value["Valor"];
    }
}

$sql_consecutivos="SELECT * FROM Consecutivos";
$res_consecutivos=ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_consecutivos);
//var_dump($res_consecutivos);
foreach ($res_consecutivos as $key => $value) {


    if ($value["tipo"]=="01        ") {
        $Nfactura=$value["numero"];
        $estado=$value["Estado"];
    }
    if ($value["tipo"]=="04        ") {
        $recibos=$value["numero"];
    }
    if ($value["tipo"]=="05        ") {
        $ajuste=$value["numero"];
    }
    if ($value["tipo"]=="06        ") {
        $devoluciones=$value["numero"];
    }
    if ($value["tipo"]=="03        ") {
        $proforma=$value["numero"];
    }
    if ($value["tipo"]=="07        ") {
        $clientes=$value["numero"];
    }
    if ($value["tipo"]=="09        ") {
        $ordenes=$value["numero"];
    }
}

?>

<!--=====================================
UTILIDADES
======================================-->

<div class="box box-warning">
	
	<div class="box-header with-border">
        <center>
        <h3>Utilidades de Sistema</h3>
        </center>
    </div>

	<br class="box-body">

<!--    Mantenimiento-->

    <div class="row">
        <div class="col-md-4">
            <div>
                <center>
                    <h4>  Utilidades</h4>
                </center>
                <br>
            </div>
            <form class="form-signin" action="vistas/modulos/utilidades_aplicar.php" method="POST">
                <center>
                    <label for="mes">Compañia: </label>
                    <input type=text" id="nombre" name="nombre" required value="<?php echo $nombre ?>">
                    <br><br>
                    <label for="ano">Cedula: </label>
                    <input type="text" id="cedula" name="cedula" required value="<?php echo $cedula ?>">
                    <br><br>
                    <label for="meta">Impuesto IVA: </label>
                    <input type="text" id="iva" name="iva" required value="<?php echo $IVA ?>">
                    <br><br>
                    <label for="meta">Porcentaje CCSS: </label>
                    <input type="text" id="ccss" name="ccss" required value="<?php echo $ccss ?>">
                    <br><br>
                    <label for="meta">Agregado de precio: </label>
                    <input type="text" id="agregado" name="agregado" required value="<?php echo $agregado ?>">
                    <br><br>
                    <label for="meta">Consecutivo Planilla: </label>
                    <input type="text" id="planilla" name="planilla" required value="<?php echo $planilla ?>">
                    <br><br>
                    <label for="meta">Meses analizar: </label>
                    <input type="text" id="meses" name="meses" required value="<?php echo $meses ?>">
                    <br><br>
                    <label for="meta">Factor precio: </label>
                    <input type="text" id="factor" name="factor" required value="<?php echo $factor ?>">

                    <br><br>
                    <button class="btn-primary" type="submit">Actualizar</button>
                </center>
            </form>

        </div>
        <div class="col-md-4">
            <div>
                <center>
                    <h4>  Retenciones</h4>
                </center>
                <br>
            </div>
            <form class="form-signin" action="vistas/modulos/retenciones_aplicar.php" method="POST">
                <center>

                    <label for="meta">Retencion 1: </label>
                    <input type="text" id="retencion1" name="retencion1" required value="<?php echo $retencion1 ?>">
                    <br><br>
                    <label for="meta">Retencion 2: </label>
                    <input type="text" id="retencion2" name="retencion2" required value="<?php echo $retencion2 ?>">
                    <br><br>
                    <label for="meta">Retencion 3: </label>
                    <input type="text" id="retencion3" name="retencion3" required value="<?php echo $retencion3 ?>">
                    <br><br>
                    <label for="meta">Conyugue: </label>
                    <input type="text" id="conyugue" name="conyugue" required value="<?php echo $conyuge ?>">
                    <br><br>
                    <label for="meta">Hijo: </label>
                    <input type="text" id="hijo" name="hijo" required value="<?php echo $hijo ?>">
                    <br><br>
                    <label for="meta">Aviso: </label>
                    <input type="text" id="aviso" name="aviso" required value="<?php echo $mensaje ?>">
                    <br><br>
                    <label for="meta">Aviso 2: </label>
                    <input type="text" id="aviso2" name="aviso2" required value="<?php echo $mensaje2 ?>">
                    <br><br>
                    <label for="meta">Descuento mayor: (1=Si / 2=No ) </label>
                    <input type="text" id="descuento" name="descuento" required value="<?php echo $descuento ?>">
                    <br><br>
                    <button class="btn-primary" type="submit">Actualizar</button>
                </center>
            </form>

        </div>
        <div class="col-md-4">
            <div>
                <center>
                    <h4>  Consecutivos</h4>
                </center>
                <br>
            </div>
            <form class="form-signin" action="vistas/modulos/consecutivos_aplicar.php" method="POST">
                <center>
                    <label for="mes">Factura Siguiente: </label>
                    <input type=text" id="factura" name="factura" required value="<?php echo $Nfactura ?>">
                    <br><br>
                    <label for="mes">Estado Factura (L/O) : </label>
                    <input type=text" id="estado" name="estado" required value="<?php echo $estado ?>">
                    <br><br>
                    <label for="ano">Recibos: </label>
                    <input type="text" id="recibos" name="recibos" required value="<?php echo $recibos ?>">
                    <br><br>
                    <label for="meta">Ajustes: </label>
                    <input type="text" id="ajustes" name="ajustes" required value="<?php echo $ajuste ?>">
                    <br><br>
                    <label for="meta">Devoluciones: </label>
                    <input type="text" id="devoluciones" name="devoluciones" required value="<?php echo $devoluciones ?>">
                    <br><br>
                    <label for="meta">Proformas: </label>
                    <input type="text" id="proformas" name="proformas" required value="<?php echo $proforma ?>">
                    <br><br>
                    <label for="meta">Clientes: </label>
                    <input type="text" id="clientes" name="clientes" required value="<?php echo $clientes ?>">
                    <br><br>
                    <label for="meta">Ordenes: </label>
                    <input type="text" id="ordenes" name="ordenes" required value="<?php echo $ordenes ?>">
                    <br><br>
                    <button class="btn-primary" type="submit">Actualizar</button>
                    <br><br>
                </center>
            </form>

        </div>
    </div>
    <hr>
    <div class="row">
        <center>
        <div class="col-md-6">
            <div>
                <center>
                    <h4>  Aumento precios</h4>
                </center>
                <br>
            </div>

            <form class="form-signin" action="vistas/modulos/aumento_aplicar.php" method="POST">
                <label for="aumenta">Monto % del incremento de Precio de Venta: </label>
                <input type=text" id="montoaumenta" name="montoaumenta" required value="">
                <br><br>
                <label for="aumenta">Redondear a que valor : </label>
                <input type=text" id="redondeaaumenta" name="redondeaaumenta" required value="">
                <br><br>
                <button class="btn-primary" type="submit">Actualizar</button>
                <br><br>
            </form>
        </div>
        <div class="col-md-6">
            <div>
                <center>
                    <h4>  Disminución precios</h4>
                </center>
                <br>
            </div>
            <form class="form-signin" action="vistas/modulos/disminucion_aplicar.php" method="POST">
                <label for="disminuye">Monto % de la disminución de Precio de Venta: </label>
                <input type=text" id="montodisminuye" name="montodisminuye" required value="">
                <br><br>
                <label for="mes">Redondear a que valor : </label>
                <input type=disminuye" id="redondeadisminuye" name="redondeadisminuye" required value="">
                <br><br>
                <button class="btn-primary" type="submit">Actualizar</button>
                <br><br>
            </form>
        </div>
        </center>
    </div> <!--Cambio de precios  -->


    <script type="text/javascript" language="JavaScript">
        document.title = 'Sistema / Utilidades';
    </script>