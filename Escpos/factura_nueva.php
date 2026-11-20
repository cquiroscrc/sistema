<?php
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
        //$resultado = $_GET['resultado'];
    }
    include "menu.php";

    if(isset($_REQUEST['vendedor']) && !empty($_REQUEST['vendedor'])){
        $vendedor=$_REQUEST['vendedor'];
    }  

    if(isset($_REQUEST['secuencia']) && !empty($_REQUEST['secuencia'])){
      $factura = $_REQUEST['secuencia'];  
    } 

?>
<!DOCTYPE html> 
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Factura</title>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.inputmask.bundle.min.js"></script>
<script src="js/jquery.maskedinput.min.js" type="text/javascript"></script>

<link href="css/bootstrap.min.css" rel="stylesheet">

<link rel="icon" href="favicon.ico">
<style>
    input{
      display: none;
    }

  </style>

 <style type="text/css">

    .botones{
        width:850px;
        height: 75px;
        padding:15px;
        background-color:#eee;
        border:1px solid #444;
    }
    </style>

<script type="text/javascript">

    jQuery(function($){
      
        $("#codigo1").mask("1*** *** ?*** *****");
        $("#codigo2").mask("2***** ?***** ****");
        $("#codigo3").mask("3*?*********");
        $("#codigo4").mask("1  N ?*** ******");
        $("#codigo5").mask("*?** *** ******");
        $("#codigo6").mask("* *?**************");
        $("#codigo7").mask("0*?** *** *** *****");
        $("#codigo9").mask("*?*********");
        
    });
</script>

<script>
        function buscar(){
            var url="factura_consulta.php"
            var user1= document.getElementById("codigo1").value
            var user2= document.getElementById("codigo2").value
            var user3= document.getElementById("codigo3").value
            var user4= document.getElementById("codigo4").value
            var user5= document.getElementById("codigo5").value
            var user6= document.getElementById("codigo6").value
            var user7= document.getElementById("codigo7").value
            var user9= document.getElementById("codigo9").value
            $.ajax({
                type: "POST",
                url:url,
                data:{codigo1:user1,codigo2:user2,codigo3:user3,codigo4:user4,codigo5:user5,codigo6:user6,codigo7:user7,codigo9:user9},
                success: function(datos){

                    $('#tabla').html(datos);
                    document.getElementById("marca").value="";
                    document.getElementById("codigo1").value="";
                    document.getElementById("codigo2").value="";
                    document.getElementById("codigo3").value="";
                    document.getElementById("codigo4").value="";
                    document.getElementById("codigo5").value="";
                    document.getElementById("codigo6").value="";
                    document.getElementById("codigo7").value="";
                    document.getElementById("codigo9").value="9";
                    document.getElementById('codigo1').style.display='none'; 
                    document.getElementById('codigo2').style.display='none'; 
                    document.getElementById('codigo3').style.display='none'; 
                    document.getElementById('codigo4').style.display='none'; 
                    document.getElementById('codigo5').style.display='none'; 
                    document.getElementById('codigo6').style.display='none'; 
                    document.getElementById('codigo7').style.display='none';  
                    document.getElementById('codigo9').style.display='none'; 
                    document.getElementById('marca').style.display='block'; 
                }
            });
        }
     </script>

</head>



<body>
    

   

 <a name="Inicio" id="a"></a> 



     
<?php
require_once("db.php");
//error_reporting(1);




//echo '<br>';
echo '<a href="factura_inicio.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
//echo '<br>';
//echo '&nbsp&nbspVendedor: '.$vendedor.'';
//echo '<br>';
//echo '&nbsp&nbspSecuencia Factura: '.$factura.'';
echo '<br>';
echo '&nbsp&nbsp'.date("d-m-Y", $fecha_sis);
//echo '<a href="#Totales"><img width="48"  height="48" src= "img/downdb.png">Ver Totales</a>';
//echo $datos .'/'.$factura.'-'.$proveedor;
?>


           
            <center><h1><span class="glyphicon glyphicon-search" aria-hidden="true"></span>&nbsp;Consulta</h1>
            
            
            <table WIDTH=400 border =1>
            <tr>
                    
                    <td>1= Volkswagen</td>
                    <td>2= Daihatsu</td>
                    <td>3= Empi</td>
                    <td>4= Grupo Ns</td>
            </tr>
                    <td>5= Familia VW</td>
                    <td>6= Descripcion</td>
                    <td>7= Volvo</td>
                    <td>9= Accesorios</td>
          </tr>
        </table>
            <br>
            <table>
                <tr>
                  <td><input type="text" id="marca" onkeyup="myFunction()" visible="True" autofocus></td><tr/>
                  <td><input id="codigo1" visible="false" autofocus></td><tr/>
                  <td><input id="codigo2" visible="false" autofocus></td><tr/>
                  <td><input id="codigo3" visible="false" autofocus></td><tr/>
                  <td><input id="codigo4" visible="false" autofocus></td><tr/>
                  <td><input id="codigo5" visible="false" autofocus></td><tr/>
                  <td><input id="codigo6" visible="false" autofocus></td><tr/>
                  <td><input id="codigo7" visible="false" autofocus></td><tr/>
                  <td><input id="codigo9" visible="false" autofocus></td><tr/>
                  <tr><td>&nbsp</td></tr>
                  <td><input type="submit" id="boton" onclick="buscar();" value="Buscar Codigo" /></td>
                </tr>
            </table>

         <div id="tabla" style="padding-top:10px;"></div>
          <div id="resultado"></div>
          <br>
        </center>
        <br>



  <?php



