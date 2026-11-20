<?php

  
    
    require("../modelos/conexion.php");
    require("../controladores/utilitaria.controlador.php");
    require("../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    

   


    if(isset($_REQUEST['documento']) && !empty($_REQUEST['documento'])){
      $documento = $_REQUEST['documento'];
    }

     $url ="https://api.hacienda.go.cr/fe/ex?autorizacion=$documento";
    $error = '{
  "code": 400,
  "status": "Bad request"
}';
$header = array(
        'Content-Type: application/json'
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $respuesta = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err)
    {
        $arrayResp = array(
            "Status"    => $status,
            "to"        => $apiTo,
            "text"      => $err
        );
        return $arrayResp;
    }
    else
    {
        $arrayResp = array(
            "Status"    => $status,
            "text"      => explode("\n", $respuesta)
        );
        
       
          $resultado = json_decode($respuesta, true);
       
          if(isset($resultado['code']) && !empty($resultado['code'])){  
           echo "ERROR";
          }else{
            echo json_encode($resultado);    
          }

          
        return $arrayResp;
    }
    
