<?php
session_start();
include "menu.php";



?>
<html>
  <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Usuarios</title>
        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script> <!--  Imagenes fancy -->
        <link rel="stylesheet" href="css/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" /> <!--  Imagenes fancy -->
        <script type="text/javascript" src="js/jquery.fancybox.pack.js?v=2.1.5"></script> <!--  Imagenes fancy -->
        <script src="js/clipboard.min.js"></script>
        <link href='http://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/anular.css">        
        <link rel="icon" href="favicon.ico">

<style type="text/css">
  .btn{
    text-decoration: none;
    padding: 5px;
    color: #ffffff;
    background-color: #1883ba;
    border-radius: 6px;
    border: 2px solid #0016b0;
  }
</style>
       

    </head>
    <body>
        
<?php
    require("db.php");

    if((isset($_POST['marca']))and(!empty($_POST['marca']))){
        $marca = ($_POST['marca']);
    }
    


$sql = "SELECT * from VAG where Marca= '$marca' order by Marca,Tipo, Year,Placa";

 if(($resultado = odbc_exec($id,$sql)) === false)
    die("Error en Select: ". odbc_errormsg($id));

    echo '<h1>Listado de Carros</h1>';
    echo '<a href="carros_elegir.php"><img width="48"  height="48" src= "img/back.png">Volver</a> ';
    echo '<a href="carros_ingreso.php"><img width="48"  height="48" src= "img/car-add.png">&nbsp;&nbsp;Agregar Carro</a>';        
    echo '<br>';
    echo '<br>';
    echo '<br>';
    echo '<center><table border =0>
            <tr class="encabezadoRegistro">
                <th>Marca</th>
                <th>Tipo</th>
                <th>Año</th>
                <th>Placa</th>
                <th>VIN</th>
                <th></th>
                <th>Motor</th>
                <th>Adicional</th>
                <th>Editar</th>';
    echo '</tr>';
    
         while($arr = odbc_fetch_array($resultado)){
                  
            $marca = $arr['Marca'];
            $tipo = $arr['Tipo'];
            $year = $arr['Year'];
            $placa = $arr['Placa'];
            $vin=$arr['Vin'];
            $motor=$arr['Motor'];
            $tipo2=$arr['Tipo2'];
            echo '<tr>';
            echo '<td>'.$marca.'</td><td>'.$tipo.'</td><td>'.$year.'</td><td>'.$placa.'</td><td id="bar">'.$vin.'</td>';
            ?>
            <td><button class="btn" data-clipboard-text="<?php echo $vin; ?>">Copiar VIN</button>
            </td>
            <?php 
            echo'<td>'.$motor.'</td><td>'.$tipo2.'</td>';       
            echo '<td><a href="carros_actualizar.php?id='.$placa.'"><img width="48"  height="48" src= "img/editar.png"></a></td>';
            //echo '<td><a href="usuarios_eliminar.php?id='.$id_usuario.'"><img width="48"  height="48" src= "img/eliminar.png"></a></td></tr>';        
            //echo "<td><a class = 'echo_link' href='#' onclick='if(confirm(\"Desea eliminar este Cliente?\")) 
            //            location.href=\"clientes_eliminar.php?id=".$id_cliente."\";
            //            '><img width='48'  height='48' src= 'img/eliminar.png'></a></td>";
        }
    
    echo '</table></center>';

?>


<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Carros';

 
    
</script>

<script>
    var clipboard = new ClipboardJS('.btn');

    clipboard.on('success', function(e) {
        console.log(e);
    });

    clipboard.on('error', function(e) {
        console.log(e);
    });
    </script>
<script src="js/bootstrap.min.js"></script> 
    </body>
</html>