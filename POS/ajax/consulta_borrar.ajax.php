
<?php

    
    
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
  
    
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $Factura = $_REQUEST['codigo'];
    }
  
    
     //$Factura=strtoupper($Factura);  
      //// *************** Incluye la consulta ******************
      
    $consulta=" exec spDeleteFactura '".$Factura."'";    
    $res_busqueda = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);

      //// *************** Ejecuto la consulta ******************

   


