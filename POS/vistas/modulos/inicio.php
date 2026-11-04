<?php 

if($_SESSION["perfil"] == "mensajeria" ){


  echo '<script>

    window.location = "mensajeria";

  </script>';

  return;

}
if($_SESSION["perfil"] == "caja" ){


  echo '<script>

    window.location = "caja";

  </script>';

  return;

}

 ?>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Escritorio
      
      <small>Panel de Control</small>
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Tablero</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="row">
      
    <?php

    if($_SESSION["perfil"] =="total"|| $_SESSION["perfil"] == "parcial"){

      include "inicio/cajas-superiores.php";

    }

    ?>

    </div> 

     <div class="row">
       
        <div class="col-lg-12">

          <?php

            if($_SESSION["perfil"] =="total"|| $_SESSION["perfil"] == "parcial"){
            
             include "reportes/grafico-ventas.php";

          }

          ?>

        </div>
      </div>

      <div class="row">

        <div class="col-lg-6">

          <?php
            if($_SESSION["perfil"] =="total"|| $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "bodega" || $_SESSION["perfil"] == "especial"){

              include "reportes/productos-mas-vendidos.php";
           }
          ?>

        </div>


         <div class="col-lg-6">

          <?php

          //if($_SESSION["perfil"] =="total"|| $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "ventas"){
          
           include "inicio/productos-recientes.php";

         //}

          ?>

        </div>
        </div>
        <div class="row">

          <div class="col-lg-12">

          <?php

            if($_SESSION["perfil"] =="total"|| $_SESSION["perfil"] == "parcial"){
            
             include "reportes/reporte-FE.php";

          }

          ?>

        </div>
        <div class="col-lg-12">

          <?php

            if($_SESSION["perfil"] =="total"|| $_SESSION["perfil"] == "parcial"){
            
             include "reportes/partlink24.php";

          }

          ?>

        </div>

         <div class="col-lg-12">
           
             <div class="box box-success">

             <div class="box-header">

            <?php 
                echo '<h1>Bienvenid@ ' .$_SESSION["nombre"].'</h1>';
             ?> 
             </div>

             </div>


         </div>
  </div>
     

  </section>
 
</div>

<?php 

$self = "inicio"; //Obtenemos la página en la que nos encontramos
//header("refresh:10; url=$self"); //Refrescamos cada 300 segundos
echo $self;
echo "<META HTTP-EQUIV='refresh' CONTENT='240; URL=".$self."'>"; 
//header("Refresh: 5; url=".$self);

 ?>
