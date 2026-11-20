<?php
session_start();
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        
    }
    include "menu.php";
?>
<!DOCTYPE html>
<style>
  #myProgress {
  position: relative;
  width: 100%;
  height: 30px;
  background-color: #ddd;
}

#myBar {
  position: absolute;
  width: 1%;
  height: 100%;
  background-color: #FF0000;
}
#label {
  text-align: center;
  line-height: 30px;
  color: white;
}

</style>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
      <title>Resultado de consulta Pedido</title>
      <script src="js/jquery.min.js"></script>
      <script src="js/jquery.inputmask.bundle.min.js"></script>
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/anular.css">
      
      <link rel="icon" href="favicon.ico">
    
  </head>
    
  <body>

    <style type="text/css">

    .botones{
        width:850px;
        height: 75px;
        padding:15px;
        background-color:#eee;
        border:1px solid #444;
    }
    input{
        
        background-color:white;
        width: 50px;
    }
    </style>

    <br>
    
    <?php
      echo '<div id="myProgress">
      <div id="myBar">
      <div id="label">10%</div></div>
      </div>';

      echo '<br>';
      echo '<div id="botones" style="display:none;">';
      echo '<a href="reportes/promedio.csv"><img width="48"  height="48" src= "img/reporte.png">Abrir Reporte</a> ';
      echo '</div>';
    

    require_once("db.php");
    $cod="XX";

 
    //echo $codigo; exit;
    $sql5="SELECT Valor FROM tbl_Utiles WHERE IDUtilidad=5";
          if(($prom2 = odbc_exec($id,$sql5)) === false)
              die("Error en Select: ". odbc_errormsg($id));
          while($arr5 = odbc_fetch_array($prom2)){
              $tiempo=$arr5["Valor"];
            }

    $suge=0;

    $sql = "SELECT CODIGO, DESCRIPCION, DESC_ALT, EXISTENCIA, ubicacion,VENTA,COSTO,costodol,proveedor,
    substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
    WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
    WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))      
    Else CODIGO END As Filtro FROM inventario  order by Filtro
    ";

    
    	$reglon=1;
      
      
      if(($resultado = odbc_exec($id,$sql)) === false)
              die("Error en Select: ". odbc_errormsg($id));

     
           
            $contador=1;
        $csv_end = "\n";  
              $csv_sep = ";";  
              $csv_file = "reportes/promedio.csv";  
              $csv="";  
          $csv.="Codigo".$csv_sep."Descripcion".$csv_sep."Tecnica".$csv_sep.
              "Existencia".$csv_sep."Venta".$csv_sep.'Costo'.$csv_sep.'Dolares'.$csv_sep.
              'Proveedor'.$csv_sep.'Promedio'.$csv_sep.'Transito'.$csv_sep.'Sugerido'.$csv_end;

        while($arr = odbc_fetch_array($resultado)){
       

      //ingresa datos en cuadro
          $codigos=$arr["CODIGO"];
          $descripcion=$arr["DESCRIPCION"];
          $desc_tecnica=$arr["DESC_ALT"];
          //$desc_tecnica=preg_replace("[\n|\r|\n\r|\t|\0|\x0B]", "",$desc_tecnica);
          $desc_tecnica = str_replace("\n", "", $desc_tecnica);
          $desc_tecnica = str_replace("\r", "", $desc_tecnica);
          $desc_tecnica = str_replace("\n\r", "", $desc_tecnica);
          $desc_tecnica = str_replace("\t", "", $desc_tecnica);
          $desc_tecnica = str_replace("\0", "", $desc_tecnica);
          $desc_tecnica = str_replace("\x0B", "", $desc_tecnica);
          $existencia=$arr["EXISTENCIA"];
          $costo=number_format($arr["COSTO"],2);
          $venta=number_format($arr["VENTA"],2);
          $costodol=number_format($arr["costodol"],2);
          $proveedor=$arr["proveedor"];
          
         
          
            

           
          $sql4="SELECT codigo, shortname FROM Proveedores WHERE codigo='$proveedor'";

          if(($prov = odbc_exec($id,$sql4)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            $nombre_prove="Local";
            while($arr4 = odbc_fetch_array($prov)){
              $nombre_prove=$arr4["shortname"];
            }

          
          

          $codigobusq =$codigos;

         /// aqui val el valor de estudio, cantidad de meses tambien en moviemientos.php
          $sql2="SELECT (SUM(cantidad)/6) as promedio from hgeneral where codigo = '$codigobusq' 
              AND movimiento not in('Ajuste') AND 
              substring(factura,1,1)='F' AND 
              FECHA >= Dateadd(mm,-6,Getdate())AND  
              FECHA <= GETDATE()";
  
          if(($prom = odbc_exec($id,$sql2)) === false)
              die("Error en Select: ". odbc_errormsg($id));
            
            while($arr2 = odbc_fetch_array($prom)){
              $promedio=number_format($arr2["promedio"],2);
          
            }
         
          $sql3="SELECT SUM(cantidad) as pedido from pedidos where codigo = '$codigobusq'";
          if(($pedi = odbc_exec($id,$sql3)) === false)
              die("Error en Select: ". odbc_errormsg($id));  

            while($arr3 = odbc_fetch_array($pedi)){
              $pedidos=number_format($arr3["pedido"],2);
            }
          
          
          //include("conectarDB.php");
          $existe='no';
            
        
              $suge = ((int)$promedio*(int)$tiempo)- $existencia-$pedidos;
              $reglon++;
              $contador++;
              
              
              $csv.=$codigos.$csv_sep.$descripcion.$csv_sep.$desc_tecnica.$csv_sep.
              $existencia.$csv_sep.$venta.$csv_sep.$costo.$csv_sep.$costodol.$csv_sep.
              $nombre_prove.$csv_sep.$promedio.$csv_sep.$pedidos.$csv_sep.$suge.$csv_end;  
                
              //Generamos el csv de todos los datos  
                  
                
             
            
        }

              if (!$handle = fopen($csv_file, "w")) {  
                  echo "Cannot open file";  
                  exit;  
              }  
              if (fwrite($handle, utf8_decode($csv)) === FALSE) {  
                  echo "Cannot write to file";  
                  exit;  
              }  
              fclose($handle);
    
    
     
    ?>


    
<script>
function move() {
  
}
</script>

 <script type="text/javascript" language="JavaScript">
    document.title = 'Sistema // Promedio';
    var elem = document.getElementById("myBar");
    var width = 1;
    var id = setInterval(frame, 250);
    function frame() {
      if (width >= 100) {
        clearInterval(id);
        document.getElementById('myProgress').style.display = 'none';
        document.getElementById('botones').style.display = 'block';
      } else {
        width++;
        elem.style.width = width + '%';
        document.getElementById("label").innerHTML = width * 1  + '%';

      }

    }
</script>    
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Promedio';
</script>
<script src="js/bootstrap.min.js"></script> 
  </body>
</html>
