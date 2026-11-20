<?php
session_start();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
      
    
  </head>
    
     
    <?php

  
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");

    //// *************** Recibe codigo ******************
    
    $cod="XX";
    if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
      $cod = $_REQUEST['codigo'];
      $familia=0;
    }

    
    
    if (substr($cod,0,5)=='1___ ') {
      $codigo=$cod;
    }
    else{
      $cant=stripos($cod,"_",0);
      if ($cant<>0) {
        $codigo= substr($cod,0,$cant);  
      }else{
        $codigo=$cod;
      }
    }
    $codigo= trim($codigo);
    $codigobusq = '';
    
    if ($familia==1) {
      $codigo='1___ '.$codigo;

    }
    
    //// *************** Incluye recibir ******************
    $suge=0;
    if ($codigo != ""){
    	$reglon=1;
      
     $codigo=strtoupper($codigo);  
      //// *************** Incluye la consulta ******************

      if (substr($codigo,0,1)==1 ) { // vw
          $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";    
      }
      elseif (substr($codigo,0,1)==0 ) { // vw
          $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";    
      }
      elseif (substr($codigo,0,1)==2) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }
      elseif (substr($codigo,0,1)==3) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }
      elseif (substr($codigo,0,1)==4) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }
      elseif (substr($codigo,0,1)==5) {
            $fam=1;
            $des=substr($codigo,1,strlen($codigo));
            $codigo=$fam.'___ '.$des;
             
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by substring(Codigo,1,1),substring(Codigo,5,4), 
          substring(Codigo,9,4),substring(Codigo,1,4),substring(Codigo,13,12)";        
      }
      elseif (substr($codigo,0,1)==9) {
           $sql="SELECT * FROM inventario WHERE codigo like '$codigo%' 
          order by codigo";    
      }elseif (substr($codigo,0,1)==6) {
            $fam=substr($codigo,1,1);
            $des=substr($codigo,2,strlen($codigo));
           $sql="SELECT * FROM inventario WHERE codigo like '$fam%' 
           AND descripcion like '%$des%' OR DESC_ALT like '%$des%' AND codigo like '$fam%' order by descripcion";    
      }


      //// *************** Ejecuto la consulta ******************
             
      $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

      //ingresa nombres de cuadro
        //echo $cod; echo '<br>';
        //echo substr($codigo,0,1);
        //echo $sql; exit;
        echo "<h1>Caja</h1>"; 
        echo "<h1>".$codigo."</h1>"; 
        echo "<center>";   
        echo '<table border =1 id="resultado">
                <tr>
                    
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>Existencia</th>
                    <th>Venta</th>';
                    echo'<th>Transito</th>
                    <th>Ubicacion</th>
                    <th>Suc</th>';
                    echo '<th>Imagen</th>';
                    
            echo '</tr>';
        foreach ($ejecutar as $key => $value) {
       
      

      //ingresa datos en cuadro
          $codigos=$value["CODIGO"];
          $descripcion=$value["DESCRIPCION"];
          $existencia=$value["EXISTENCIA"];
          $costo=$value["COSTO"];
          $venta=$value["VENTA"];
          $costodol=$value["costodol"];
          $proveedor=$value["proveedor"];
          $ubicacion=$value["ubicacion"];
          $desc_tecnica=$value["DESC_ALT"];
          $potencia=$value['potencia'];
          $xpotencia=$potencia*100;
          $venta=$venta+$xpotencia;

            
            
          ?>

  
          
          <?php 
            
            echo '<td><button class="btn btn-success" id="add_codigo" onclick="myCaja('."'".$codigos."'".')">'.$codigos.'</button></td>';
            
            echo '<td><a name="'.$codigos.'" id="a"></a><a style="color:black" data-toggle="tooltip" title="'.$desc_tecnica.'">'.$descripcion.'</a></td>';
            if ($existencia==0) {
              echo '<td style="color:red;"><b>'.number_format($existencia,2).'</b></td>';
            }else{
            echo '<td style="color:blue;"><b>'.number_format($existencia,2).'</b></td>';
            }  
            echo '<td style="color:green"><b>'.number_format(($venta),2).'</b></td>';
            
          
          $sql3="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigos'";
          
          $res_pedi = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql3);         
            
            foreach ($res_pedi as $key => $arr3) {   
              $pedidos=$arr3["pedido"];
            }
            
            /// Pedidos por separado
            $ped_totales="";
            unset($ped_factura);
            $sqlped= "SELECT p.Factura, p.Cantidad, t.shortname from pedidos p, Proveedores t where p.Proveedor=t.codigo and p.codigo = '$codigos'";

            $res_pedi2 = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlped);         
            $cont=0;   
            foreach ($res_pedi2 as $key => $arr_ped) {   
          
              $ped_factura[]=trim($arr_ped['shortname'])." / ".$arr_ped['Factura']." / ".$arr_ped['Cantidad']." *-* "; 
                $cont++;
            }
            if(isset($ped_factura)){
                if ($cont==0) {
                  $ped_totales=$ped_factura[0];
                }else{
                  for($x = 0; $x < $cont; $x++) {
                    $ped_totales=$ped_totales.$ped_factura[$x];
                  }
                }  
                
            }else{
                $ped_totales="";
            }

            //Pedidos por separado *****
          echo '<td><a name="'.$codigos.'" id="a"></a><a style="color:black" data-toggle="tooltip" title="'.$ped_totales.'">'.number_format($pedidos,2).'</a></td>';
          echo '<td>'.$ubicacion.'</td>';
          // meter sucursal
          $sqlsuc= "SELECT * FROM tbl_exi_suc WHERE codigo = '$codigos'";
            $sucursal=0;
            $ressuc = ControladorUtilitaria::ctrMostrarUtilitariaVar($sqlsuc);         
            
            foreach ($ressuc as $key => $arr_suc) {   
              $sucursal = $arr_suc["cantidad"];
            }
          echo '<td style="color:orange">'.$sucursal.'</td>';  

          // ingresar imagen************
          $path='../../subida_cat/';

          if (substr($codigos,0,1)==1) {
            $directorio='1/';
          }
          if (substr($codigos,0,1)==2) {
            $directorio='2/';
          }
          if (substr($codigos,0,1)==3) {
            $directorio='3/';
          }
          if (substr($codigos,0,1)==4) {
            $directorio='4/';
          }
          if (substr($codigos,0,1)==9) {
            $directorio='9/';
          }
          if (substr($codigos,0,1)==0) {
            $directorio='0/';
          }
          $codigos1=rtrim($codigos);
          if (substr($codigos,-2)=='EU') {
             $codigos1=substr($codigos,0,-2);
             $codigos1=rtrim($codigos1);
             
          } 
          if (substr($codigos,-2)=='GR') {
             $codigos1=substr($codigos,0,-2);
             $codigos1=rtrim($codigos1);
          }
          if (substr($codigos,-2)=='TW') {
             $codigos1=substr($codigos,0,-2);
             $codigos1=rtrim($codigos1);
             
          }
          if (substr($codigos,-2)=='JP') {
             $codigos1=substr($codigos,0,-2);
             $codigos1=rtrim($codigos1);
          }
          if (substr($codigos,-2)=='ZL') {
             $codigos1=substr($codigos,0,-2);
             $codigos1=rtrim($codigos1);
          }

          $foto=substr ( $codigos1 , 1 );
          $foto=rtrim($foto);

          if (substr($foto,0,1)==' ') {
                //echo 'FOTOS';
                $foto=substr($foto, 2, strlen($foto));
              }
              
          $filename = $path.$directorio.$foto.".jpg";
          $archivo=$filename;

            $existes = $_SERVER["DOCUMENT_ROOT"].'/subida_cat/'.$directorio.$foto.'.jpg';

            if (is_file($existes)) {
                $archivo=$filename;
            } else {
                $archivo=$path."inf.png";
            }

          echo '<td><a class="fancybox" href="'.$archivo.'" title="'.$codigos.'  -- '.$descripcion.'"><img width="75"  height="75" src= "'.$archivo.'""></a></td>';
          
          echo '</tr>';
         $reglon++;
          			
        }

    }

    ?>

    </div>
    
    
    
  </table>
   <script type="text/javascript">
        $(document).ready(function() {
            $(".fancybox").fancybox();
        });
    </script>
    