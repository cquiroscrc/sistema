<?php
$listar=null;
$directorio=opendir("Proformas");
while ($elemento = readdir($directorio)){
	
	if ($elemento != '.' && $elemento != '..') {	
		if (is_dir("Proformas/.$elemento")) {
			$listar.="<li><a href='Proformas/$elemento' target='_blank'>$elemento/</a></li>";
		}else{
			$listar.="<li><a href='Proformas/$elemento' target='_blank'>$elemento/</a></li>";
		}
	}
}

$ordenes=null;
$directorio2=opendir("Ordenes");
while ($elemento2 = readdir($directorio2)){
  
  if ($elemento2 != '.' && $elemento2 != '..') {  
    if (is_dir("Ordenes/.$elemento")) {
      $ordenes.="<li><a href='Ordenes/$elemento2' target='_blank'>$elemento2/</a></li>";
    }else{
      $ordenes.="<li><a href='Ordenes/$elemento2' target='_blank'>$elemento2/</a></li>";
    }
  }
}

$recibos=null;
$directorio3=opendir("Recibos");
while ($elemento3 = readdir($directorio3)){

    if ($elemento3 != '.' && $elemento3 != '..') {
        if (is_dir("Recibos/.$elemento")) {
            $recibos.="<li><a href='Recibos/$elemento3' target='_blank'>$elemento3/</a></li>";
        }else{
            $recibos.="<li><a href='Recibos/$elemento3' target='_blank'>$elemento3/</a></li>";
        }
    }
}
?>
<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Lista de Proformas
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Proformas y Ordenes</li>
    
    </ol>

  </section>

<section class="content">

    <div class="box">

      
      <div class="box-body">
        <ul>
      		<?php 
      			echo $listar;
      		 ?>
    		</ul>    
      </div>

    </div>
  <section class="content-header">
    <br>
    <br>
    <br>
    <h1>
      
      Lista de Ordenes de compra
    
    </h1>

  </section>

  </section>

<section class="content">

    <div class="box">

      
      <div class="box-body">
        <ul>
          <?php 
            echo $ordenes;
           ?>
        </ul>    
      </div>

    </div>

  </section>
    <section class="content-header">
        <br>
        <br>
        <br>
        <h1>

            Lista de Recibos

        </h1>

    </section>

    <section class="content">

        <div class="box">


            <div class="box-body">
                <ul>
                    <?php
                    echo $recibos;
                    ?>
                </ul>
            </div>

        </div>

    </section>
</div>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Archivos';
</script>
	