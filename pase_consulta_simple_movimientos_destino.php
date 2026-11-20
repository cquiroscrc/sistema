<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
        //$resultado = $_GET['resultado'];
    }
    include "menu.php";
    if(isset($_REQUEST['codigoOrigen']) && !empty($_REQUEST['codigoOrigen'])){
		$codigoOrigen = $_REQUEST['codigoOrigen'];
		      
    } 
?>
<!DOCTYPE html>  
<html>  
<head>  
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
	<title>Actualizacion de codigos</title>
	<script src="js/jquery.min.js"></script>
	<link rel="stylesheet" href="css/anular.css">
	<link rel="icon" href="favicon.ico">

</head>
<body>	

	<?php
    
      
		
		  if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
		      $codigo = $_REQUEST['codigo'];
		      
		    } 
          if(isset($_REQUEST['sele']) && !empty($_REQUEST['sele'])){
		      $cod = $_REQUEST['sele'];
		      
		    }
		    echo '<br>';
		    echo '<a href="pase_consulta_simple_resultado_destino.php?codigo='.$cod.'&codigoorigen='.$codigoOrigen.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          

		    require_once("db.php");
		    
		     echo "<center><h1>".$codigo."&nbsp;&nbsp;&nbsp;<a class = 'echo_link' href='#' onclick='if(confirm(\"Unificar codigo ".$codigoOrigen." --> ".$codigo."\")) 
              location.href=\"pase_consulta_simple_aplicar.php?codigo=".$codigo."&codigoOrigen=".$codigoOrigen."\";'><img width='48'  height='48' src= 'img/agregar.png'></a></h1>";
			echo '<br>';
			echo '<center><table border =0>
                <tr>
                    <th>Factura</th>
                    <th>tipo</th>
                    <th>Cantidad</th>
                    <th>Anterior</th>
                    <th>Actual</th>
                    <th>Fecha</th>';
        
            echo '</tr>';
            $movisql="SELECT Factura, fecha, cantidad, movimiento, anterior, actual from hgeneral where codigo = '$codigo' 
              ORDER by FECHA DESC";
              if(($mov = odbc_exec($id,$movisql)) === false)
              		die("Error en Select: ". odbc_errormsg($id));
            
		          while($prome_mov = odbc_fetch_array($mov)){
		             $m_factura=$prome_mov["Factura"];
		             $fecha2=date("d-m-Y",strtotime($prome_mov["fecha"]));
		             $m_fecha= $fecha2;
		             $m_cantidad=$prome_mov["cantidad"];
		             $m_tipo=$prome_mov["movimiento"];
		             $m_anterior=$prome_mov["anterior"];
		             $m_actual=$prome_mov["actual"];
		             echo '<td>'.$m_factura.'</td>';
	              	 echo '<td>'.$m_tipo.'</td>';
	              	 echo '<td>'.$m_cantidad.'</td>';
	              	 echo '<td>'.$m_anterior.'</td>';
	              	 echo '<td>'.$m_actual.'</td>';
	              	 echo '<td>'.$m_fecha.'</td>';
	              	 echo '</tr>';
		          }
		      echo '</table>';   
     ?>
    <script type="text/javascript" language="JavaScript">
    	document.title = 'Sistema // Pase Movimientos';
	</script> 
     <script src="js/bootstrap.min.js"></script> 
 </body>
 </html>