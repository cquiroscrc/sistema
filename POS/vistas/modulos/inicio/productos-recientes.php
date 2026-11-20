<?php



$sqlrecientes = "SELECT TOP 20 i.CODIGO, i.DESCRIPCION, i.EXISTENCIA, i.VENTA, i.ubicacion, (SELECT sum(cantidad) 
FROM Pedidos where codigo=i.CODIGO) as Pedido FROM 
inventario i, Pedidos p WHERE i.ubicacion = 'ALTA' GROUP BY i.CODIGO,i.DESCRIPCION, i.EXISTENCIA,i.VENTA,i.ubicacion 
ORDER BY i.EXISTENCIA DESC";

$productos1= ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlrecientes); 
            
 ?>


<div class="box box-primary">

  <div class="box-header with-border">

    <h3 class="box-title">Productos recientes Ubicación Alta</h3>

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

    <ul class="products-list product-list-in-box">

    <?php
    
    foreach ($productos1 as $key => $productos) {
      
    //  LLamo para las fotos****
       //var_dump($productos); 
      include "controladores/fotos.php";
      
        echo '<li class="item">

          <div class="product-img">

            <img src="'.$filename.'" alt="'.$productos["CODIGO"].'">

          </div>

          <div class="product-info">

            <a href="" class="product-title">

              '.$productos["DESCRIPCION"].' -Existencia- '.$productos["EXISTENCIA"].' -Transito- '.$productos["Pedido"].'

              <span class="label label-warning pull-right">&cent;'.number_format($productos["VENTA"],2).'</span>

            </a>
      
         </div>

        </li>';

      
    }
    ?>

    </ul>

  </div>

  <div class="box-footer text-center">
    <?php

          if($_SESSION["perfil"] =="total"|| $_SESSION["perfil"] == "parcial"){
          
           echo '<a href="productos" class="uppercase">Ver todos los productos</a>';

          }

    ?>
  
    
  
  </div>

</div>
