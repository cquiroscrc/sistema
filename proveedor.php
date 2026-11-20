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
?>
<!DOCTYPE html> 
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>jQuery UI Autocomplete - Default functionality</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  
  <script>
  $(function() {
    var availableTags = [
    <?php
                  $sql="SELECT * FROM Proveedores ORDER BY nombre";
                  
                  if(($resultado = odbc_exec($id,$sql)) === false)
                      die("Error en Select: ". odbc_errormsg($id));
              
                while($arr = odbc_fetch_array($resultado)){
                  $cod_proveedor=$arr['codigo'];
                  $nombre_proveedor=$arr['nombre'];
                    echo "'".$cod_proveedor." - ".$nombre_proveedor."',";    
                } 
    ?>
      
    ];
    $( "#tags" ).autocomplete({
      source: availableTags
    });
  });
  </script>
</head>
<body>
 
<div class="ui-widget">
  <label for="tags">Tags: </label>
  <input id="tags">
</div>
 <!--
          <select class="form-control" id="proveedor" name="proveedor">
            <optgroup >
              <option value="">Seleccione el proveedor</option>
                <?php
                  $sql="SELECT * FROM Proveedores ORDER BY nombre";
                  
                  if(($resultado = odbc_exec($id,$sql)) === false)
                      die("Error en Select: ". odbc_errormsg($id));
              
                while($arr = odbc_fetch_array($resultado)){
                  $cod_proveedor=$arr['codigo'];
                  $nombre_proveedor=$arr['nombre'];
                    echo '<option value="'.$cod_proveedor.'">'.$cod_proveedor.' - '.$nombre_proveedor.'</option>';    
                } 
                ?>
                
            </optgroup>
          </select>
        -->
 
</body>
</html>