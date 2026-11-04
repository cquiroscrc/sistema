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

    <script language="javascript">
    function doSearch()
    {
      var tableReg = document.getElementById('datos');
      var searchText = document.getElementById('searchTerm').value.toLowerCase();
      var cellsOfRow="";
      var found=false;
      var compareWith="";
 
      // Recorremos todas las filas con contenido de la tabla
      for (var i = 1; i < tableReg.rows.length; i++)
      {
        cellsOfRow = tableReg.rows[i].getElementsByTagName('td');
        found = false;
        // Recorremos todas las celdas
        for (var j = 0; j < cellsOfRow.length && !found; j++)
        {
          compareWith = cellsOfRow[j].innerHTML.toLowerCase();
          // Buscamos el texto en el contenido de la celda
          if (searchText.length == 0 || (compareWith.indexOf(searchText) > -1))
          {
            found = true;
          }
        }
        if(found)
        {
          tableReg.rows[i].style.display = '';
        } else {
          // si no ha encontrado ninguna coincidencia, esconde la
          // fila de la tabla
          tableReg.rows[i].style.display = 'none';
        }
      }
    }
  </script>
 
  <style>
    #datos  {border:1px solid #ccc;padding:10px;}
    #datos tr:nth-child(even) {background:#ccc;}
    #datos td {padding:5px;}
  </style>


 
<body>
  <h1>Buscar Telefono</h1>
  <form>
    Telefono a buscar <input id="searchTerm" type="text" onkeyup="doSearch()" />
  </form>
 <br>
  <button type="button" onclick="window.open('', '_self', ''); window.close();">Cerrar</button>
  
  <a style="color:black" href="#agregar"><button>Agregar</button></a>


  <p>
    <table id="datos" border="1">
      <tr>
        <th>id</th><th>Nombre</th><th>Telefono</th><th>Correo</th>
      </tr>
      <tr>
        <?php 
        require("db.php");
        $sql = "SELECT * from tbl_TEL_Numeros order by Descripcion";

        if(($resultado = odbc_exec($id,$sql)) === false)
          die("Error en Select: ". odbc_errormsg($id));

        while($arr = odbc_fetch_array($resultado)){
                  
            $id = $arr['id'];
            $nombre = $arr['Descripcion'];
            $telefono = $arr['Numero'];
            $correo = $arr['Correo'];
            
            echo '<td>'.$id.'</td><td>'.$nombre.'</td><td>'.$telefono.'</td><td>'.$correo.'</td>';       
            echo '<tr>';
        }

         ?>
       
    </table>
  </p>
  <table border="1" id="agregar" name ="agregar">
  <td>
  <form  action="factura_telefono_agregar.php" method="post">
    <div align="left">
    <p>Nombre</p>
    <input type="text" name="nombre"><br>
    <p>Telefono</p>
    <input type="text" name="telefono"><br>
    <p>E-mail</p>
    <input type="text" name="correo"><br>
    </div>
    <br>
    <button type="submit">Agregar</button>
    
  </form>
  </td>
  </table>
  <br>
  <button type="button" onclick="window.open('', '_self', ''); window.close();">Cerrar</button>
</body>
</html>





</body>
</html>