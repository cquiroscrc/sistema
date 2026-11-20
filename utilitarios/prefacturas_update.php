<?php

    include("db.php");

    $sql = "DELETE FROM hprefacturas";

  
    //echo $sql; exit;
    if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));


    $sql = "DELETE FROM hpregeneral";

    //echo $sql; exit;
    if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));

       echo '<br>'; 
       echo '<br>';
       echo '<H1>Actualizado las Prefacturas</H1>';

    $sql = "DELETE FROM tmp_devol";

    //echo $sql; exit;
    if(($r_update = odbc_exec($id,$sql)) === false)
        die("Error en Select: ". odbc_errormsg($id));

       echo '<br>'; 
       echo '<br>';
       echo '<H1>Actualizado las Devoluciones</H1>';

?>

<SCRIPT LANGUAGE="javascript"> // Redirecciona al home      
      function closed() { 
        window.open('','_parent',''); 
        window.close(); 
      } 
      setTimeout(closed(),1500);
 </SCRIPT>