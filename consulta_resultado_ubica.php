<?php 

    //echo $codigo; exit;
    

          $sql="SELECT CODIGO,DESCRIPCION,EXISTENCIA,VENTA,ubicacion,potencia,
        substring(codigo,1,1),substring(codigo,6,3 ),substring(codigo,10,15),Case substring(codigo, 1, 1)
        WHEN '0' then substring(codigo,1,1)+substring(codigo,5,4 )+substring(codigo,9,4 )+' '+substring(codigo,1,4 )+' '+ltrim(rtrim(substring(codigo,13,12 )))
        WHEN '1' then substring(codigo,1,1)+substring(codigo,5,4 )+substring(codigo,9,4 )+' '+substring(codigo,1,4 )+' '+ltrim(rtrim(substring(codigo,13,12 )))
        Else codigo END As Filtro FROM inventario WHERE ubicacion LIKE '$codigo%' ORDER BY Filtro";  
     
      

 ?>