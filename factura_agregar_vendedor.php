<?php

    require("db.php");

    if((isset($_POST['consecutivo']))and(!empty($_POST['consecutivo']))){
        $consecutivo = $_POST['consecutivo'];
    }
    if((isset($_POST['vendedor']))and(!empty($_POST['vendedor']))){
        $vendedor = $_POST['vendedor'];
    }
    $existe=false;
    $sql_con = "SELECT * FROM hfacturas_tmp where Nfac=$consecutivo";

        if(($res_con = odbc_exec($id,$sql_con)) === false)
          die("Error en Select: ". odbc_errormsg($id));

        while($arr_con = odbc_fetch_array($res_con)){
         
          $existe=true;
       }    

   



    if ($existe==true) {
           //Update
            $sql = "UPDATE hfacturas_tmp SET vendedor='$vendedor' WHERE Nfac=$consecutivo";

            if(($resultado = odbc_exec($id,$sql)) === false)
                    die("Error en Select: ". odbc_errormsg($id));
       }else{
            $sql = "INSERT INTO hfacturas_tmp
            (vendedor,Nfac)
            values
            ('".$vendedor."','".$consecutivo."')";

    
            if(($resultado = odbc_exec($id,$sql)) === false)
                die("Error en Select: ". odbc_errormsg($id));    
       }   
    
        
       // echo 'Registro Guardado';
       
       header("location: factura_vendedor.php?consecutivo=".$consecutivo);
    







?>