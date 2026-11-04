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
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.inputmask.bundle.min.js"></script>
	<script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/signin.css">
	<link rel="StyleSheet" href="css/calendar.css">
	<link rel="icon" href="favicon.ico">
	<style>
      .CalFld{
        background:#FFFFD8;
        padding:1px;
        margin:auto;
      }

      input{
			display: none;
		}
	</style>

	

</head>
<body>
		<a href="#" onClick="location.href = 'cierre_financiero_resultados.php'"><img width="48"  height="48" src= "img/adelante.png">Resultados</a> 
		<?php 
		    require("db.php");
			$sql = "SELECT TOP 1 * from tbl_Financiero ORDER BY Fecha DESC";

 			if(($resultado = odbc_exec($id,$sql)) === false)
    			die("Error en Select: ". odbc_errormsg($id));
         	while($arr = odbc_fetch_array($resultado)){
         		$ultima_fecha=strtotime($arr['Fecha']);
         	}

		 ?>
		<p>Ultimo Cierre efectuado <?php echo date("d-m-Y", $ultima_fecha); ?></p>
	    <form class="form-signin" action="cierre_financiero_agregar.php" method="POST">
      		<center>
      		<h2><span class="glyphicon glyphicon-send" aria-hidden="true"></span>&nbsp; Cierre Financiero</h2></center>
      		<table align="center" border="0" cellspacing="0">
      		<tbody>
		      
		      <td >
		        <script>
		        	 var date = new Date();
			          var ultimoDia = new Date(date.getFullYear(), date.getMonth() + 1, 0);
			          
			          var mm = ultimoDia.getMonth() + 1;
			          var dd = 1;
			          var yyyy = ultimoDia.getFullYear();
			          Cal_ImgDir='images/'; var DT=Cal_Today(); Cal_Delim='/'; Cal_MD=1;
		        	DrawCalFld('Date1', Cal_DTFmt(dd, mm, yyyy));
		        </script> 
		      </td>
		      
		      <td >
		        <script>
		        
		         
		          var date = new Date();
		          var ultimoDia = new Date(date.getFullYear(), date.getMonth() + 1, 0);
		          
		          var mm = ultimoDia.getMonth() + 1;
		          var dd = ultimoDia.getDate();
		          var yyyy = ultimoDia.getFullYear();
		          Cal_ImgDir='images/'; var DT=Cal_Today(); Cal_Delim='/'; Cal_MD=1;
		          //DrawCalFld('Date2', Cal_DTFmt(Cal_GetMonthLen(DT[2]), DT[1], DT[2])); // 01.02.2003 today , '01.02.2003'
		          DrawCalFld('Date2', Cal_DTFmt(dd, mm, yyyy));
		        </script>
		      </td>
		      </tr>  
		    </tbody>
			</table>
			<br>
	      	<input class="form-control" id="costof" name="costof" required placeholder="Costo Financiero" autofocus>
			<br>
	      	<input class="form-control" id="agua" name="agua" required placeholder="Agua" autofocus>
			<br>
		    <input class="form-control" id="luz" name="luz" required placeholder="Luz" autofocus>
			<br>
			<input class="form-control" id="telefono" name="telefono" required placeholder="Telefono" autofocus>
			<br>
			<input class="form-control" id="ccss" name="ccss" required  placeholder="C.C.S.S" autofocus>
			<br>
			<input class="form-control" id="tributacion" name="tributacion" required placeholder="Tributacion" autofocus>
			<br>
			<input class="form-control" id="tc" name="tc" required placeholder="Tipo de Cambio" autofocus>
			<br>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Aplicar</button>
			
          
          <!--<a href="index.php" class="btn btn-lg btn-primary btn-block">Volver</a> -->

<script src="js/bootstrap.min.js"></script>
</html>