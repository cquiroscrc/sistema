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
      {
          return $_SERVER["HTTP_FORWARDED"];
      }
      else
      {
          return $_SERVER["REMOTE_ADDR"];
      }

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

  $maquina=getRealIP();

  $ip1='170.81.32.82';
  $ip2='201.195.22.182';

  $dirlocal="http://172.16.12.3:88/POS";
  $direxterna="http://paticas.no-ip.org:88/POS";

  if((isset($_POST['username']))and(!empty($_POST['username']))){
    $usuario = $_POST['username'];
    
  }
  if((isset($_POST['pass']))and(!empty($_POST['pass']))){
    $password = $_POST['pass'];
    
  }

  if ($usuario=='Taicosta' && $password=='paticas') {
    
        if ($maquina==$ip1 || $maquina==$ip2) {
          header("location: ".$dirlocal."");

        }else{
          header("location: ".$direxterna."");
        }
    
  }else{
    header("location: https://www.lospaticas.com");
  }
  
 ?>
