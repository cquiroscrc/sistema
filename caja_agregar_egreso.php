<?php
  session_start(); 
  include("db.php");
  header("Content-type: text/html; charset=utf-8");
 
    if(isset($_REQUEST['egresos']) && !empty($_REQUEST['egresos'])){
          $egresos = $_REQUEST['egresos'];      
    }
    if ($_REQUEST['egresos']==0) {
      $egresos = $_REQUEST['egresos'];
    } 
    if(isset($_REQUEST['monto']) && !empty($_REQUEST['monto'])){
          $monto = $_REQUEST['monto'];      
    } 
    if(isset($_REQUEST['descripcion']) && !empty($_REQUEST['descripcion'])){
          $descripcion1 = $_REQUEST['descripcion'];
          
    }
    if(isset($_REQUEST['empresa']) && !empty($_REQUEST['empresa'])){
          $empresa = $_REQUEST['empresa'];      
          $empresa= $empresa.' / ';
    }

    if(isset($_REQUEST['hora']) && !empty($_REQUEST['hora'])){
          $hora = $_REQUEST['hora'];      
          $hora= $hora.' / ';
    }

    if(isset($_REQUEST['lugar']) && !empty($_REQUEST['lugar'])){
          $lugar = $_REQUEST['lugar'];      
          $lugar= $lugar.' / ';
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

  //$descripcion=$empresa .' / '.$lugar.' / '.$descripcion1;
 $descripcion=$empresa .$hora.$lugar.$descripcion1;
 //(Tipo,Documento,Fecha,Monto,Descripcion,Compa�ia)
    
  $sql = "INSERT INTO tbl_egresos
            (Tipo,Documento,Fecha,Monto,Descripcion,Compañia)
            values
            ('".$egresos."','".$documento."','".$Date2."','".$monto."','".$descripcion."',0)";
 //echo $sql; exit;   
    //$sql = "SELECT * FROM tbl_egresos WHERE Fecha ='$Date2'";
    
               if(($res = odbc_exec($id,mb_convert_encoding($sql,  "ISO-8859-1", "UTF-8"))) === false)
              die("Error en Select: ". odbc_errormsg($id));

    
       // echo 'Registro Guardado';
      //echo '<a class="agregarUsuario" href="resultado.php?codigo='.$cod.'"><img width="48"  height="48" src= "img/back.png">Volver</a>';          
      header("location: caja_resultado.php?Date2=".$Date2."&tipo=dia");
    
?>
