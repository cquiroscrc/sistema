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
    
      
		
		  if(isset($_REQUEST['factura']) && !empty($_REQUEST['factura'])){
		      $factura = $_REQUEST['factura'];
		      
		    } 
		    if(isset($_REQUEST['documento']) && !empty($_REQUEST['documento'])){
		      $documento = $_REQUEST['documento'];
		      
		    }
            echo '<br>';
		    echo '<a href="consulta_ver_documento.php?documento='.$documento.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          

		    require_once("db.php");
		    echo '<center><h2>'.$factura.'</h2>';
			echo '<br>';
			echo '<center><table border =0>
                <tr>
                    <th>Factura</th>
                    <th>Fecha</th>
                    <th>Movimiento</th>
                    <th>Codigo</th>
                    <th>Cantidad</th>
                    <th>Anterior</th>
                    <th>Actual</th>';
        
            echo '</tr>';
            $movisql="SELECT * from hgeneral where Factura = '$factura' 
              ORDER by FECHA DESC";
              if(($mov = odbc_exec($id,$movisql)) === false)
              		die("Error en Select: ". odbc_errormsg($id));
            
		          while($prome_mov = odbc_fetch_array($mov)){
		             $m_factura=$prome_mov["Factura"];
		             $fecha2=date("d-m-Y",strtotime($prome_mov["fecha"]));
		             $m_fecha= $fecha2;
		             echo '<td>'.$m_factura.'</td>';
	              	 echo '<td>'.$m_fecha.'</td>';
	              	 $ajus_id=$prome_mov['movimiento'];
	              	 echo '<td>'.$prome_mov['movimiento'].'</td>';
	              	 echo '<td>'.$prome_mov['codigo'].'</td>';
	              	 echo '<td>'.$prome_mov['cantidad'].'</td>';
	              	 echo '<td>'.$prome_mov['anterior'].'</td>';
	              	 echo '<td>'.$prome_mov['actual'].'</td>';
	              	 echo '</tr>';
		          }
		      echo '</table>'; 
		      if ($ajus_id=='Ajuste') {
		      		$ajussql="SELECT * from ajutras where IDMovimiento = '$factura'";
              		if(($ajus = odbc_exec($id,$ajussql)) === false)
              			die("Error en Select: ". odbc_errormsg($id));
            
		          	while($arr_ajus = odbc_fetch_array($ajus)){
		             echo '<H2>Comentario:'.$arr_ajus['Comentario'].'</H2>';
		             $id_usuario=$arr_ajus['IDUsuario'];
		             $maquina="SELECT * from acc_usuarios where id = $id_usuario";
              			if(($maq = odbc_exec($id,$maquina)) === false)
              			die("Error en Select: ". odbc_errormsg($id));
            
		          		while($arr_maq = odbc_fetch_array($maq)){
		          			$nombre=$arr_maq['nombre'];
		          		}
	              	 echo '<H3> Usuario: '.$id_usuario.' - '.$nombre.'</H3>';
	              	 
		          }
		    
		        	
		        }  
     ?>
     <script src="js/bootstrap.min.js"></script> 
     <script type="text/javascript" language="JavaScript">
    	document.title = 'Sistema // Consulta documento';
	</script>
 </body>
 </html>