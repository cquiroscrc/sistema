<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Consulta Mandados</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="icon" href="img/favicon.ico">

    <style>
      /* Remove the navbar's default margin-bottom and rounded borders */ 
      
      .navbar {
      margin-bottom: 4px;
      border-radius: 0;
      }
      /* Add a gray background color and some padding to the footer */
      footer {
      background-color: #4d7185;
      padding: 25px;
      }
      .carousel-inner img {
      width: 100%; /* Set width to 100% */
      margin: auto;
      min-height:200px;
      }
      .navbar-brand
      {
      padding:5px 40px;
      }
      .navbar-brand:hover
      {
      background-color:#ffffff;
      }
      /* Hide the carousel text when the screen is less than 600 pixels wide */
      @media (max-width: 600px) {
      .carousel-caption {
      display: none; 
      }
      }
    </style>

    
 
  <style>
    #datos  {border:1px solid #ccc;padding:10px;}
    #datos tr:nth-child(even) {background:#ccc;}
    #datos td {padding:5px;}
  </style>


 
<body>
  <h1>Ver Mandados</h1>
  
 <br>
  <button type="button" onclick="window.open('', '_self', ''); window.close();">Cerrar</button>
  <br>
    <table id="datos" border="1">
      <tr>
        <th>id</th><th>Fecha</th><th>Factura</th><th>Monto</th><th>Cantidad</th><th>Envia</th><th>Motorizado</th>
      </tr>
      <tr>
        
        <?php 
        date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
        require_once("db.php");
        $date1= date ( 'm-d-Y');

        $sql="SELECT * FROM mandados WHERE fecha BETWEEN '$date1 00:00:00.000' AND '$date1 23:59:59.000'  
        order by fecha ASC";  

      
        
        
        if(($resultado = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));
        $item=1;
        while($arr = odbc_fetch_array($resultado)){
                  
           $fecha = $arr['fecha'];
           $factura = $arr['factura'];
           $monto = $arr['monto'];
           $cantidad= $arr["cantidad"];
           $envia=$arr["envia"];
           $motorizado=$arr["motorizado"];
            
            echo '<td>'.$item.'</td>';
            echo '<td>'.$fecha.'</td>';       
            echo '<td>'.$factura.'</td>';       
            echo '<td>'.$monto.'</td>';       
            echo '<td>'.$cantidad.'</td>';
            echo '<td>'.$envia.'</td>';
            echo '<td>'.$motorizado.'</td>';                     
            echo '<tr>';
            $item++;
        }
       
         ?>
       
    </table>
  </p>
  
</body>
</html>





</body>
</html>