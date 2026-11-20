<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
        //$resultado = $_GET['resultado'];
    }
    include "menu.php";

    function getRealIP()
    {
      // Migracion: detras de Cloudflare Tunnel la IP real del cliente llega en CF-Connecting-IP
      if (!empty($_SERVER["HTTP_CF_CONNECTING_IP"])) {
          return $_SERVER["HTTP_CF_CONNECTING_IP"];
      }

   
      if (isset($_SERVER["HTTP_CLIENT_IP"]))
      {
          return $_SERVER["HTTP_CLIENT_IP"];
      }
      elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
      {
          return $_SERVER["HTTP_X_FORWARDED_FOR"];
      }
      elseif (isset($_SERVER["HTTP_X_FORWARDED"]))
      {
          return $_SERVER["HTTP_X_FORWARDED"];
      }
      elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]))
      {
          return $_SERVER["HTTP_FORWARDED_FOR"];
      }
      elseif (isset($_SERVER["HTTP_FORWARDED"]))
      {
          return $_SERVER["HTTP_FORWARDED"];
      }
      else
      {
          return $_SERVER["REMOTE_ADDR"];
      }
   
    }


?>
<!DOCTYPE html> 
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Resultado de Visitas</title>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.inputmask.bundle.min.js"></script>
<script src="js/calendar.js?v=1.12" charset="ISO-8859-1"></script>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/anular.css">
<link rel="StyleSheet" href="css/calendar.css">
<link rel="icon" href="favicon.ico">

</head>

<body>
  
