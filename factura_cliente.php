<?php


include("db.php");

$cliente="XX";
    $familia=2;
     
     if(isset($_REQUEST['cliente']) && !empty($_REQUEST['cliente'])){
      $cliente = $_REQUEST['cliente'];
     
    } 
    $suge=0;
    if ($cliente != ""){
    	$reglon=1;
      
          $sql="SELECT * FROM clientes WHERE nombre like '%$cliente%' and comentario <> '**DESACTIVADO**'
            order by nombre";    
      }

      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));

//muestra los datos consultados
	echo "<table border=1>";	
	echo $codigo;
  $temp="numfact";	
	echo "<th>Codigo</th><th>Nombre</th><th>Direccion</th><tr> \n";
	//while($row = mysql_fetch_array($sql)){
	while($row = odbc_fetch_array($resultado)){
    echo '<td><a href="factura_agregar.php?codigo='.$row['codigo'].'</a></td>';
    echo "<td>".$row['nombre']."</td>";
    echo "<td style='color:red'>".$row['direccion']."</td><tr> \n";
	}
	echo "<table>";
}
?>