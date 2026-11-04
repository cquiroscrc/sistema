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

     header("location: caja_resultado.php?Date2=".$Date2."&tipo=dia&error=".$existe."");
  }  
  if ($existe=='no') {
      
    
  $sql = "INSERT INTO tbl_Bancos
            (Fecha,Cuenta,Documento,Descripcion,Credito,Debito,Acreditado,Tipo,Saldo,Compañia,CuentaC)
            values
            ('".$Date2."','".$cuentas."','".$documento."','Caja del ".$Date2." (0)','".$monto."','0','1900-01-01 00:00:00.000','0','','0','')";
    //echo $sql; exit;
    //$sql = "SELECT * FROM tbl_egresos WHERE Fecha ='$Date2'";
    
               if(($res = odbc_exec($id,mb_convert_encoding($sql,  "ISO-8859-1", "UTF-8"))) === false)
              die("Error en Select: ". odbc_errormsg($id));

    
       // echo 'Registro Guardado';
      //echo '<a class="agregarUsuario" href="resultado.php?codigo='.$cod.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          
      header("location: caja_resultado.php?Date2=".$Date2."&tipo=dia");
    }  
?>