<br>

  



  <?php

     
      //ingresa nombres de cuadro
      echo '<center>';
      echo "<h2>Record de visitas paginas web desde el 15 de febrero 2017</h2>";
      echo "<table border = '1'> ";
      echo "<tr> ";
      echo "<th><b>Carro</b></th> ";
      echo "<th><b>Visitas</b></th> ";
      echo "<th><b>Carro</b></th>";
      echo "<th><b>Visitas</b></th> ";
      echo "<th><b>Carro</b></th>";
      echo "<th><b>Visitas</b></th> ";
      echo "<th><b>Carro</b></th>";
      echo "<th><b>Visitas</b></th> ";
      echo "<th><b>Carro</b></th>";
      echo "<th><b>Visitas</b></th> ";
      echo "<th><b>Carro</b></th>";
      echo "<th><b>Visitas</b></th> ";
      echo "<th><b>Carro</b></th>";
      echo "<th><b>Visitas</b></th> ";
      echo "</tr> ";
      echo "<tr> ";

      
      
        //Verificar direccion
        $ipdireccion= getRealIP();
               
       // if(substr($ipdireccion, 0, 8 ) == "192.168."){
       //   $ipbase="http://192.168.1.89";  
       // }else{
        //  $ipbase="http://201.195.22.182:89";
         // $ipbase="http://201.195.22.182:89";
        //}
         $ipbase="http://lospaticas.com/cats";
        ///Principal
        $archivo = "http://lospaticas.com/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_sitio = file_get_contents($archivo);
        fclose($abre);

        $archivo = $ipbase."/Vocho/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        
        $visita_vocho = file_get_contents($archivo);
        
        fclose($abre);

        $archivo = $ipbase."/Microbus/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_micro = file_get_contents($archivo);
        fclose($abre);

        $archivo = $ipbase."/Jetta/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_jetta = file_get_contents($archivo);
        fclose($abre);

        $archivo = $ipbase."/karmann/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_karmann = file_get_contents($archivo);
        fclose($abre);        

        $archivo = $ipbase."/Charade/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_charade = file_get_contents($archivo);
        fclose($abre);

        $archivo = $ipbase."/Terios/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_terios = file_get_contents($archivo);
        fclose($abre);

        $archivo = $ipbase."/Delta/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_delta = file_get_contents($archivo);
        fclose($abre);

        $archivo = $ipbase."/Empi/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_empi = file_get_contents($archivo);
        fclose($abre);
        
        $archivo = $ipbase."/Rocky/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_rocky = file_get_contents($archivo);
        fclose($abre);

        $archivo = $ipbase."/Bego/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_bego = file_get_contents($archivo);
        fclose($abre);

        $archivo = $ipbase."/Sirion/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_sirion = file_get_contents($archivo);
        fclose($abre);

        $archivo = $ipbase."/Amarok/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_amarok = file_get_contents($archivo);
        fclose($abre);
        $archivo = $ipbase."/GranMax/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_max = file_get_contents($archivo);
        fclose($abre);



        //// boletines
        $ipbase="http://lospaticas.com/boletines";
        $archivo = $ipbase."/abril/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_abril_17 = file_get_contents($archivo);
        fclose($abre);

        $ipbase="http://lospaticas.com/boletines";
        $archivo = $ipbase."/mayo/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_mayo_17 = file_get_contents($archivo);
        fclose($abre);

        $ipbase="http://lospaticas.com/boletines";
        $archivo = $ipbase."/junio/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_junio_17 = file_get_contents($archivo);
        fclose($abre);

        $ipbase="http://lospaticas.com/boletines";
        $archivo = $ipbase."/julio/contador.hhv"; 
        $abre = fopen($archivo, "r"); 
        $visita_julio_17 = file_get_contents($archivo);
        fclose($abre);

        echo "<td>Vocho</td> ";
        echo "<td>".number_format($visita_vocho,2)."</td>";
        echo "<td>Microbus</td> ";
        echo "<td>".number_format($visita_micro,2)."</td>";
        echo "<td>Jetta</td> ";
        echo "<td>".number_format($visita_jetta,2)."</td>";
        echo "<td>Empi</td> ";
        echo "<td>".number_format($visita_empi,2)."</td>";
        echo "<td>Karmann</td>";
        echo "<td>".number_format($visita_karmann,2)."</td>";
        echo "<td>Amarok</td>";
        echo "<td>".number_format($visita_amarok,2)."</td>";
        echo "<td></td>";
        echo "<td></td>";
        echo "</tr> ";

        echo "<td>Charade</td> ";
        echo "<td>".number_format($visita_charade,2)."</td>";
        echo "<td>Terios</td> ";
        echo "<td>".number_format($visita_terios,2)."</td>";
        echo "<td>Delta</td> ";
        echo "<td>".number_format($visita_delta,2)."</td>";
        echo "<td>Rocky</td> ";
        echo "<td>".number_format($visita_rocky,2)."</td>";
        echo "<td>Bego</td> ";
        echo "<td>".number_format($visita_bego,2)."</td>";
        echo "<td>Sirion</td> ";
        echo "<td>".number_format($visita_sirion,2)."</td>";
        echo "<td>Gran Max</td> ";
        echo "<td>".number_format($visita_max,2)."</td>";
        echo "</tr> ";
        

        echo "<th>Boletines</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>";
        echo "</tr> ";
        echo "<td>Abril 2017</td> ";
        echo "<td>".number_format($visita_abril_17,2)."</td>";
        echo "<td>Mayo 2017</td>"; 
        echo "<td>".number_format($visita_mayo_17,2)."</td>";
         echo "<td>Junio 2017</td>"; 
        echo "<td>".number_format($visita_junio_17,2)."</td>";
        echo "<td>Julio 2017</td>"; 
        echo "<td>".number_format($visita_julio_17,2)."</td>";
        
        echo "<td></td>";
        echo "</tr> ";

        echo "<th>Pagina</th> ";
        $valvisita=intval($visita_sitio);
        $visita_sitio=$valvisita;
        echo "<th>".number_format($visita_sitio,2)."</th>";        
        echo "<th></th>";
        echo "<th></th>";
        echo "<th></th>";
        echo "<th></th>";
        echo "<th></th>";
        echo "<th></th>";
        echo "<th></th>";
        echo "<th></th>";
        echo "<th></th>";
        echo "<th></th>";
        echo "<th></th>";
        echo "<th></th>";
        echo "</tr> ";
        
        echo "</table>";
        echo "</center>";
echo '<a href="javascript:window.print(); void 0;"><img width="48"  height="48" src= "img/imprime.png">Imprimir</a>';

?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Visitas';
</script> 
<script src="js/bootstrap.min.js"></script> 
</body>
</html>