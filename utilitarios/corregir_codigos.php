<?php 

    include("db.php");
    

    $sql="SELECT * FROM inventario WHERE DESCRIPCION like '%,%'"; 
    echo $sql;
     if(($res = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));

      while($arr = odbc_fetch_array($res)){
        $descripcion= $arr['DESCRIPCION'];
        $nueva_descripcion= str_replace(",","-",$descripcion);
        $codigo=$arr["CODIGO"];

        $sql_update="UPDATE inventario SET DESCRIPCION='$nueva_descripcion' WHERE CODIGO='$codigo'";
        echo $sql_update;
        echo '<br>';
        if(($res1 = odbc_exec($id,$sql_update)) === false)
        die("Error en Select: ". odbc_errormsg($id));


      }
 ?>