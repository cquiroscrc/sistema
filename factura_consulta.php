<?php


include("db.php");

$cod="XX";
    $familia=2;
     
     if(isset($_REQUEST['codigo9']) && !empty($_REQUEST['codigo9'])){
      $cod = $_REQUEST['codigo9'];
      $familia=0;
    } 
    if(isset($_REQUEST['codigo1']) && !empty($_REQUEST['codigo1'])){
      $cod = $_REQUEST['codigo1'];
      $familia=0;
    }
   
    if(isset($_REQUEST['codigo2']) && !empty($_REQUEST['codigo2'])){
      $cod = $_REQUEST['codigo2'];
      $familia=0;
    }

    if(isset($_REQUEST['codigo3']) && !empty($_REQUEST['codigo3'])){
      $cod = $_REQUEST['codigo3'];
      $familia=0;
    }

    if(isset($_REQUEST['codigo4']) && !empty($_REQUEST['codigo4'])){
      //echo 'entro';
      if ($_REQUEST['codigo4']='1 N ') {
        
      }else{
        $cod = $_REQUEST['codigo4'];
        $familia=0;  
      }
      
    }

    if(isset($_REQUEST['codigo5']) && !empty($_REQUEST['codigo5'])){
      $cod = $_REQUEST['codigo5'];
      $familia=1;  
    }

    
    if(isset($_REQUEST['codigo6']) && !empty($_REQUEST['codigo6'])){
      $cod = '8'.$_REQUEST['codigo6'];
      $familia=0;
    }

    if(isset($_REQUEST['codigo7']) && !empty($_REQUEST['codigo7'])){
      $cod = $_REQUEST['codigo7'];
      $familia=0;
    }  
    
   

    if (substr($cod,0,5)=='1___ ') {
      $codigo=$cod;
    }
    else{
      $cant=stripos($cod,"_",0);
      if ($cant<>0) {
        $codigo= substr($cod,0,$cant);  
      }else{
        $codigo=$cod;
      }
    }
    $codigo= trim($codigo);
    $codigobusq = '';
    
    if ($familia==1) {
      $codigo='1___ '.$codigo;

    }
    
    //echo 'X'.$cod.'X'; exit;
    $suge=0;
    if ($codigo != ""){
    	$reglon=1;
      
      if (substr($codigo,0,1)==1 ) { // vw
          $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";    
      }
      elseif (substr($codigo,0,1)==0 ) { // vw
          $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";    
      }
      elseif (substr($codigo,0,1)==2) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }
      elseif (substr($codigo,0,1)==3) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }
      elseif (substr($codigo,0,1)==9) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }elseif (substr($codigo,0,1)==8) {
            $fam=substr($codigo,1,1);
            $des=substr($codigo,3,strlen($codigo));
           $sql="SELECT * FROM inventario WHERE codigo like '$fam%' 
           AND descripcion like '%$des%' order by descripcion";    
      }

      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));

//muestra los datos consultados
	echo "<table border=1>";	
	echo $codigo;
  $temp="numfact";	
	echo "<th>Codigo</th><th>Descripcion</th><th>Existencia</th><th>Precio</th><th>Ubicacion</th><tr> \n";
	//while($row = mysql_fetch_array($sql)){
	while($row = odbc_fetch_array($resultado)){
    echo '<td><a href="factura_agregar.php?codigo='.$row['CODIGO'].'&factura='.$temp.'">'.$row['CODIGO'].'</a></td>';
    echo "<td>".$row['DESCRIPCION']."</td>";
    echo "<td style='color:red'>".$row['EXISTENCIA']."</td>";
    echo "<td style='color:green'>".number_format($row['VENTA'],2)."</td>";
    echo "<td>".$row['ubicacion']."</td><tr> \n";
	}
	echo "<table>";
}
?>