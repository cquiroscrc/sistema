
<?php 

function getRealIP()
    {
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
  
 ?>