@$varsubtotal =0;
@$vartotal =0;
@$cant=0;
$codigobusq = '';
@$noexite=0;

//$sql="SELECT * FROM Pedidos WHERE Factura='$factura' order by ID DESC";  
//    if(($res = odbc_exec($id,$sql)) === false)
//        die("Error en Select: ". odbc_errormsg($id));
      
    $marcador1='';
    $marcador2='inicio';
    $marcador3='inicio';
    $contador=1;   

//echo $sql;

//ingresa nombres de cuadro


   
echo "<center>";
echo "<table>";
echo '<div class="botones">';
echo '<td><a href="factura_busqueda.php?marca=VW&vendedor='.$vendedor.'&factura='.$factura.'"><img width="64"  height="64" src= "img/vw_logo.png"></a></td>';

echo '<td><a href="factura_busqueda.php?marca=Empi&vendedor='.$vendedor.'&factura='.$factura.'"><img width="64"  height="64" src= "img/empi_logo.png"></a></td>';

echo '<td><a href="factura_busqueda.php?marca=Dh&vendedor='.$vendedor.'&factura='.$factura.'"><img width="64"  height="64" src= "img/daihatsu_logo.png"></a></td>';

echo '<td><a href="factura_busqueda.php?marca=Acc&vendedor='.$vendedor.'&factura='.$factura.'"><img width="64"  height="64" src= "img/accesorios_logo.png"></a></td>';
echo '</div>';
echo "</table>";


echo "<br>";

echo "<table border='1'>";
echo "<th>ID</th>";
echo "<th>Factura</th>";
echo "<th>Codigo</th>";
echo "<th>Descripcion</th>";
echo "<th>Cantidad</th>";
echo "<th>Precio</th>";
echo "<th>Total</th>";
echo "<th>Eliminar</th>";
echo "</tr>";


/*
while($arr = odbc_fetch_array($res)){
    //ingresa datos en cuadro
    $id_codigos= $arr["ID"];
    $codigos=$arr["Codigo"];
    $descripcion='NUEVO';
    if ($marcador1=='') {
       $marcador=$codigos;
       $marcador1='inicio';
    }
      
    if ($contador==2) {
        $marcador1=$marcador2;
        $marcador2=$marcador3;
        $marcador3=$codigos;
        
    }
    if ($contador==3) {
        $marcador1=$marcador2;
        $marcador2=$marcador3;
        $marcador3=$codigos;
        $contador=1;
    }
    echo "<td><a href='pedido_codigo_act.php?id=".$id_codigos."&fact=".$factura."&proveedor=".$proveedor."&marcador=".$marcador1."'>".$id_codigos."</a></td>";
    echo "<td>".$arr["Factura"]."</td>";
    echo "<td>".$arr["Proveedor"]."</td>";
    echo "<td>".$arr["Codigo"]."</td>";
   
    $sql_inventario="SELECT DESCRIPCION FROM inventario WHERE CODIGO='$codigos'";  
        if(($res_inve = odbc_exec($id,$sql_inventario)) === false)
            die("Error en Select: ". odbc_errormsg($id));
        while($arr_inve = odbc_fetch_array($res_inve)){
            $descripcion=mb_convert_encoding($arr_inve["DESCRIPCION"],  "ISO-8859-1", "UTF-8");
        }
    if ($descripcion=='NUEVO') {
        $descripcion=' *********** CODIGO NO EXISTE **********';
        $noexite++;
    }
    

    echo '<td><a name="'.$codigos.'" id="a"></a>'.$descripcion.'</td>';
    echo "<td>".$arr["Cantidad"]."</td>";
    echo "<td>".$arr["Precio"]."</td>";
    echo "<td>".$arr["factor"]."</td>";
    echo "<td>".number_format($arr["Sugerido"],2)."</td>";
    echo "<td>".number_format($arr["PrecioAnterior"],2)."</td>";
    echo "<td>".($arr["Cantidad"]*$arr["Precio"])."</td>";
    echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea eliminar este codigo del pedido?\")) 
                        location.href=\"pedido_eliminar.php?id=".$id_codigos."&fact=".$factura."&proveedor=".$proveedor."\";
                        '><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";

    $vartotal=$vartotal+($arr["Cantidad"]*$arr["Precio"]);
    $varsubtotal=$varsubtotal+($arr["Cantidad"]*$arr["Sugerido"]);
    $cant++;
    echo "</tr> ";
    
    $contador++;    			

}
echo "<td></td><td></td><td></td><td><strong> Cantidad  :".$cant."</td>
<td><strong>Codigos nuevos : ".$noexite."</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>" ;
*/

