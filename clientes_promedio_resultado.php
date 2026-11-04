<?php
session_start();
include "menu.php";
?>
<html>
  <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Clientes</title>
        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script> <!--  Imagenes fancy -->
        <link rel="stylesheet" href="css/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" /> <!--  Imagenes fancy -->
        <script type="text/javascript" src="js/jquery.fancybox.pack.js?v=2.1.5"></script> <!--  Imagenes fancy -->

        <link href='http://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/anular.css">
        
        <link rel="icon" href="favicon.ico">
    </head>
    <body>
<?php
    require("db.php");

if(isset($_REQUEST['estudio']) && !empty($_REQUEST['estudio'])){
        $estudio = $_REQUEST['estudio'];      
    } 

//afiliciacion
$mes=date("m");  
$ante_year=date("Y")-1; 
$reg=0;

$sql = "SELECT * from clientes where codigo < '9999' and month(afiliacion)= '$estudio' AND comentario <> '**DESACTIVADO**'";

 if(($resultado = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));

    echo '<h1>Listado de Clientes Frecuentes</h1>';
    ?>
        <a href="#" onclick="javascript:imprSelec('muestra');function imprSelec(muestra)
        {var ficha=document.getElementById(muestra);var ventimp=window.open(' ','popimpr');
        ventimp.document.write(ficha.innerHTML);ventimp.document.close();ventimp.print();
        ventimp.close();};"><img width="48"  height="48" src= "img/imprime.png">Imprime Listado</a>
    <?php 
    
    echo '<br>';
    echo '<br>';
    echo '<br>';
    //echo $estudio; 
    //echo '<br>';
    //echo $sql;
    echo "<div id='muestra'>";
    echo '<center><table border =1>
            <tr class="encabezadoRegistro">
                <th>Reg</th>
                <th>Id</th>
                <th>Nombre       /'.$ante_year.'/'.$estudio.'</th>
                <th>Telefono</th>
                <th>Celular</th>
                <th>Correo</th>
                <th>Afiliación</th>
                <th>Saldo</th>';
        echo '</tr>';
    
         while($arr = odbc_fetch_array($resultado)){
                 
            $id_cliente = $arr['codigo'];
            $datos=$arr['codigo'];
            $saldo=0;
            
            $dia=1;
            //$sql_saldo="SELECT * FROM cxc WHERE IDCliente='$datos'";  
            $sql_saldo="SELECT * FROM cxc WHERE IDCliente='$datos' AND Fecha >='".$ante_year."-".$estudio."-01 00:00:00.000' ORDER BY Fecha";  
            //echo '<br>';
            //echo $sql_saldo;

                if(($res_saldo = odbc_exec($id,$sql_saldo)) === false)
                    die("Error en Select: ". odbc_errormsg($id));
                while($arr_saldo = odbc_fetch_array($res_saldo)){
                    $saldo=$saldo+$arr_saldo["Total"];
                }
            if ($saldo<99999) {
                $reg++; 
                $nombre = $arr['nombre'];
                $telefono = $arr['telefono'];
                $direccion = $arr['direccion'];
                $cedula = $arr['cedula'];
                $celular=$arr['celular'];
                $correo=$arr['correo'];
                $afiliacion=strtotime($arr['afiliacion']);
                echo '<tr>';
                echo '<td>'.$reg.'</td><td>'.$id_cliente.'</td><td>'.$nombre.'</td><td>'.$telefono.'</td><td>'.$celular.'</td>'; echo '<td>'.$correo.'</td><td>'.date("d-m-Y", $afiliacion).'</td><td>'.$saldo.'</td>';       
               
                    
                }    
            
        }
    
    echo '</table></center>';
    echo '</div>'

?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Saldo Clientes';
</script>
<script src="js/bootstrap.min.js"></script> 
    </body>
</html>