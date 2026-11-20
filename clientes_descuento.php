<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

include "menu.php";
include("db.php");

?>

<!DOCTYPE>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta charset="utf-8">  
    <title>Reporte de Metas</title>
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.inputmask.bundle.min.js"></script>
    <script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/signin.css">
    <link rel="StyleSheet" href="css/calendar.css">
    <link rel="icon" href="favicon.ico">
    
  </head>
<body>

<div align="center"><big style="font-weight: bold;"><big><big><big>   
  
  
  <form class="form-signin" action="clientes_autorizar.php" method="POST" name="F;">
    <center><h1><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;Descuentos Clientes</h1>
      </center>  
  <br>
<?php 

      $existe='NO';
      $sql="SELECT * FROM clientes WHERE  codigo = 1998 ";  
      
      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
      while($arr = odbc_fetch_array($resultado)){
        echo "<h3>".$arr['codigo']." - ".$arr['nombre']."</h3>";
        echo "<h3>Pasa al codigo -".$arr['comentario']."</h3>";
        echo "<br>";
        echo '<a class="btn btn-lg btn-primary btn-block" href="clientes_liberar.php" role="button">Reestablecer</a>';
        $existe = 'SI';
      }
  if ($existe=='NO') {
       
          

 ?>
  <select class="form-control" id="datos" name="datos" onChange="habilitar(this.form)">
      <optgroup >
  <?php 
  
   $sql="SELECT * FROM clientes WHERE  codigo >= 2000 ";  
      
      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
      while($arr = odbc_fetch_array($resultado)){
        echo "<option value='".$arr['codigo']."'>".$arr['codigo']." - ".$arr['nombre']."</option>";
        
      }

   ?>
    </optgroup>
  </select>
  <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Autorizar</button>

  </form>

<?php 
  } // Fin de existe
 ?>


<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Clientes Descuento';
</script>
<script src="js/bootstrap.min.js"></script> 
</BODY>
</HTML>
