<?php
session_start();
include "menu.php";
?>
<html>
  <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Planilla</title>
        
        <link rel="stylesheet" href="css/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" /> <!--  Imagenes fancy -->
        

        
        
        <link rel="stylesheet" href="css/anular.css">
        <link rel="icon" href="img/favicon.ico">
    </head>
    <body>
<?php
    include("db.php");

    if((isset($_REQUEST['planilla']))and(!empty($_REQUEST['planilla']))){
        $planilla = $_REQUEST['planilla'];
    }
    
    //if((isset($_REQUEST['fecha']))and(!empty($_REQUEST['fecha']))){
    //    $fecha = $_REQUEST['fecha'];
    //}
     if((isset($_REQUEST['fecha']))and(!empty($_REQUEST['fecha']))){
        $fecha = $_REQUEST['fecha'];
    }

    if((isset($_REQUEST['msg']))&&(!empty($_REQUEST['msg']))){
        $message = '<center><h3>Registro '.$_REQUEST['msg'].'</h3></center>';
    }else{
        $message = '';
    }
    //$newdate=strtotime("$fecha");
    //$newdate = date ( 'Y-m-d' , $newdate );
    //$fecha=$newdate;
       
    $newdate=strtotime("$fecha");
    $newdate = date ( 'Y-m-d' , $newdate );
    $fecha=$newdate;

    $sql = "SELECT * FROM rh_planilla WHERE id_planilla ='".$planilla."' and fecha = '".$fecha."' ORDER BY id_empleado";


    if(($result = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));

     
        

        echo '<h1><img width="48"  height="48" src= "img/profile.png">Planilla</h1>';
        //echo $sql;

        if ($admin==true) {
           
            ?>

            <div style="color:blue; margin:20px;" id ="pulsador" onclick="window.location.href='planilla.php';"><a href="#"><img width="48"  height="48" src= "img/back.png">Volver</a> </div>
            <?php 
            //echo '<a class="agregarUsuario" href="planilla.php"><img width="48"  height="48" src= "img/back.png">Volver</a>';    
        }
        
        
       
        echo $message;
        echo 'Fecha: '.$fecha;
        echo ' Planilla '.$planilla;
        echo '<center><table border =0>
                <tr class="encabezadoRegistro">
                    <th>Nombre</th>
                    <th>Salario</th>
                    <th>Reajuste</th>
                    <th>Extras</th>
                    <th>CCSS</th>
                    <th>Vales</th>
                    <th>Compras</th>
                    <th>Otros Egresos</th>
                    <th>Comision</th>
                    <th>Mandados</th>
                    <th>Otros Ingresos</th>
                    <th>Total</th>
                    <th>Eliminar</th>';
        
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
        $total_dif=0;

            while($row = odbc_fetch_array($result)){
            
                
                $id_empleado = $row['id_empleado'];
                $sql2 = "SELECT * FROM rh_empleados WHERE id_empleado ='".$id_empleado."'";


                if(($result2 = odbc_exec($id,$sql2)) === false)
                    die("Error en Select: ". odbc_errormsg($id));
    
    
                while($row2 = odbc_fetch_array($result2)){
                    $nombre = $row2['nombre'] ." ".$row2['apellido'];
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

                $ajuste=$base-$ccss;
                if ($ajuste>$total) {
                    $dif=$total-$ajuste;
                }else{
                    $dif=0;
                }

                
                $total_dif=$total_dif+$dif;
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
                echo '<td>'.$nombre.'</td><td>'.number_format($base,2).'</td><td>'.number_format($reajuste,2).'</td><td>'.number_format($extras,2).'</td><td>'.number_format($ccss,2).'</td><td>'.number_format($vales,2).'</td><td>'.number_format($compras,2).'</td><td>'.number_format($otros_egresos,2).'</td><td>'.number_format($comision,2).'</td><td>'.number_format($mandados,2).'</td>';
                echo '<td>'.number_format($otros_ingresos,2).'</td><td>'.number_format($total,2).'</td>';
                //echo '<td><img width="75"  height="75" src= "'.$imagen.'""></td>';
                
                if ($admin==true) {
                    echo '<td><a href="planilla_eliminar_planilla.php?id='.$id_empleado.'&planilla='.$planilla.'&fecha='.$fecha.'"><img width="48"  height="48" src= "img/eliminar.png"></a></td></tr>';
                    
                }else{
                    echo '</tr>';        
                }       
                


            }
        
        if ($admin==true) {

            echo '<td><a class="agregarProducto" href="planilla_agregar_planilla.php?fecha='.$fecha.'&planilla='.$planilla.'"><img width="48"  height="48" src= "img/agregar.png">Agregar</a></td>';
            /*
            <td><div style="color:blue; margin:20px;" id ="pulsador" onclick="window.location.href='planilla_agregar_planilla.php?"<?php echo $fecha ?>"';"><a href="#"><img width="48"  height="48" src= "img/agregar.png"> Agregar</a> </div></td>
            */
            echo '<td style="color:blue">'.number_format($total_salario,2).'</td><td style="color:blue">'.number_format($total_reajustes,2).'</td><td style="color:blue">'.number_format($total_extras,2).'</td><td style="color:blue">'.number_format($total_ccss,2).'</td><td style="color:blue">'.number_format($total_vales,2).'</td><td style="color:blue">'.number_format($total_compras,2).'</td><td style="color:blue">'.number_format($total_egresos,2).'</td><td style="color:blue">'.number_format($total_comision,2).'</td><td style="color:blue">'.number_format($total_mandados,2).'</td><td style="color:blue">'.number_format($total_ingresos,2).'</td><td style="color:blue">'.number_format($total_total,2).'</td></tr>';
            $total_sal_ccss=$total_salario-$total_ccss;
            $total_sal_reajustes=$total_reajustes+$total_extras+$total_comision+$total_mandados+$total_ingresos-$total_vales-$total_compras-$total_egresos;
            $total_sal_ccss=$total_sal_ccss+$total_dif;
            $total_sal_reajustes=$total_sal_reajustes-$total_dif;
            /*
            ?>
            <td><div style="color:blue; margin:20px;" id ="pulsador" onclick="window.location.href='planilla_imprimir.php';"><a href="#"><img width="48"  height="48" src= "img/imprimir.png"> Listado Imprimir</a> </div></td>
            <?php 
            */
            
            echo '<td><a class="agregarProducto" href="planilla_imprimir.php?fecha='.$fecha.'&planilla='.$planilla.'"><img width="48"  height="48" src= "img/imprimir.png">Listado Imprimir</a></td>';
            echo '<td style="color:red">Salarios</td><td style="color:blue">'.number_format($total_sal_ccss,2).'</td><td style="color:red">Reajustes</td><td style="color:blue">'.number_format($total_sal_reajustes,2).'</td></tr>';
            echo '<td style="color:red"><a href="javascript:window.print(); void 0;"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a></td>';
            echo '</td>';
            

        }
        echo '</table></center>';
        
        ////*****Script para ver imagenes////
        echo '<script type="text/javascript">
            $(document).ready(function() {
                $(".fancybox").fancybox();
            });
        </script>';
        
    

?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Planilla';
</script>
    <script src="js/bootstrap.min.js"></script> 
    </body>
</html>