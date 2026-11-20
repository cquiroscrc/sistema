<?php
session_start();
include "menu.php";
?>
<html>
  <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Imprimir Planilla</title>
        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script> <!--  Imagenes fancy -->
        <link rel="stylesheet" href="css/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" /> <!--  Imagenes fancy -->
        <script type="text/javascript" src="lib/jquery.fancybox.pack.js?v=2.1.5"></script> <!--  Imagenes fancy -->

        <link href='http://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/main.css">
        <link rel="icon" href="favicon.ico">
    </head>
    <body>
       
        
            
<?php
    include("db.php");

    if((isset($_REQUEST['planilla']))and(!empty($_REQUEST['planilla']))){
        $planilla = $_REQUEST['planilla'];
    }
    
    if((isset($_REQUEST['fecha']))and(!empty($_REQUEST['fecha']))){
        $fecha = $_REQUEST['fecha'];
    }


    if((isset($_GET['msg']))&&(!empty($_GET['msg']))){
        $message = '<center><h3>Registro '.$_GET['msg'].'</h3></center>';
    }else{
        $message = '';
    }

    $newdate=strtotime("$fecha");
    $newdate = date ( 'Y-m-d' , $newdate );
    $fecha=$newdate;
       
    //echo '<div style="color:blue; margin:20px;" id ="pulsador" onclick="window.location.href="planilla_planilla.php";"><a href="#"><img width="48"  height="48" src= "img/back.png">Volver</a> </div>';
    echo '<h1><img width="48"  height="48" src= "img/profile.png">Planilla</h1>';
    echo '<a class="agregarUsuario" href="planilla_planilla.php?fecha='.$fecha.'&planilla='.$planilla.'"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
    
    $sql = "SELECT * FROM rh_planilla WHERE id_planilla ='".$planilla."' and fecha = '".$fecha."' ORDER BY id_empleado";
       
    

    //$sql = "SELECT * FROM rh_planilla WHERE id_planilla ='".$planilla."' ORDER BY id_empleado";


    if(($result = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));
    
     
        echo $message;
        
        echo '<center><table border =1>
                <tr class="encabezadoRegistro">
                    <th>Nombre</th>
                    <th>Cuenta Bancaria</th>
                    <th>Total CCSS</th>
                    <th>Total Reajustes</th>
                    <th>Total</th>
                    ';
        
            echo '</tr>';
        $total_salario=0;
        $total_reajustes=0;
        $total_extras=0;
        $total_vales=0;
        $total_compras=0;
        $total_comision=0;
        $total_mandados=0;
        $total_egresos=0;
        $total_ingresos=0;
        $total_ccss=0;
        $total_total=0;
        $total_sal_ccss=0;
        $total_sal_reajustes=0;

        while($row = odbc_fetch_array($result)){
        
            
                
                $id_empleado = $row['id_empleado'];
                $sql2 = "SELECT * FROM rh_empleados WHERE id_empleado ='".$id_empleado."'";

                if(($result2 = odbc_exec($id,$sql2)) === false)
                    die("Error en Select: ". odbc_errormsg($id));
                
                
                while($row2 = odbc_fetch_array($result2)){

                    $nombre = $row2['nombre'] ." ".$row2['apellido'];
                    $cuenta = $row2['cuenta'];
        
                }

                $base= $row['base'];
                $reajuste= $row['reajuste'];
                $salxhora=$row['salxhora'];
                $extras=$row['extras'];
                $vales=$row['vales'];
                $compras=$row['compras'];
                $otros_egresos=$row['otros_egresos'];
                $comision=$row['comision'];
                $mandados=$row['mandados'];
                $ccss=$row['ccss'];
                $otros_ingresos=$row['otros_ingresos'];
                $total=$row['total'];
                
                $total_salario=$total_salario+$base;
                $total_reajustes=$total_reajustes+$reajuste;
                $total_extras=$total_extras+$extras;
                $total_vales=$total_vales+$vales;
                $total_compras=$total_compras+$compras;
                $total_comision=$total_comision+$comision;
                $total_mandados=$total_mandados+$mandados;
                $total_ccss=$total_ccss+$ccss;
                $total_ingresos=$total_ingresos+$otros_ingresos;
                $total_egresos=$total_egresos+$otros_egresos;
                $total_total=$total_total+$total;
               

                // llenar registro
                echo '<tr class="registroA">';
                
                $empleado_reajustes=$reajuste+$extras+$comision+$otros_ingresos-$vales-$compras-$otros_egresos;
                
                $empleado_ccss=$base+$mandados-$ccss;
                if ($empleado_reajustes<0) {

                    $empleado_ccss=$empleado_ccss+$empleado_reajustes;
                    $empleado_reajustes=0;
                }
                $total_sal_ccss=$total_sal_ccss+$empleado_ccss;
                $total_sal_reajustes=$total_sal_reajustes+$empleado_reajustes;
                echo '<td>'.$nombre.'</td><td>'.$cuenta.'</td><td>'.number_format($empleado_ccss,2).'</td><td>'.number_format($empleado_reajustes,2).'</td><td>'.number_format($total,2).'</td></tr>';
                
                //echo '<td><img width="75"  height="75" src= "'.$imagen.'""></td>';
                
                
                


            }
        
       

            //echo '<td><a class="agregarProducto" href="planilla.php"><img width="48"  height="48" src= "img/agregar.png"></a></td>';
           // echo '<td style="color:blue">'.$total_salario.'</td><td style="color:blue">'.$total_reajustes.'</td><td style="color:blue">'.$total_extras.'</td><td style="color:blue">'.$total_ccss.'</td><td style="color:blue">'.$total_vales.'</td><td style="color:blue">'.$total_compras.'</td><td style="color:blue">'.$total_egresos.'</td><td style="color:blue">'.$total_comision.'</td><td style="color:blue">'.$total_mandados.'</td><td style="color:blue">'.$total_ingresos.'</td><td style="color:blue">'.$total_total.'</td></tr>';
        
            echo'<td><input type="image" src="img/imprime.png"  width="48"  height="48" name="imprimir" value="Imprimir"  onClick="window.print();"/></td>';
            //echo '<td><a class="agregarProducto" href="imprimir.php"><img width="48"  height="48" src= "img/imprimir.png"></a></td>';
            echo '<td style="color:red">Salarios</td><td style="color:blue">'.number_format($total_sal_ccss,2).'</td><td style="color:red">Reajustes</td><td style="color:blue">'.number_format($total_sal_reajustes,2).'</td></tr>';
      
        echo '</table></center>';
        ////*****Script para ver imagenes////
        echo '<script type="text/javascript">
            $(document).ready(function() {
                $(".fancybox").fancybox();
            });
        </script>';
        
    

?>
<script src="js/bootstrap.min.js"></script>
    </body>
</html>