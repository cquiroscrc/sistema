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
     
     if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
      $consecutivo = $_REQUEST['consecutivo'];  
  }

?>
<!DOCTYPE html>  
<html>  
<head>  
  <meta charset="utf-8">  
  <title>Factura</title>

  <script src="js/jquery.min.js"></script>
  
  <script type="text/javascript" src="js/jquery.fancybox.pack.js?v=2.1.5"></script>
  <link rel="stylesheet" type="text/css" href="css/csscamara.css" />
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/anular.css">
  
  <link rel="icon" href="favicon.ico">

<script language="JavaScript">
function sele(idcliente,consecutivo)
{
var agree=confirm("¿Realmente desea facturar al cliente seleccionado?");
if (idcliente=='0') {idcliente='contado'}

if (agree) { document.location="factura_nombre.php?cliente="+idcliente+"&consecutivo="+consecutivo; }
else return false ;
}
</script>
<style type="text/css">
body p {
	font-family: Verdana, Geneva, sans-serif;
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


<?php 
  echo '<a href="factura_nueva.php?consecutivo='.$consecutivo.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';
 ?>
<form class="form-signin" name="form1" method="post" action="factura_buscador.php" id="cdr" >
  <center>
  <h3>Buscar Cliente  </h3>
      <p>
        <input class="form-control" name="busca"  type="text" id="busqueda" required autofocus>
        <input name="consecutivo"  type="hidden" id="consecutivo" value=<?php echo $consecutivo;  ?>>
        <br>

        <input  class="btn btn-lg btn-primary btn-block" type="submit" name="Submit" value="Buscar" />
        </p>
      </p>
</form>
  <a class="btn btn-lg btn-primary btn-block"  href="javascript: void(0)" onclick="popup('factura_cliente_tipo.php?consecutivo=<?php echo $consecutivo; ?>')">Agregar</a>

</center>
 <p>
  
   
  <?php
if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
  $consecutivo = $_REQUEST['consecutivo'];  
}
$busca="";
if(isset($_REQUEST['busca']) && !empty($_REQUEST['busca'])){
  $busca = $_REQUEST['busca'];  
}


if($busca!=""){
  $sql="SELECT * FROM clientes WHERE nombre like '%$busca%' and comentario <> '**DESACTIVADO**'
              order by nombre";    
       
  if(($resultado = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));


  ?>
    <div>
      <center>
    <table width="1166" border="1" id="tab">
       <tr>
         <th width="19">Id </th>
         <th width="220">Nombre</th>
         <th width="221">Direccion</th>
         <th width="176">Correo</th>
         <th width="70">Telefono</th>
         <th width="70">Cedula</th>
         <th width="60">Selec</th>
       </tr>
     
<?php

  while($row = odbc_fetch_array($resultado)){
  echo '<tr>';
  echo '<td width="19">'.$row['codigo'].'</td>';
  echo '<td width="220">'.$row['nombre'].'</td>';
  echo '<td width="221">'.$row['direccion'].'</td>';
  echo '<td width="67">'.$row['correo'].'</td>';
  echo '<td width="107">'.$row['telefono'].'</td>';
  echo '<td width="107">'.$row['cedula'].'</td>';
  echo  '<td>'.'<a onclick="sele('.$row['codigo'].','.$consecutivo.')"><img width="48" height="48" src="img/agregar.png"></a></td>';

  echo '</tr>';
  
  }
}

?>
</table>
  </center>
</div>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Nombre Factura';
</script> 
<script src="js/bootstrap.min.js"></script>