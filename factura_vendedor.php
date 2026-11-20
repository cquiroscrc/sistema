<?php

date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
        //$resultado = $_GET['resultado'];
    }
    
    

include("db.php");

    $mensajeria=0;
    $descuento=0;
    $vendedor=0;
    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
      $consecutivo = $_REQUEST['consecutivo'];      
    } 
    if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
      $cliente = $_REQUEST['cliente'];      
    } 
    if(isset($_REQUEST['descuento']) && !empty($_REQUEST['descuento'])){
          $descuento = $_REQUEST['descuento'];      
    } 
    if(isset($_REQUEST['mensajeria']) && !empty($_REQUEST['mensajeria'])){
      $mensajeria = $_REQUEST['mensajeria'];  
    }

?>

<!DOCTYPE>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta charset="utf-8">  
    <title>Vendedores</title>
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.inputmask.bundle.min.js"></script>
    <script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/signin.css">
    <link rel="StyleSheet" href="css/calendar.css">
    <link rel="icon" href="favicon.ico">
    

  

    

<div align="center"><big style="font-weight: bold;"><big><big><big>   
  
  
  <form class="form-signin" action="factura_agregar_vendedor.php" method="post" name="F;">
  <table align="center" border="0" cellspacing="0">
    <center><h1><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;Seleccione el vendedor</h1>
      <select class="form-control" id="vendedor" name="vendedor" >
      <optgroup >
     <?php             
        
        $sql = "SELECT * FROM Vendedores";

         if(($res = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));

        while($arr = odbc_fetch_array($res)){
            $vendedor=$arr['codigo'];
            $descripcion=$arr['nombre'];
          echo '<option value="'.$vendedor.'">'.$descripcion.'</option>';
      }
      echo '</optgroup>'
    ?>
    </select>
    <br>
  <input type="hidden" class="form-control" id="consecutivo" name="consecutivo" value=<?php echo $consecutivo; ?>>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Seleccionar</button>

  </form>
<input class="btn btn-lg btn-primary btn-block" type='button' id='btn' value='Cerrar' />
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Fact Vendedor';
</script> 
<script src="js/bootstrap.min.js"></script> 
<script type="text/javascript">
    $(document).ready(function () {
        $('#btn').click(function () {
            window.opener.location.reload(true);
            window.close();
        });
    });
</script>

</BODY>
</HTML>

