<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
        //$resultado = $_GET['resultado'];
    }
    include "menu.php";
    require("db.php");
     	  $vendedor=0; 
		  $subtotal=0;
		  $impuesto=0;
		  $descuento=0;
		  $total=0;
if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
  $consecutivo = $_REQUEST['consecutivo'];
  $sql_relleno = "SELECT * FROM hfacturas_tmp where Nfac=$consecutivo";

        if(($res_relleno = odbc_exec($id,$sql_relleno)) === false)
          die("Error en Select: ". odbc_errormsg($id));

        while($arr_relleno = odbc_fetch_array($res_relleno)){
          $fechafact=$arr_relleno["fechafact"];
          $vendedor=intval($arr_relleno["vendedor"]);
          $proveedor=$arr_relleno["proveedor"];
          $nombre=$arr_relleno["Nombre"];
          $adicional=$arr_relleno["Adicional"];
          $subtotal=$arr_relleno["subtotal"];
          $impuesto=$arr_relleno["impuesto"];
          $descuento=$arr_relleno["descuento"];
          $total=$arr_relleno["total"];
          $tipo=$arr_relleno["Tipo"];
          $existe_consecutivo=true;
         //echo '<script language="javascript">alert('.$vendedor.');</script>'; 
       }

}


?>
<!DOCTYPE html>  
<html>  
<head>  
	<meta charset="utf-8">  
	<title>Factura</title>

	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	<link rel="stylesheet" href="css/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
	<script type="text/javascript" src="js/jquery.fancybox.pack.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="css/csscamara.css" />
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/anular.css">
	
	<link rel="icon" href="favicon.ico">
	<style type="text/css">
		
		.botones{
			
			border: 0px;
			background-color: #66ccff;
		}
		
		
	</style>

	<script type="text/javascript">
      
      function popup(url) 
      {
       var width  = 800;
       var height = 500;
       var left   = (screen.width-800) /2;
       var top    = 100;
       var params = 'width='+width+', height='+height;
       params += ', top='+top+', left='+left;
       params += ', directories=no';
       params += ', location=no';
       params += ', menubar=no';
       params += ', resizable=no';
       params += ', scrollbars=yes';
	   params += ', titlebar=no';	      
       params += ', toolbar=no';
       newwin=window.open(url,'customWindow', params);
       if (window.focus) {newwin.focus()}
       return false;
      }

      function closeWin() {
        newwin.close();   // Closes the new window
      }
      
    </script>
