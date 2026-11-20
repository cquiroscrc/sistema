
<?php 

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
          return $_SERVER["HTTP_FORWARDED"];
      else
          return $_SERVER["REMOTE_ADDR"];

    }

    function verIP(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
    }

  /*
   * MIGRACION 2026-11 a https://sistema.lospaticas.com (nginx + PHP 8.5 en pat-php-app01):
   * REDIRECCION POR IP DESHABILITADA (comentada, no borrada).
   *
   * Que hacia: tomaba la IP del cliente con getRealIP(). Si era una de las IPs publicas
   * de la empresa (170.81.32.82 o 201.195.22.182, o sea alguien dentro de la red) lo
   * mandaba con JavaScript a la IP interna del servidor viejo http://172.16.12.3:88/POS;
   * si venia de cualquier otra IP lo mandaba a http://paticas.no-ip.org:88/POS (DNS dinamico).
   * No era una validacion de seguridad: solo elegia la URL interna o externa del servidor
   * Windows/XAMPP viejo. Con la migracion todo se sirve desde sistema.lospaticas.com, y
   * dejarlo activo mandaba a los usuarios al servidor viejo (172.16.12.3), por eso se comenta.
   *
  $maquina=getRealIP();

  $ip1='170.81.32.82';
  $ip2='201.195.22.182';

  if ($maquina==$ip1 || $maquina==$ip2) {
  ?>
  	<script> 
		window.location.replace('http://172.16.12.3:88/POS'); 
	</script>
  <?php 

  	 //echo '<script language="javascript">alert("Local");</script>';
  }else{
  ?>
  	<script> 
		window.location.replace('http://paticas.no-ip.org:88/POS'); 
	</script>
  <?php 
  
  	 //echo '<script language="javascript">alert("Externo");</script>';
  }
  
   */
?>