<?php 
    require("../../../modelos/conexion.php");
    require("../../../controladores/utilitaria.controlador.php");
    require("../../../modelos/utilitaria.modelo.php");

    date_default_timezone_set("America/Costa_Rica");
    header("Content-type: text/html; charset=utf-8");
    
    if(isset($_REQUEST['nombre']) && !empty($_REQUEST['nombre'])){
          $nombre = strtoupper($_REQUEST['nombre']);
    }
    if(isset($_REQUEST['monto']) && !empty($_REQUEST['monto'])){
          $monto = $_REQUEST['monto']; 
          $V=new EnLetras();
        $monto_letras= $V->ValorEnLetras($monto," ");     
    }
    if(isset($_REQUEST['detalle']) && !empty($_REQUEST['detalle'])){
          $detalle = strtoupper($_REQUEST['detalle']);      
    }
    if(isset($_REQUEST['cuentabancaria']) && !empty($_REQUEST['cuentabancaria'])){
          $cuenta_bancaria = $_REQUEST['cuentabancaria'];      
    }
    if(isset($_REQUEST['cuentacontable']) && !empty($_REQUEST['cuentacontable'])){
          $cuenta_contable = $_REQUEST['cuentacontable'];      
    }
    if(isset($_REQUEST['fecha']) && !empty($_REQUEST['fecha'])){
          $fecha1 = $_REQUEST['fecha']; 
        setlocale(LC_ALL, "es_ES", 'Spanish_Spain', 'Spanish');
        $fecha=iconv('ISO-8859-2', 'UTF-8', strftime("%d %B %Y", strtotime($fecha1)));     
    }
    if(isset($_REQUEST['cheque']) && !empty($_REQUEST['cheque'])){
          $cheque = $_REQUEST['cheque'];      
    }
    $sql_cuenta="SELECT * FROM tbl_CuentasC WHERE  id='$cuenta_contable'";
        
        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_cuenta);
        foreach ($ejecutar as $key => $arr) {
            $cuenta_contable=$arr["id"].'-'.$arr["Descripcion"];
        }
    

 ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Imprimir Cheque</title>
        <script type="text/javascript">
            function imprimirDocumento(numeroDeCopias) {
                var count = 0;
                while (count < numeroDeCopias) {
                    window.print(0);
                    count++;
                }
            }
        </script>
    </head>
    <body>
        <br>
        <table>
            <tr>
                <td style="width:350px"></td>
                
                <td><?php echo $fecha; ?></td>

            </tr>
        </table>
        <?php
        //echo $cuenta_bancaria;
        if ($cuenta_bancaria=='100-01-000-208040-6' || $cuenta_bancaria=='100-02-201-000117-0') {
            //echo '<script language="javascript">alert("juas");</script>';
        }else{
            echo "<br>";
        }


        ?>
        <!--        <br>-->

        <table>
            <tr>
               
                <td style="width:70px"></td>
                <td style="width:390px"><?php echo $nombre; ?></td>
                <td style="width:15px"></td>
                
                <td><?php echo number_format($monto,2); ?></td>
            </tr>
            <tr>
                <td style="width:70px"></td>
                <td><font size=2><?php echo $monto_letras; ?></td>
            </tr>
        </table>
        
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <center>  
        <table>
            <tr>
                <th>DETALLE DEL CHEQUE</th>
            </tr>
        </table>
        </center>
        <hr>
        <table>
            <tr>
                <td>Fecha:</td>
                <td><?php echo $fecha; ?></td>
            </tr>
            <tr>
                <td>Cheque #:</td>
                <td><?php echo $cheque; ?></td>
            </tr>
            <tr>
                <td>Paguese a :</td>
                <td><?php echo $nombre; ?></td>
            </tr>
            <tr>
                <td>Monto:</td>
                <td><?php echo number_format($monto,2); ?></td>
            </tr>
            <tr>
                <td>Monto Letras:</td>
                <td><?php echo $monto_letras; ?></td>
            </tr>
            <tr>
                <td>Cuenta:</td>
                <td><?php echo $cuenta_bancaria; ?></td>
            </tr>
            <tr>
                <td>Cuenta Cont:</td>
                <td><?php echo $cuenta_contable ?></td>
            </tr>
        </table>
        <center>
        <table>
            <tr>
                <th>DETALLE DE LAS FACTURAS</th>
            </tr>
        </table>
        </center>
        <hr>
        <table>
            <tr>
                <?php echo $detalle; ?>
                
            </tr>
        </table>
        <hr>
        <br>
        <br>
        <br>
       
        <table>
            <tr>
                <td>____________________________</td>
                <td></td>
                <td>____________________________</td>
                <td></td>
                <td>_____________________________</td>
            </tr>
            <tr>
                <td style="text-align: center">Nombre</td>
                <td></td>
                <td style="text-align: center">Cedula</td>
                <td></td>
                <td style="text-align: center">Firma</td>
            </tr>
        </table>
        <br>
        <br>
        <hr/>
        
        <a style="color:red;" onclick="imprimirDocumento('1')"/>Archivo</a>
        <script src="../../js/analytics.js" type="text/javascript"></script>
    </body>
</html>
