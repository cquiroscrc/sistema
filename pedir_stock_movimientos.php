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
	<meta charset="utf-8">  
	<title>Consulta de codigos</title>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	
	<link rel="stylesheet" type="text/css" href="css/csscamara.css" />
	<link href="css/bootstrap.min.css" rel="stylesheet">
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
		    if(isset($_REQUEST['marcador']) && !empty($_REQUEST['marcador'])){
		      $marcador = $_REQUEST['marcador'];
		      
		    }
		    
		    echo '<br>';
		    echo '<a href="pedir_stock_resultado.php?codigo='.$cod.'#'.$marcador.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          

		    require_once("db.php");

		    $sql5="SELECT Valor FROM tbl_Utiles WHERE IDUtilidad=5";
          		if(($prom2 = odbc_exec($id,$sql5)) === false)
              	die("Error en Select: ". odbc_errormsg($id));
          		while($arr5 = odbc_fetch_array($prom2)){
              		$tiempo=$arr5["Valor"];
            	}



			$sql="SELECT * FROM inventario WHERE codigo ='$codigo' 
      		order by substring(Codigo,1,1),substring(Codigo,5,4), 
      		substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";  

      		if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));

          	while($arr = odbc_fetch_array($resultado)){
	          $codigos=$arr["CODIGO"];
	          $descripcion=$arr["DESCRIPCION"];
	          $tecnica=$arr["DESC_ALT"];
	          $existencia=$arr["EXISTENCIA"];
	          $costo=$arr["COSTO"];
	          $venta=$arr["VENTA"];
	          $costodol=$arr["costodol"];
	          $proveedor=$arr["proveedor"];
	         } 
	          $sql4="SELECT codigo, shortname FROM Proveedores WHERE codigo='$proveedor'";

	          if(($prov = odbc_exec($id,$sql4)) === false)
	              die("Error en Select: ". odbc_errormsg($id));
	            $nombre_prove="Local";
	            while($arr4 = odbc_fetch_array($prov)){
	              $nombre_prove=$arr4["shortname"];
	            }
	            /// aqui val el valor de estudio, cantidad de meses tambien en resultado.php
	          $sql2="SELECT (SUM(cantidad)/6) as promedio from hgeneral where codigo = '$codigo' 
              AND movimiento not in('Ajuste') AND 
              substring(factura,1,1)='F' AND 
              FECHA >= Dateadd(mm,-6,Getdate())AND  
              FECHA <= GETDATE()";
         
              if(($prom = odbc_exec($id,$sql2)) === false)
              	die("Error en Select: ". odbc_errormsg($id));
            
	          while($arr2 = odbc_fetch_array($prom)){
	             $promedio=$arr2["promedio"];
	          }
         
          	  $sql3="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigo'";
          	  if(($pedi = odbc_exec($id,$sql3)) === false)
              	die("Error en Select: ". odbc_errormsg($id));  

	          while($arr3 = odbc_fetch_array($pedi)){
              	$pedidos=$arr3["pedido"];
              }

              //datos de codigo

              echo '<br>';
              echo '<br>';
              echo '<br>';
               echo '<center><table border =0>
                <tr>
                    <th WIDTH=250>Codigo</th>
                    <th WIDTH=200>Descripcion</th>
                    <th>Existencia</th>
                    <th>Costo</th>
                    <th>Venta</th>
                    <th>Dolar</th>
                    <th>Proveedor</th>
                    <th>Prom Mens</th>
                    <th>Prom x '.$tiempo.'</th>
                    <th>Transito</th>
                    <th>Sugerido</th>';
        
            echo '</tr>';
               
              echo '<td>'.$codigos.'</td><td>'.$descripcion.'</td>';
              echo '<td>'.number_format($existencia,2).'</td><td>'.number_format($costo,2).
                 '</td><td>'.number_format(($venta+ 100),2).'</td><td>'.number_format($costodol,2).'</td>';
              echo '<td>'.$nombre_prove.'</td><td>'.number_format($promedio,2).'</td>
              <td>'.number_format($promedio*$tiempo,2).'</td><td>'.number_format($pedidos,2).'</td>';          
          $suge = ($promedio*$tiempo)- $existencia-$pedidos;
          echo '<td>'.number_format($suge,2).'</td></td>';
          //datos mensuales
          echo'</table>';
          echo '<br>';
          echo '<center><div>';
          echo '<center><textarea  name="tecnica">'.$tecnica.'</textarea>
          		';

          echo '</div></center>';
           echo '<div class="meses">';

           echo '<center><table border =0>';
           
           $startdate=strtotime("today");
			$enddate=strtotime("-12 months",$startdate);
			$i=0;
			$meses = array();


			while ($startdate >  $enddate) { // saca los ultimos 12 meses
				$meses[$i]=date("M Y", $startdate);
			   //echo "<td class='meses'>".date("M Y", $startdate),"</td>";
			   $startdate = strtotime("-1 month", $startdate);
			   $i++;
			}
			echo '<td>'.$meses[0].'</td><td>'.$meses[1].'</td><td>'.$meses[2].'</td><td>'.$meses[3].'</td><td>'.$meses[4].'</td><td>'.$meses[5].'</td><td>'.$meses[6].'</td><td>'.$meses[7].'</td><td>'.$meses[8].'</td><td>'.$meses[9].'</td><td>'.$meses[10].'</td><td>'.$meses[11].'</td>';
			echo '<tr>';
			//ciclo de rellenar
			for ($i=0; $i < 12; $i++) { 
				$lastday = date('t',strtotime($meses[$i]));
				$ultimo=$lastday.' '.$meses[$i];
				$d2=strtotime($ultimo);
				$d2=date('Y-m-d',$d2);
				$inicio='1 '.$meses[$i];
				$d1=strtotime($ultimo);
				$d1=date('Y-m-1',$d1);

				$messql="SELECT SUM(cantidad) as promedio from hgeneral where codigo = '$codigo' 
	              AND movimiento not in('Ajuste') AND 
	              substring(factura,1,1)='F' AND 
	              FECHA >= '$d1' AND  
	              FECHA <= '$d2'";

	              if(($prome = odbc_exec($id,$messql)) === false)
              		die("Error en Select: ". odbc_errormsg($id));
            
		          while($prome_mensual = odbc_fetch_array($prome)){
		             $p_mensual=$prome_mensual["promedio"];
		          }
	              echo '<td>'.$p_mensual.'</td>';

			}
			echo '</tr>';
			
			//sacar el ultimo dia del mes.
			//$lastday = date('t',strtotime($meses[1]));
			//$ultimo=$lastday.' '.$meses[1];
			//$d2=strtotime($ultimo);
			echo '</div>';

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
              AND FECHA >= Dateadd(yy,-1,Getdate())AND  
              FECHA <= GETDATE() ORDER by FECHA DESC";
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
		         
	              

			


     ?>

		<script>  //script de la mascara
			$(document).ready(function(){
			    $(":input").inputmask();
			});
		</script>

	<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // B_Stock';
</script> 
	<script src="js/bootstrap.min.js"></script> 
</html>