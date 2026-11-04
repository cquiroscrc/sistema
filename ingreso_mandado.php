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
?>
<!DOCTYPE html>  
<html>  
<head>  
	<meta charset="utf-8">  
	<title>Agregar mandados</title>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	<script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
	<link rel="stylesheet" type="text/css" href="css/csscamara.css" />
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="StyleSheet" href="css/calendar.css">
	<link rel="stylesheet" href="css/signin.css">
	
	<link rel="icon" href="favicon.ico">

	<style>
		input{
			display: none;
		}

      .CalFld{
      	background:#FFFFD8;
        padding:1px;
      	margin:auto;
      }
    
		
	</style>
</head>
<body>	<?php
		include("db.php");
		echo '<form class="form-signin" action="agregar_mandados.php" method="POST">
 		 		<center><h2>Agregar Mandado</h2></center>';
 		echo '<h3>Mensajero:</>';
 		echo '<select class="form-control" id="mensajero" name="mensajero" required>
	          <optgroup >';
	          	
 
		$sqlmensa = "SELECT * FROM mensajeros";

               if(($resmensa = odbc_exec($id,$sqlmensa)) === false)
      				die("Error en Select: ". odbc_errormsg($id));

   				while($arrmensa = odbc_fetch_array($resmensa)){
              		$idmensa=$arrmensa['id'];
              		$nombremensa=$arrmensa['nombre'];
	          		echo '<option value="'.$idmensa.'">'.$nombremensa.'</option>';
	          }
		echo '</optgroup>
        	   </select>';
		 

        echo '<br>';  
		
 		 ?>
	    <p>Seleccione el rango de fechas</p>
	    <tbody>
	      <tr>
	      
	      
	      <td >
	        <script>
	          DrawCalFld('Date2', 'today');
	        </script>
	      </td>

	      </tr>
	    </tbody>
	    </table>
	  
	   
	   
		<?php 
		
		echo '
		
 		<p>Envia:</p>

      	  <select class="form-control" id="vendedor" name="vendedor" required>
	          <optgroup >';
	          	

	          	$sql = "SELECT * FROM Vendedores";

               if(($res = odbc_exec($id,$sql)) === false)
      				die("Error en Select: ". odbc_errormsg($id));

   				while($arr = odbc_fetch_array($res)){
              		$cuenta=$arr['codigo'];
              		$descripcion=$arr['nombre'];
	          		echo '<option value="'.$cuenta.'">'.$descripcion.'</option>';
	          }
	          echo '</optgroup>
          </select>
          
          
          <p>Factura:</p>
		  <input class="form-control" id="factura" name="factura" placeholder="Numero de factura" required autofocus>
		  <p>Monto:</p>
		  <input class="form-control" id="monto" name="monto" placeholder="Monto" required autofocus>
		  <p>Lugar:</p>
		  <input class="form-control" id="lugar" name="lugar" placeholder="Lugar" required autofocus>
		  <p>Cantidad:</p>
		  <input class="form-control" id="cantidad" name="cantidad" placeholder="Cantidad mandados" required autofocus>  
          <br>	
          <button class="btn btn-lg btn-primary btn-block" type="submit">Agregar</button>
        </form>';
		?>
<script>
W.F=D.F;

function Date1OnChange(){
if (F.Date2) Cal_SetDate(F.Date2, this.value);
}

F.Date1.onchange=Date1OnChange;
</script>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Ingreso Mandado';
</script>	
<script src="js/bootstrap.min.js"></script>
</html>