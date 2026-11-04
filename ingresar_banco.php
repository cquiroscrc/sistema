<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Deposito ya existe";
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
	<title>Agregar Depositos</title>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	<script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
	<link rel="stylesheet" type="text/css" href="css/csscamara.css" />
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/signin.css">
	<link rel="StyleSheet" href="css/calendar.css">
	<link rel="icon" href="favicon.ico">

	<style>
		input{
			display: none;
		}
		
	</style>

	<style>
      .CalFld{
      	background:#FFFFD8;
        padding:1px;
      	margin:auto;
      }
    </style>
</head>
<body>	<?php
		 
		   

        echo '<br>';  
		
  		echo '<h1>&nbsp;<span class="glyphicon glyphicon-credit-card" aria-hidden="true"></span>&nbsp;Ingresar Bancos </h1>';
  		echo '<a class="agregarUsuario" href="ingresar_banco_interbanco.php">&nbsp;&nbsp;<img width="48"  height="48" src= "img/pago.png">&nbsp;&nbsp;Interbanco</a>';

		include("db.php");

 		echo '<form class="form-signin" action="ingresar_banco_ingresar.php" method="POST">
 		 <center><h2>Agregar movimiento</h2></center>
	    	<p>';

	    ?>
		  <table align="center" border="0" cellspacing="0">
    		<center>
    		<h2>Seleccione la fecha</h2>
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
	   		</center>
	       <br>
		  <?php 
			echo ' 		
      	  <select class="form-control" id="cuentas" name="cuentas" required>
	          <optgroup >';
	          	

	          	$sql = "SELECT * FROM tbl_Cuentas";

               if(($res = odbc_exec($id,$sql)) === false)
      				die("Error en Select: ". odbc_errormsg($id));

   				while($arr = odbc_fetch_array($res)){
              		$cuenta=$arr['Cuenta'];
              		$descripcion=$arr['Banco'];
	          		echo '<option value="'.$cuenta.'">'.$descripcion.'</option>';
	          }
	          echo '</optgroup>
          </select>
          <br>
          <select class="form-control" id="tipo" name="tipo" required>
	          <optgroup >';
	          	echo '<option value="tingreso">Transferencia Ingreso</option>';
	          	echo '<option value="tegreso">Transferencia Salida</option>';
	          	echo '<option value="oingreso">Otro Ingreso</option>';
	          	echo '<option value="oegreso">Otro Egreso</option>';
	          echo '</optgroup>
          </select>
          <br>
		  <input class="form-control" id="documento" name="documento" placeholder="Documento de deposito" required autofocus>
		  <br>
		  <input class="form-control" id="descripcion" name="descripcion" placeholder="Descripcion de movimiento" required autofocus>
		  <br>
		  <input class="form-control" id="monto" name="monto" placeholder="Monto de deposito" required autofocus>
		  <br>';
		  
		  //echo '<input id="Date2" name="Date2" value="'.$fecha2.'">
		  //<input id="tipo" name="tipo" value="'.$tipo.'">

          echo'<button class="btn btn-lg btn-primary btn-block" type="submit">Agregar</button>
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
    document.title = 'Sistema / Ingreso a Banco';
</script> 
<script src="js/bootstrap.min.js"></script>
</html>