</head>
<body>	
		<?php
		 
		 
		  
		  echo '<h3>&nbsp&nbsp<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>&nbsp;'.date("d-m-Y", $fecha_sis).'</span>&nbsp;'.$vendedor.'</h3>';
		  //echo '&nbsp&nbsp'.date("d-m-Y", $fecha_sis);
		  //tmp_factura
		  ?>
    	 

          <center>
          	<div >
          	<table><tr>
          		<td>
          			
            		<?php             
	                 	$sql_v = "SELECT * FROM Vendedores";
	                 	if(($res_v = odbc_exec($id,$sql_v)) === false)
	                	 die("Error en Select: ". odbc_errormsg($id));
	                 	while($arr_v = odbc_fetch_array($res_v)){
	                    	$vendedor_v=$arr_v['codigo'];
	                    	$nombre_v=$arr_v['nombre'];
	                    	if ($vendedor_v==$vendedor) {
	                    	echo $nombre_v;	}
	               		}
               	 	?>
          			
          		</td>
          		<td class="botones">
          			<a style="color:black" href="javascript: void(0)" onclick="popup('factura_vendedor.php?consecutivo=<?php echo $consecutivo; ?>')"><img width="48"  height="48" src= "img/vendedor.png">Vendedor</a>          			
          		</td>
          		<?php 
          		echo'<td class="botones">
          			<a style="color:black" href="factura_iniciar.php"><img width="48"  height="48" src= "img/agregar.png">Nueva</a>
          		</td>';
          		echo'<td class="botones">
          			<a style="color:black" href="factura_buscador.php?consecutivo='.$consecutivo.'"><img width="48"  height="48" src= "img/imprime.png">Facturar</a>
          		</td>';
          		echo'<td class="botones">
          			<a style="color:black" href="#Totales"><img width="48"  height="48" src= "img/facts.png">Proforma</a>
          		</td>';
          		?>
          		<td class="botones">
          			<a style="color:black" href="javascript: void(0)" onclick="popup('factura_envios.php')"><img width="48"  height="48" src= "img/mensajeria.png">Mandados</a>          			
          		</td>
				<td class="botones">
          			<a style="color:black" href="javascript: void(0)" onclick="popup('factura_encomiendas.php')"><img width="48"  height="48" src= "img/envios.png">Mensajeria</a>          			
          		</td>
          		
				<td class="botones">
          			<a style="color:black" href="javascript: void(0)" onclick="popup('factura_telefonos.php')"><img width="48"  height="48" src= "img/phone.png">Telefonos</a>          			
          		</td>
          		<?php
          		echo'<td class="botones">
          			<a style="color:black" href="#Limpiar" onclick="if(confirm(\'Desea limpiar la factura?\')) 
        			location.href=\'factura_limpiar.php?consecutivo='.$consecutivo.'\';
        			"><img width="48"  height="48" src= "img/eliminar.png">Limpiar</a>
          		</td>';
          		 ?>
          	</tr></table>
          	</div>
          	<h1><span class="glyphicon glyphicon-qrcode" aria-hidden="true"></span>&nbsp;Factura <?php echo $consecutivo;?></h1>
          	<br>
	      
          
          <br>
	      <table> 
	      <tr>
	      <td><b>Consecutivo</b></td>
	      <td><b>ID</b></td>
	      <td><b>Codigo</b></td>
	      <td><b>Descripcion</b></td>
	      <td><b>Cantidad</b></td>
	      <td><b>Venta</b></td>
	      <td><b>Total</b></td>
	      <td><b>Foto</b></td>
	      <td><b>Eliminar</b></td>

	      </tr><tr>

	      
			<?php
				//Percent = txtFields(1) / txtFields(0)  'Sub/Descuento/Lineas
    	  		//NCost = txtCosto - (txtCosto * Percent)

				$csubtotal=0;
				$cimpuesto=0;
				$ctotal=0;
				$sqlrelleno="SELECT m.ID,m.Factura,m.Codigo,m.Vendedor,m.Precio,m.Cantidad,m.Fecha,i.DESCRIPCION
  				FROM tmp_factura m, inventario i WHERE Factura='".$consecutivo."' AND m.Codigo = I.CODIGO  ORDER BY ID";

				//$sqlrelleno="SELECT * FROM mcompras WHERE Factura='".$consecutivo."' AND Proveedor='".$proveedor."' ORDER BY ID";
          		if(($resultadorelleno = odbc_exec($id,$sqlrelleno)) === false)
	              die("Error en Select: ". odbc_errormsg($id));

	          	while($relleno = odbc_fetch_array($resultadorelleno)){
	          		echo '<td>'.$consecutivo.'</td>
	      			<td>'.$relleno['ID'].'</td>
	      			<td>'.$relleno['Codigo'].'</td>';
	      			$cod=$relleno['Codigo'];
	      			$codigos=$cod;
	      			$descripcion=$relleno['DESCRIPCION'];
	      			echo '<td>'.$relleno['DESCRIPCION'].'</td>
	      			<td>'.$relleno['Cantidad'].'</td>
	      			<td>'.number_format($relleno['Precio'],2).'</td>';
	      			$totalcod=$relleno['Cantidad']*$relleno['Precio'];
	      			echo '<td>'.number_format($totalcod,2).'</td>';
	      			$csubtotal=$csubtotal+$totalcod;
	      			$path='subida_cat/';

			          if (substr($codigos,0,1)==1) {
			            $directorio='1/';
			          }
			          if (substr($codigos,0,1)==2) {
			            $directorio='2/';
			          }
			          if (substr($codigos,0,1)==3) {
			            $directorio='3/';
			          }
			          if (substr($codigos,0,1)==9) {
			            $directorio='9/';
			          }
			          if (substr($codigos,0,1)==0) {
			            $directorio='0/';
			          }
			          $codigos1=rtrim($codigos);
			          if (substr($codigos,-2)=='EU') {
			             $codigos1=substr($codigos,0,-2);
			             $codigos1=rtrim($codigos1);
			             
			          } 
			          if (substr($codigos,-2)=='GR') {
			             $codigos1=substr($codigos,0,-2);
			             $codigos1=rtrim($codigos1);
			          }
			          if (substr($codigos,-2)=='TW') {
			             $codigos1=substr($codigos,0,-2);
			             $codigos1=rtrim($codigos1);
			             
			          }
			          if (substr($codigos,-2)=='JP') {
			             $codigos1=substr($codigos,0,-2);
			             $codigos1=rtrim($codigos1);
			          }
			          if (substr($codigos,-2)=='ZL') {
			             $codigos1=substr($codigos,0,-2);
			             $codigos1=rtrim($codigos1);
			          }

			          $foto=substr ( $codigos1 , 1 );
			          $foto=rtrim($foto);

			          if (substr($foto,0,1)==' ') {
			                //echo 'FOTOS';
			                $foto=substr($foto, 2, strlen($foto));
			              }
			              
			          $filename = $path.$directorio.$foto.".jpg";
			          $archivo=$filename;

			          if (file_exists($filename)) {
			              $archivo=$filename;
			          } else {
			              $archivo=$path."inf.png";
			          }

			          echo '<td><a class="fancybox" href="'.$archivo.'" title="'.$codigos.'  -- '.$descripcion.'"><img width="75"  height="75" src= "'.$archivo.'""></a></td>';



	      			echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea eliminar este codigo de la factura?\")) 
        			location.href=\"factura_eliminar.php?codigo=".$cod."&consecutivo=".$consecutivo."\";
        			'><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";          
        			echo '</tr><tr>';
	          		
          		}
          		
          		$cimpuesto=$csubtotal*0.13;
          		$ctotal=$csubtotal+$cimpuesto;
          		echo '<td><b>Subtotal</b></td><td><b>'.number_format($csubtotal,2).'</b></td><td><b>Impuesto</b></td><td><b>'.number_format($cimpuesto,2).'</b></td><td><b>Total</b></td><td><b>'.number_format($ctotal,2).'</b></td>';

			?>

	  	  </table>
		<br>
	    <form class="form-signin" action="factura_consultar.php" method="POST">
	    	<p>
      	  <center><h1><span class="glyphicon glyphicon-search" aria-hidden="true"></span>&nbsp;Consulta de codigos</h1>
      	  
        
        	<table WIDTH=400 border =1>
        	<tr>
                    
                    <td>1= Volkswagen</td>
                    <td>2= Daihatsu</td>
                    <td>3= Empi</td>
                    <td>4= Grupo Ns</td>
            </tr>
                    <td>5= Familia VW</td>
                    <td>6= Descripcion</td>
                    <td>7= Volvo</td>
                    <td>9= Accesorios</td>
          </tr>
          </table>
      		</center>
      	  
      	  	<br>
			<input class="form-control" type="text" autocomplete="off" spellcheck="false" id="marca" name="marca" value=" " onkeyup="onChange()" autofocus/>
    		
    		
         
          <input class="form-control" autocomplete="off" spellcheck="false" data-inputmask="'mask': '1### ### ### ######'" id="codigo1" name="codigovw" visible="false"  autofocus>
          <input class="form-control" autocomplete="off" spellcheck="false" data-inputmask="'mask': '### ### #####'" id="codigof" name="codigovwf" visible="false"  autofocus>
          <input class="form-control" autocomplete="off" spellcheck="false" data-inputmask="'mask': '1  N ### ### ###'" id="codigo6" name="codigovwn" visible="false"  autofocus>
          <input class="form-control" autocomplete="off" spellcheck="false" data-inputmask="'mask': '2##### ##### #####'" id="codigo2" name="codigod" visible="false" autofocus>
          <input class="form-control" autocomplete="off" spellcheck="false" 
          data-inputmask="'mask': '&##############'" id="codigo3" name="codigoa" visible="false" autofocus>
          <input class="form-control" autocomplete="off" spellcheck="false" data-inputmask="'mask': '0### ### ### #####'" id="codigo4" name="codigov" visible="false" autofocus>
          <input class="form-control" autocomplete="off" spellcheck="false" data-inputmask="'mask': '3##############'" id="codigo5" name="codigou" visible="false" autofocus>
          <input class="form-control" autocomplete="off" spellcheck="false" data-inputmask="'mask': '# ###################'" id="codigo7" name="codigodes" visible="false" autofocus>
			
		  <input type="hidden" class="form-control" id="consecutivo" name="consecutivo" value=<?php echo $consecutivo; ?>>
		 
          <br>
          
          <button class="btn btn-lg btn-primary btn-block" type="submit">Consultar</button>
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->

        </form>

		

        <script languaje="javascript">
        	 
        	 function onChange() {
            	
            	if ($("input").val() == 1){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo1').style.display='block'; 
            		document.getElementById('codigo1').focus();
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo6').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 	
            	}
            	if ($("input").val() == 2){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='block'; 
			    	document.getElementById('codigo2').focus();
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo6').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 	
            	}
            	if ($("input").val() == 3){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo5').style.display='block'; 
            		document.getElementById('codigo5').focus();
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo6').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 	
            	}
            	if ($("input").val() == 9){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo3').style.display='block'; 
            		document.getElementById('codigo3').focus();
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo6').style.display='none'; 	
            	}
            	if ($("input").val() == 7){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo4').style.display='block'; 
            		document.getElementById('codigo4').focus();
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo6').style.display='none'; 	
            	}
            	if ($("input").val() == 4){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo6').style.display='block'; 
            		document.getElementById('codigo6').focus();
			    	document.getElementById('codigof').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 	
            	}
            	if ($("input").val() == 5){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigof').style.display='block'; 
            		document.getElementById('codigof').focus();
			    	document.getElementById('codigo6').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigo7').style.display='none'; 	
            	}
            	if ($("input").val() == 6){
            		document.getElementById('marca').style.display='none'; 
            		document.getElementById('codigo7').style.display='block'; 
            		document.getElementById('codigo7').focus();
			    	document.getElementById('codigo6').style.display='none'; 
			    	document.getElementById('codigo2').style.display='none'; 
			    	document.getElementById('codigo3').style.display='none'; 
			    	document.getElementById('codigo4').style.display='none'; 
			    	document.getElementById('codigo1').style.display='none'; 
			    	document.getElementById('codigo5').style.display='none'; 
			    	document.getElementById('codigof').style.display='none'; 	
            	}
            	
        	}

			
		</script>
		 <script type="text/javascript">
            $(document).ready(function() {
                $(".fancybox").fancybox();
            });
        </script>
		<script>  //script de la mascara
			$(document).ready(function(){
			    $(":input").inputmask();
			    document.getElementById('codigof').style.display='none'; 
			    document.getElementById('codigo1').style.display='none'; 
		    	document.getElementById('codigo2').style.display='none'; 
		    	document.getElementById('codigo3').style.display='none'; 
		    	document.getElementById('codigo4').style.display='none'; 
		    	document.getElementById('codigo5').style.display='none';
		    	document.getElementById('codigo6').style.display='none';
		    	document.getElementById('codigo7').style.display='none'; 
			});
		</script>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Facturacion';
</script> 	
<script src="js/bootstrap.min.js"></script>	
</html>