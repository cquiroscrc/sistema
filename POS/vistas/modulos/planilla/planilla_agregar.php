<?php
  require("../../../modelos/conexion.php");
  require("../../../controladores/utilitaria.controlador.php");
  require("../../../modelos/utilitaria.modelo.php");

  date_default_timezone_set("America/Costa_Rica");
  header("Content-type: text/html; charset=utf-8");

    $base=0;
    $reajuste=0;
    $mandado=0;
    $comision=0;

    if(isset($_REQUEST['nombre']) && !empty($_REQUEST['nombre'])){
        $nombre = $_REQUEST['nombre'];
    }
    if(isset($_REQUEST['apellido']) && !empty($_REQUEST['apellido'])){
          $apellido = $_REQUEST['apellido'];
    }
    if(isset($_REQUEST['cedula']) && !empty($_REQUEST['cedula'])){
        $cedula = $_REQUEST['cedula'];
    }
    if(isset($_REQUEST['direccion']) && !empty($_REQUEST['direccion'])){
        $direccion = $_REQUEST['direccion'];
    }
    if(isset($_REQUEST['telefono']) && !empty($_REQUEST['telefono'])){
        $telefono = $_REQUEST['telefono'];
    }
    if(isset($_REQUEST['cuenta']) && !empty($_REQUEST['cuenta'])){
        $cuenta = $_REQUEST['cuenta'];
    }
    if(isset($_REQUEST['base']) && !empty($_REQUEST['base'])){
        $base = $_REQUEST['base'];
    }
    if(isset($_REQUEST['reajuste']) && !empty($_REQUEST['reajuste'])){
        $reajuste = $_REQUEST['reajuste'];
    }
    if(isset($_REQUEST['comision']) && !empty($_REQUEST['comision'])){
        $comision = $_REQUEST['comision'];
    }
    if(isset($_REQUEST['mandado']) && !empty($_REQUEST['mandado'])){
        $mandado = $_REQUEST['mandado'];
    }
    if(isset($_REQUEST['ingreso']) && !empty($_REQUEST['ingreso'])){
        $ingreso = $_REQUEST['ingreso'];
    }
    if(isset($_REQUEST['comentario']) && !empty($_REQUEST['comentario'])){
        $comentario = $_REQUEST['comentario'];
    }
    if(isset($_REQUEST['estado']) && !empty($_REQUEST['estado'])){
        $estado = $_REQUEST['estado'];
    }
    $salxhora=($base+$reajuste)/196;
    $salxhora=round($salxhora, 2);

    $sql = "INSERT INTO rh_empleados (nombre,apellido,cedula,direccion,telefono,cuenta,base,reajuste,mandado,comision,ingreso,comentario,estado,salxhora)  values 
    ('$nombre','$apellido','$cedula','$direccion','$telefono','$cuenta','$base','$reajuste',$mandado,'$comision','$ingreso','$comentario','$estado','$salxhora')";

    $respuesta = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

  echo "exito";
