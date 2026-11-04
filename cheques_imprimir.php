<?php 
session_start();
include "menu.php";
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente}
$documento=0;
if((isset($_REQUEST['documento']))and(!empty($_REQUEST['documento']))){
        $documento = $_REQUEST['documento'];
    }

$tipo=1;
 ?>


<html>
<head>
<style type="text/css">
@media print {
    div,a {display:none}
    .ver {display:block}
    .nover {display:none}
    p { font: 16px/18px sans-serif }
    .mini{
        font: 10px/12px sans-serif 
    }
    #mini{
        font: 10px/12px sans-serif 
    }

}


@page{
  
  margin: 1%;
}
h5{
    font-size:100%    
}


</style>
<script>
function impre(num) {
    document.getElementById(num).className="ver";
    print();
    document.getElementById(num).className="nover";
}
</script>
</head>

<body>
    <a href="consulta_factura.php"><img width="48"  height="48" src= "img/back.png">Volver</a>

    <a href="#" onclick="impre('uno');return false"><img width="48"  height="48" src= "img/imprime.png">Imprime</a>
    
<div id="imprimeme">

        <p>___________________________________________________________________________________________________________I</p>
        <br>
        <br>
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;25/02/2016</p>
        
        <p>HERNAN HERNANDEZV VIQUEZ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;125,000.00</p>
        
        <p>CIENTO VEINTICINCO MIL COLONES</p>
</div>
<button onclick="imprimir();">
  IMPRIMIR
</button>
<script>
    function imprimir(){
      var objeto=document.getElementById('imprimeme');  //obtenemos el objeto a imprimir
      var ventana=window.open('','_blank');  //abrimos una ventana vacía nueva
      ventana.document.write(objeto.innerHTML);  //imprimimos el HTML del objeto en la nueva ventana
      ventana.document.close();  //cerramos el documento
      ventana.print();  //imprimimos la ventana
      ventana.close();  //cerramos la ventana
    }

</script>

<div id="uno">

    <p>___________________________________________________________________________________________________________I</p>
    <br>
    <p>___________________________________________________________________________________________________________Linea Fecha</p>
    <br>
    <p>___________________________________________________________________________________________________________Linea Monto</p>
    <br>
    <p>___________________________________________________________________________________________________________Linea Letras</p>
</div>



<script src="js/bootstrap.min.js"></script>
</body>
</html>

