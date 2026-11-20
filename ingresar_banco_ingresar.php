<?php
  session_start(); 
  include("db.php");
  header("Content-type: text/html; charset=utf-8");
    if(isset($_REQUEST['cuentas']) && !empty($_REQUEST['cuentas'])){
          $cuentas = $_REQUEST['cuentas'];      
    }
    if ($_REQUEST['cuentas']==0) {
      $cuentas = $_REQUEST['cuentas'];
    } 
    if(isset($_REQUEST['monto']) && !empty($_REQUEST['monto'])){
          $monto = $_REQUEST['monto'];      
    } 
    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
          $Date2 = $_REQUEST['Date2']; 
    }
    if(isset($_REQUEST['tipo']) && !empty($_REQUEST['tipo'])){
          $tipo = $_REQUEST['tipo']; 
    }
    if(isset($_REQUEST['documento']) && !empty($_REQUEST['documento'])){
          $documento = $_REQUEST['documento']; 
    }
    if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
          $descripcion = $_REQUEST['descripcion']; 
    }
 //(Tipo,Documento,Fecha,Monto,Descripcion,Compa�ia)
  $sql2= "SELECT * FROM tbl_Bancos WHERE Cuenta='".$cuentas."' AND Documento='".$documento."'";  
  $existe='no';

  if(($res = odbc_exec($id,$sql2)) === false)
              die("Error en Select: ". odbc_errormsg($id));

          while($arr = odbc_fetch_array($res)){
               
                $existe="si";
            }
 //echo $sql2; 
  if ($existe=='si') {
     //echo "<script type='text/javascript'>alert('Deposito ya existe')";            
     //echo "window.location = '\cierre_dia_resultado.php?Date2=".$Date2."&tipo=dia';";
     //echo "</script>";  

     header("location: ingresar_banco.php?error=".$existe."");
  }
  
  if ($existe=='no') {
    //echo $sql2; 
    //echo $existe;
    //exit;    
    if ($tipo=='tingreso') {
      $ttipo=0;
      $ingreso=$monto;
      $egreso=0;
    }elseif ($tipo=='oingreso') {
      $ttipo=0;
      $ingreso=$monto;
      $egreso=0;
    }elseif ($tipo=='tegreso') {
      $ttipo=1;
      $ingreso=0;
      $egreso=$monto;
    }elseif ($tipo=='oegreso') {
      $ttipo=1;
      $ingreso=0;
      $egreso=$monto;
    }

    $sql = "INSERT INTO tbl_Bancos
            (Fecha,Cuenta,Documento,Descripcion,Credito,Debito,Acreditado,Tipo,Saldo,Compañia,CuentaC)
            values
            ('".$Date2."','".$cuentas."','".$documento."','".$descripcion."','".$ingreso."','".$egreso."','1900-01-01 00:00:00.000','".$ttipo."','','0','')";
    //echo $sql; exit;
    //$sql = "SELECT * FROM tbl_egresos WHERE Fecha ='$Date2'";
    
               if(($res = odbc_exec($id,mb_convert_encoding($sql,  "ISO-8859-1", "UTF-8"))) === false)
              die("Error en Select: ". odbc_errormsg($id));
     
    header("location: ingresar_banco.php");        
  }
  
    
       // echo 'Registro Guardado';
      //echo '<a class="agregarUsuario" href="resultado.php?codigo='.$cod.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          
      //header("location: cierre_dia_resultado.php?Date2=".$Date2."&tipo=dia");
    
?>
