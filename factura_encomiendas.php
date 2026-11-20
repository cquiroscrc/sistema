<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Consulta telefonos</title>
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
  <h1>Buscar Encomiendas</h1>
  <form method="GET" action="factura_encomiendas.php">
    <?php 
       date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
      require("db.php");

        $fecha = date('d-m-Y');
        $fecha1 = strtotime ( '-1 day' , strtotime ( $fecha ) ) ;
        $fecha1 = date ( 'd-m-Y' , $fecha1 );
        $fecha2 = strtotime ( '-2 day' , strtotime ( $fecha ) ) ;
        $fecha2 = date ( 'd-m-Y' , $fecha2 );
        $fecha3 = strtotime ( '-3 day' , strtotime ( $fecha ) ) ;
        $fecha3 = date ( 'd-m-Y' , $fecha3 );
        $fecha4 = strtotime ( '-4 day' , strtotime ( $fecha ) ) ;
        $fecha4 = date ( 'd-m-Y' , $fecha4 );
        $fecha5 = strtotime ( '+1 day' , strtotime ( $fecha ) ) ;
        $fecha5 = date ( 'd-m-Y' , $fecha5 );

     ?>

    <select  id="date1" name="date1">
            <optgroup >
              <?php 
              echo '<option value="'.$fecha.'">'.$fecha.'</option>';
              echo '<option value="'.$fecha1.'">'.$fecha1.'</option>';
              echo '<option value="'.$fecha2.'">'.$fecha2.'</option>';
              echo '<option value="'.$fecha3.'">'.$fecha3.'</option>';
              echo '<option value="'.$fecha4.'">'.$fecha4.'</option>';
              echo '<option value="'.$fecha5.'">'.$fecha5.'</option>';
              
               ?>
            </optgroup>
          </select>
          <td><button  type="submit">Buscar</button></td>     
  </form>
 <br>
  <button type="button" onclick="window.open('', '_self', ''); window.close();">Cerrar</button>

    <table id="datos" border="1">
      <tr>
        <th>id</th><th>Encomienda</th>
      </tr>
      <tr>
        
        <?php 
        //$date1 = date('m-d-Y 00:00:00.000');
        if(isset($_REQUEST['date1']) && !empty($_REQUEST['date1'])){
          $date1 = $_REQUEST['date1'];      
          
        $date1=strtotime($date1);
        $date1= date ( 'm-d-Y 00:00:00.000' , $date1 );
        $sql = "SELECT * FROM tbl_Egresos WHERE Tipo=5 AND Fecha = '".$date1."'" ;
        
        if(($resultado = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));
        $item=1;
        while($arr = odbc_fetch_array($resultado)){
                  
            $id = $arr['Documento'];
            $nombre = $arr['Descripcion'];
            $telefono = $arr['Monto'];
            
            echo '<td>'.$item.'</td>';
            echo '<td>'.$id.'-'.$nombre.'-'.$telefono.'</td>';       
            echo '<tr>';
            $item++;
        }
        }
         ?>
       
    </table>
  </p>
  
</body>
</html>





</body>
</html>