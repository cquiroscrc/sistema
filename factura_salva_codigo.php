<?php
  session_start();
  include("db.php");

    
    if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
      $consecutivo = $_REQUEST['consecutivo'];      
    } 
   if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $codigo = $_REQUEST['codigo'];      
    } 
    if(isset($_REQUEST['precio']) && !empty($_REQUEST['precio'])){
          $precio = $_REQUEST['precio'];      
    } 
    if(isset($_REQUEST['cantidad']) && !empty($_REQUEST['cantidad'])){
          $cantidad = $_REQUEST['cantidad'];      
    } 
    if(isset($_REQUEST['existencia']) && !empty($_REQUEST['existencia'])){
          $existencia = $_REQUEST['existencia'];      
    } 
    if(isset($_REQUEST['fecha_sis']) && !empty($_REQUEST['fecha_sis'])){
          $fecha_sis = $_REQUEST['fecha_sis'];      
    }



    date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
    //$fecha=date("Y-m-d");
    $fecha=date("Y-m-d 00:00:00.000", $fecha_sis);

      if ($cantidad===0) {
          // Cantidad =0
          header("location: Factura_agregar.php?consecutivo=".$consecutivo."&codigo=".$codigo."&cantidad=".$existencia."&precio=".$precio."&error=4"); 

      }else{
    
          if ($existencia<$cantidad) {
          //Mayor a existencia
          
           header("location: Factura_agregar.php?consecutivo=".$consecutivo."&codigo=".$codigo."&cantidad=".$existencia."&precio=".$precio."&error=4"); 
          }else{
           // Mayor que 0 y menor que existencia
          // verificar si el codigo ya existe
            $hay=false;
            
            $sql_ver="SELECT * FROM tmp_factura WHERE Codigo='$codigo' AND Factura='$consecutivo'";
            if(($res_ver = odbc_exec($id,$sql_ver)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            while($arr_ver = odbc_fetch_array($res_ver)){
              $hay=true;
              $antigua_cantidad=$arr_ver["Cantidad"];
              $nueva_cantidad=$antigua_cantidad+$cantidad;
            }

            if ($hay==true) {
              if ($existencia<$nueva_cantidad) {
                header("location: Factura_agregar.php?consecutivo=".$consecutivo."&codigo=".$codigo."&cantidad=".$existencia."&precio=".$precio."&error=4"); 
                exit;
              }else{

                $sql_hay="UPDATE tmp_factura SET Cantidad='$nueva_cantidad' WHERE Codigo='$codigo' AND Factura = '$consecutivo'";
                if(($resultado_hay = odbc_exec($id,$sql_hay)) === false)
                  die("Error en Select: ". odbc_errormsg($id));
              }
 
            }else{
              
              $sql_Insert="INSERT INTO tmp_factura (Factura,Codigo,Precio,Cantidad,Fecha)
              values ('".$consecutivo."','".$codigo."','".$precio."','".$cantidad."','".$fecha."')"; 
              $subtotal=$precio*$cantidad;
              $impuesto=$subtotal*0.13;
               $total=$subtotal+$impuesto;
        
        
              if(($r_insert = odbc_exec($id,$sql_Insert)) === false)
               die("Error en Select: ". odbc_errormsg($id));
            }  
        // verifica que existe
            $existe=false;
            $sql_con = "SELECT * FROM hfacturas_tmp where Nfac=$consecutivo";

            if(($res_con = odbc_exec($id,$sql_con)) === false)
                die("Error en Select: ". odbc_errormsg($id));
            $a_subtotal=0;
            $a_impuesto=0;
            $a_total=0;
            while($arr_con = odbc_fetch_array($res_con)){
              $existe=true;
            }    
          
            if ($existe==true) {
           //Update Hfacturas
              $n_subtotal=0;
              $n_impuesto=0;
              $n_total=0;

              $sql_ver2="SELECT * FROM tmp_factura WHERE Factura='$consecutivo'";
                if(($res_ver2 = odbc_exec($id,$sql_ver2)) === false)
                  die("Error en Select: ". odbc_errormsg($id));
                
                while($arr_ver2 = odbc_fetch_array($res_ver2)){
                  $n_subtotal=$n_subtotal+($arr_ver2["Precio"]*$arr_ver2["Cantidad"]);
                  $n_impuesto=$n_subtotal*0.13;
                  $n_total=$n_subtotal+$n_impuesto;
                }
              
              $sql_u = "UPDATE hfacturas_tmp SET subtotal='$n_subtotal',impuesto='$n_impuesto',total='$n_total',fechafact='$fecha' WHERE Nfac=$consecutivo";
            
              if(($resultado_u = odbc_exec($id,$sql_u)) === false)
                die("Error en Select: ". odbc_errormsg($id));
            }else{
            // Ingresa a Hfacturas
              $sql_ifac = "INSERT INTO hfacturas_tmp (Nfac,subtotal,impuesto,total,fechafact)values
              ('".$consecutivo."','".$subtotal."','".$impuesto."','".$total."','".$fecha."')";

              echo $sql_ifac;
              if(($resultado_ifac = odbc_exec($id,$sql_ifac)) === false)
                die("Error en Select: ". odbc_errormsg($id));    
            }   
    


    
          header("location: Factura_nueva.php?consecutivo=".$consecutivo);    
      }
    }
    
   
?>
