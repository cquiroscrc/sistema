<?php
  session_start(); 
  include("db.php");
  header("Content-type: text/html; charset=utf-8");
    if(isset($_REQUEST['cuentaso']) && !empty($_REQUEST['cuentaso'])){
          $cuentaso = $_REQUEST['cuentaso'];      
    }
    if ($_REQUEST['cuentaso']==0) {
      $cuentaso = $_REQUEST['cuentaso'];
    }
    if(isset($_REQUEST['cuentasd']) && !empty($_REQUEST['cuentasd'])){
          $cuentasd = $_REQUEST['cuentasd'];      
    }
    if ($_REQUEST['cuentasd']==0) {
      $cuentasd = $_REQUEST['cuentasd'];
    } 
    if(isset($_REQUEST['monto']) && !empty($_REQUEST['monto'])){
          $monto = $_REQUEST['monto'];      
    } 
    if(isset($_REQUEST['ingreso']) && !empty($_REQUEST['ingreso'])){
          $ingreso = $_REQUEST['ingreso'];      
    } 
    if(isset($_REQUEST['Date2']) && !empty($_REQUEST['Date2'])){
          $Date2 = $_REQUEST['Date2']; 
    }
    if(isset($_REQUEST['documento']) && !empty($_REQUEST['documento'])){
          $documento = $_REQUEST['documento']; 
    }
    if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
          $descripcion = $_REQUEST['descripcion']; 
    }
 //(Tipo,Documento,Fecha,Monto,Descripcion,Compa�ia)
  $sql2= "SELECT * FROM tbl_Bancos WHERE Cuenta='".$cuentaso."' AND Documento='".$documento."'";  
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

     header("location: ingresar_banco_interbanco.php?error=".$existe."");
  }
  
  if ($existe=='no') {
    //echo $sql2; 
    //echo $existe;
    //exit;
    $ttipoo=1;
    $ttipod=0;
    $ingresoo=0;
    $ingresod=$ingreso;
    $egresoo=$monto;
    $egresod=0;

    // Salida
    $sql = "INSERT INTO tbl_Bancos
            (Fecha,Cuenta,Documento,Descripcion,Credito,Debito,Acreditado,Tipo,Saldo,Compañia,CuentaC)
            values
            ('".$Date2."','".$cuentaso."','".$documento."','".$descripcion."','".$ingresoo."','".$egresoo."','1900-01-01 00:00:00.000','".$ttipoo."','','0','')";
    //echo $sql; exit;
    
    
               if(($res = odbc_exec($id,mb_convert_encoding($sql,  "ISO-8859-1", "UTF-8"))) === false)
              die("Error en Select: ". odbc_errormsg($id));
    // Entrada

    $sql2 = "INSERT INTO tbl_Bancos
            (Fecha,Cuenta,Documento,Descripcion,Credito,Debito,Acreditado,Tipo,Saldo,Compañia,CuentaC)
            values
            ('".$Date2."','".$cuentasd."','".$documento."','".$descripcion."','".$ingresod."','".$egresod."','1900-01-01 00:00:00.000','".$ttipod."','','0','')";
    
    //echo $sql2; exit;
    
               if(($res2 = odbc_exec($id,mb_convert_encoding($sql2,  "ISO-8859-1", "UTF-8"))) === false)
              die("Error en Select: ". odbc_errormsg($id));

            
    header("location: ingresar_banco_interbanco.php");        
  }
  
    
       // echo 'Registro Guardado';
      //echo '<a class="agregarUsuario" href="resultado.php?codigo='.$cod.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          
      //header("location: cierre_dia_resultado.php?Date2=".$Date2."&tipo=dia");
    
?>
