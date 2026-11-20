<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente


?>


<div class="box box-warning">
	
	<div class="box-header with-border">
    <h3 class="box-title">Catalogo Electonico</h3>
    
    <div class="box-tools pull-right">

      <button type="button" class="btn btn-box-tool" data-widget="collapse">

        <i class="fa fa-minus"></i>

      </button>

      <button type="button" class="btn btn-box-tool" data-widget="remove">

        <i class="fa fa-times"></i>

      </button>

    </div>
   </div> 
  <div class="box-body">  	
        <table border=1 style="padding:5px;">
          
        
      <?php
          $cont=1;
          $tot=0;
          $file = fopen(__DIR__ ."../../contador.hhv", "r") or exit("Unable to open file!");
          //Output a line of the file until the end is reached
          while(!feof($file))
          {
            $dato=fgets($file);
               if (substr($dato,0,4)=="172.") {
                }else{
                   if ($tot<4){
                       $tot=0;
                   }
               }

            if ($cont==1) {
              echo "<tr>";
              echo "<td style='padding:5px;color:green'>".$dato."</td>";
              $cont=$cont+1;
              $tot=$tot+1;

            }elseif ($cont==2) {
              echo "<td style='padding:5px;color:red'>".$dato."</td>";
              $cont=$cont+1;
              $tot=$tot+1;

            }elseif ($cont==3) {
              echo "<td style='padding:5px;color:black'>".$dato."</td>";
                $cont=$cont+1;
                $tot=$tot+1;

            }elseif ($cont==4) {
              echo "<td style='padding:5px;color:blue'>".$dato."</td>";
              echo "</tr>";
              $cont=1;
              $tot=$tot+1;
            }

          }
          
          fclose($file);
          $tot=$tot-1;
          echo "<h3 style='color:blue;'> Total de veces usado el catalogo es: ".$tot."</h3>";

      ?>
  	</div>
        </table>
  	
</div>

