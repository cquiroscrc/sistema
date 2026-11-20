<?php
session_start();
include "menu.php";
?>
  <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro Empleados</title>
        <link href='http://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/main.css">
        <link rel="icon" href="favicon.ico">
    </head>
    <body>
      

              <?php
                
               include("db.php");

                if((isset($_POST['empleado']))&&(!empty($_POST['empleado']))){
                    $id_empleado =$_POST['empleado'];
                  $sql = "SELECT * FROM rh_empleados WHERE id_empleado='".$id_empleado."'";
                
                    if((isset($_REQUEST['fecha']))and(!empty($_REQUEST['fecha']))){
                        $fecha = $_REQUEST['fecha'];
                    }

                    if((isset($_REQUEST['planilla']))and(!empty($_REQUEST['planilla']))){
                        $planilla = $_REQUEST['planilla'];
                    }

                    if(($result = odbc_exec($id,$sql)) === false)
                        die("Error en Select: ". odbc_errormsg($id));
                    
                    
                    while($row = odbc_fetch_array($result)){
                    
                  //header("location: resultado_producto.php?msg=Eliminado");
                  
                  
                        $id_empleado = $row['id_empleado'];
                        $nombre = $row['nombre'];
                        $apellido = $row['apellido'];
                        $cedula = $row['cedula'];
                        $direccion = $row['direccion'];
                        $telefono = $row['telefono'];
                        $cuenta = $row['cuenta'];
                        $base = $row['base'];
                        $base= $base/2;
                        $reajuste = $row['reajuste'];
                        $reajuste=$reajuste/2;
                        $ccss=$base*0.1050;  //nueva ley 0.1050  Enero 2019  *****
                        $salxhora = $row['salxhora'];
                        $mandado = $row['mandado'];
                        $comision = $row['comision'];
                        $ingreso = $row['ingreso'];
                        $comentario = $row['comentario'];
                        $estado = $row['estado'];
                        $foto = $row['foto'];
                        echo 'Fecha '.$fecha;
                        echo ' Planilla '.$planilla;
                        echo '<form action="planilla_incluir_planilla.php" method="post" enctype="multipart/form-data">';
                        echo '<h1>Modificacion de Empleados</h1>';
                        echo '<fieldset>';
                        echo '<legend><span class="number">:)</span>Informacion del Empleado</legend>';
                        echo '<input type="hidden" id="fecha" name="fecha" value="'.$fecha.'">';
                        echo '<input type="hidden" id="planilla" name="planilla" value="'.$planilla.'">';
                        echo '<label for="name">Nombre:</label>';
                        echo '<input type="text" id="nombre" name="nombre" value="'.$nombre.' '.$apellido.'" required>';
                        
                        //echo '<label for="name">Salario por quincena:</label>';
                        echo '<input type="hidden" id="base" name="base" value="'.$base.'" required>';
                        
                        //echo '<label for="name">CCSS:</label>';
                        echo '<input type="hidden" id="ccss" name="ccss" value="'.$ccss.'" required>';

                        //echo '<label for="name">Reajuste por quincena:</label>';
                        echo '<input type="hidden" id="reajuste" name="reajuste" value="'.$reajuste.'" required>';

                        echo '<label for="name">Salario x Hora:</label>';
                        echo '<input type="text" id="salxhora" name="salxhora" value="'.$salxhora.'" required>';

                        echo '<label for="name">Extras:</label>';
                        echo '<input type="text" id="extras" name="extras" value=0 required>';

                        echo '<label for="name">Vales:</label>';
                        echo '<input type="text" id="vales" name="vales" value=0 required>';

                        echo '<label for="name">Compras:</label>';
                        echo '<input type="text" id="compras" name="compras" value="0" required>';

                        echo '<label for="name">Otros Egresos:</label>';
                        echo '<input type="text" id="otros_egresos" name="otros_egresos" value="0" required>';

                        echo '<label for="name">Comision:</label>';
                        echo '<input type="text" id="comision" name="comision" value="0" required>';

                        echo '<label for="name">Mandados:</label>';
                        echo '<input type="text" id="mandado" name="mandado" value="0" required>';

                        
                        echo '<label for="name">Otros Ingresos:</label>';
                        echo '<input type="text" id="otros_ingresos" name="otros_ingresos" value="0" required>';

                        echo '<input type="hidden" id="idempl" name="idempl" value="'.$id_empleado.'">';
                        //echo '<label for="name">Valor de Mandados:</label>';
                        echo '<input type="hidden" id="valor_mandado" name="valor_mandado" value="'.$mandado.'">';

                        //echo '<label for="name">Valor de Comision:</label>';
                        echo '<input type="hidden" id="valor_comision" name="valor_comision" value="'.$comision.'">';
                        
                        
                        echo '</fieldset>';
                        echo '<button type="submit">Ingresar</button>';
                        echo '</form>';
                    }
                }                  

              ?>
    <script src="js/bootstrap.min.js"></script> 
    </body>
</html>