echo '<td></td><td></td><td></td><td><strong>Subtotal: '.number_format($varsubtotal,2).'</td><td><strong>Total : '.number_format($vartotal,2).'</strong></td><td></td><td></td>
<td></td></tr>';



?>
<script>

  document.getElementById('marca').style.display='block'; 
  document.getElementById('boton').style.display='block'; 
  document.getElementById("codigo9").value="9";
  function myFunction() {
    
      if ($(marca).val() == 1){
          document.getElementById('marca').style.display='none'; 
          document.getElementById('codigo1').style.display='block'; 
          document.getElementById('codigo1').focus();
          document.getElementById('codigo2').style.display='none'; 
          document.getElementById('codigo3').style.display='none'; 
          document.getElementById('codigo4').style.display='none'; 
          document.getElementById('codigo5').style.display='none'; 
          document.getElementById('codigo6').style.display='none'; 
          document.getElementById('codigo7').style.display='none';  
          document.getElementById('codigo9').style.display='none'; 
      
      }if ($(marca).val() == 2){
            document.getElementById('marca').style.display='none'; 
            document.getElementById('codigo1').style.display='none'; 
            document.getElementById('codigo2').style.display='block'; 
            document.getElementById('codigo2').focus();
            document.getElementById('codigo3').style.display='none'; 
            document.getElementById('codigo4').style.display='none'; 
            document.getElementById('codigo5').style.display='none'; 
            document.getElementById('codigo6').style.display='none'; 
            document.getElementById('codigo7').style.display='none'; 
            document.getElementById('codigo9').style.display='none';  

      }if ($(marca).val() == 3){
            document.getElementById('marca').style.display='none'; 
            document.getElementById('codigo1').style.display='none'; 
            document.getElementById('codigo2').style.display='none'; 
            document.getElementById('codigo3').style.display='block'; 
            document.getElementById('codigo3').focus();
            document.getElementById('codigo4').style.display='none'; 
            document.getElementById('codigo5').style.display='none'; 
            document.getElementById('codigo6').style.display='none'; 
            document.getElementById('codigo7').style.display='none'; 
            document.getElementById('codigo9').style.display='none';  
      }if ($(marca).val() == 4){
            document.getElementById('marca').style.display='none'; 
            document.getElementById('codigo1').style.display='none'; 
            document.getElementById('codigo2').style.display='none'; 
            document.getElementById('codigo3').style.display='none'; 
            document.getElementById('codigo4').style.display='block';
            document.getElementById('codigo4').focus(); 
            document.getElementById('codigo5').style.display='none'; 
            document.getElementById('codigo6').style.display='none'; 
            document.getElementById('codigo7').style.display='none'; 
            document.getElementById('codigo9').style.display='none';  
      }if ($(marca).val() == 5){
            document.getElementById('marca').style.display='none'; 
            document.getElementById('codigo1').style.display='none'; 
            document.getElementById('codigo2').style.display='none'; 
            document.getElementById('codigo3').style.display='none'; 
            document.getElementById('codigo4').style.display='none';
            document.getElementById('codigo5').style.display='block'; 
            document.getElementById('codigo5').focus(); 
            document.getElementById('codigo6').style.display='none'; 
            document.getElementById('codigo7').style.display='none'; 
            document.getElementById('codigo9').style.display='none';  
      }if ($(marca).val() == 6){
            document.getElementById('marca').style.display='none'; 
            document.getElementById('codigo1').style.display='none'; 
            document.getElementById('codigo2').style.display='none'; 
            document.getElementById('codigo3').style.display='none'; 
            document.getElementById('codigo4').style.display='none';
            document.getElementById('codigo5').style.display='none'; 
            document.getElementById('codigo6').style.display='block'; 
            document.getElementById('codigo6').focus(); 
            document.getElementById('codigo7').style.display='none'; 
            document.getElementById('codigo9').style.display='none';  
      }if ($(marca).val() == 7){
            document.getElementById('marca').style.display='none'; 
            document.getElementById('codigo1').style.display='none'; 
            document.getElementById('codigo2').style.display='none'; 
            document.getElementById('codigo3').style.display='none'; 
            document.getElementById('codigo4').style.display='none';
            document.getElementById('codigo5').style.display='none'; 
            document.getElementById('codigo6').style.display='none'; 
            document.getElementById('codigo7').style.display='block'; 
            document.getElementById('codigo7').focus(); 
            document.getElementById('codigo9').style.display='none';  
            
      }if ($(marca).val() == 9){
            document.getElementById('marca').style.display='none'; 
            document.getElementById('codigo1').style.display='none'; 
            document.getElementById('codigo2').style.display='none'; 
            document.getElementById('codigo3').style.display='none'; 
            document.getElementById('codigo4').style.display='none';
            document.getElementById('codigo5').style.display='none'; 
            document.getElementById('codigo6').style.display='none'; 
            document.getElementById('codigo7').style.display='none'; 
            document.getElementById('codigo9').style.display='block';  
            document.getElementById('codigo9').focus(); 
      }
  }//funcion
</script>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Factura';
</script> 
<script src="js/bootstrap.min.js"></script>
</body>
</html>