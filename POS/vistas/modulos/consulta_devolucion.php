<?php 
    require("../../modelos/conexion.php");
    require("../../controladores/utilitaria.controlador.php");
    require("../../modelos/utilitaria.modelo.php");

if(isset($_REQUEST['codigo']) && !empty($_REQUEST['codigo'])){
  $codigo = $_REQUEST['codigo'];  
}

$codigo ='F-'.sprintf("%014s", $codigo);

$sql="DELETE FROM tmp_devol WHERE nfac = '$codigo'";    
$res_busqueda = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql);

    //// *************** Incluye la consulta ******************
    
$consulta="SELECT h.Factura,h.fecha,h.codigo, h.cantidad, h.precio, i.DESCRIPCION,  f.subtotal,f.impuesto,f.descuento,f.total,f.Tipo,f.estado,f.Nombre,f.adicional, v.codigo AS codven, v.nombre AS nombreven, c.codigo AS codclie, c.nombre AS nomclie FROM hgeneral h, inventario i, hfacturas f, Vendedores v, clientes c WHERE h.codigo = i.CODIGO AND h.Factura = f.nfac AND h.codcliente = c.codigo AND FORMAT(cast( h.vendedor as numeric),'###')= FORMAT(cast( v.codigo as numeric),'###') AND h.Factura = '$codigo'";    

$entro="si";

$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);
if ($ejecutar==False) {
  $entro="no";
}
?>
<style>
table {
  border-collapse: collapse;
  width: 100%;
}

th, td {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {background-color: #f2f2f2;}
</style>

<center>  

<h3 id="consecutivoFac"><?php echo $codigo; ?></h3>  
<?php if ($entro=="si") {
          # code...
         ?>
<table  border =1 id="devfactura" class="display" style="width:100%">

        <thead >
            <tr class="modal-title">
              <th>Codigo</th>
              <th>Descrpcion</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <th>Total</th>
              <th>Cantidad</th>
              <th>Acción</th>
            </tr>
        </thead>
        <tbody id="dev_list">
          
          <?php


             

          foreach ($ejecutar as $key => $value) {
              
            $sub=$value["subtotal"];
            $descuento=$value["descuento"];
            $impuesto=$value["impuesto"];
            $total=$value["total"];
            $estado=$value["estado"];
            $nombre=$value["Nombre"];
            $adicional=$value["adicional"];
            $codcliente=$value["codclie"];
            $nomclie=$value["nomclie"];
            $codven=$value["codven"];
            $nombreven=$value["nombreven"];
            $fecha=$value["fecha"];
            $codproduct =$value["codigo"];  
            echo '<tr>
                    
                    <td id="coddev">'.$codproduct.'</td>

                    <td>'.$value["DESCRIPCION"].'</td>
                    
                    <td>'.number_format($value["cantidad"],2).'</td>

                    <td id="preciodev">'.$value["precio"].'</td>

                    <td>'.number_format($value["cantidad"]*$value["precio"],2).'</td>

                    <td><input type="number" id="devcantidad" min="1" max="'.$value["cantidad"].'" value="'.$value["cantidad"].'"></td>';
                    ?>
                    <td><button id="btnDevCantidad" type="button" class="btn btn-danger boton">Devolver</button></td>
                  </tr>
                  <?php 
                  
          }

        ?>
   
        </tbody>
        
    </table>
    <div>
          
   
        <h3>Cliente: <?php echo $codcliente." - ".$nomclie; ?></h3>
        <h3>Nombre: <?php echo $nombre; ?></h3>
        
        <table border = 1>
         <?php 
              echo "<tr> ";
              echo "<td><b>Fecha</b></td> ";
              echo "<td><b>Subtotal</b></td>";
              echo "<td><b>Descuento</b></td>";
              echo "<td><b>Impuesto</b></td> ";
              echo "<td><b>Total</b></td> ";
              echo "<td><b>Vendedor</b></td> ";
              echo "<td><b>Estado</b></td> ";
              echo "</tr> ";


               echo '<tr>

                    <td>'.$fecha.'</td>

                    <td>'.number_format($sub,2).'</td>
                    
                    <td>'.number_format($descuento,2).'</td>

                    <td>'.number_format($impuesto,2).'</td>

                    <td>'.number_format($total,2).'</td>

                    <td>'.$codven.'</td>

                    <td>'.$estado.'</td>
                    

                  </tr>';
                  $totalimp=$impuesto/($sub-$descuento);
                  $totaldec=$descuento/$sub;

          ?>
        </table>

  <br>
  <hr>
      <table border="1" cellspacing="0" cellpadding="0" id="dev-producto">
            <thead>
                <tr>
                    <th class="text-left">CODIGO</th>
                    <th class="text-right">PRECIO</th>
                    <th class="text-right">CANTIDAD</th>
                    <th class="text-right">TOTAL</th>
                </tr>
            </thead>
            <tbody id="dev_list">
                
                
                
            </tbody>
            <tfoot id="dev_totales">
                <tr>
                    <td colspan="2"></td>
                    <td colspan="1"><b>SUBTOTAL</b></td>
                    <td style="color:red;" id="dev_subtotal"><b>0.00</b></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="1"><b>DESCUENTO</b></td>
                    <td style="color:red;" id="dev_descuento"><b>0.00</b></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="1"><b>IMPUESTO</b></td>
                    <td style="color:red;" id="dev_impuesto"><b>0.00</b></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="1"><b>TOTAL</b></td>
                    <td style="color:red;" id="dev_total"><b>0.00</b></td>
                </tr>
            </tfoot>
      </table>   
        
        
        
    </div>
  
</center>


<script>
  $(document).ready(function(){
        subtotalact=0;
        descuentoact=0;
        impuestoact=0;
        totalact=0;
        
        $(".boton").click(function(){

        var cantidad="";
        var codigo="";
        var precio="";
        var sub="";

        // Obtenemos todos los valores contenidos en los <td> de la fila
        // seleccionada
        $(this).parents("tr").find("#coddev").each(function(){
        codigo+=$(this).html();
        });
        $(this).parents("tr").find("#preciodev").each(function(){
        precio+=$(this).html();
        });
        $(this).parents("tr").find("#devcantidad").each(function(){
        cantidad+=$(this).val();
        sub=Number(precio)*Number(cantidad);
        consecutivo=$('#consecutivoFac').text();
        });
         // Agregarlos a BD igual que prefactura
         
        var html= '<tr>\
                  <td style="color:blue;" class="codigo"><b>'+codigo+'</b></td>\
                  <td style="color:blue;" class="unit"><b>'+Number(precio)+'</b></td>\
                  <td style="color:blue;" class="qty"><b>'+cantidad+'</b></td>\
                  <td style="color:blue;" class="total"><b>'+sub+'</b></td>\
                  </tr>';
                  $('#dev-producto').append(html);

        
        var vardesc = <?php echo $totaldec;?>          
        var varimpues = <?php echo $totalimp;?>  

        IngresarDevolucion(consecutivo,codigo,cantidad,precio,vardesc,varimpues);
        
        subtotalact=subtotalact+sub;
        descuentoact = subtotalact *vardesc;  
        impuestoact = (subtotalact-descuentoact)*varimpues;
        totalact = subtotalact-descuentoact+impuestoact;
        precio=number_format(precio,2,'.',',');
        sub=number_format(sub,2,'.',',');          

        $('#dev_subtotal').text(subtotalact.toFixed(2));
        $('#dev_descuento').text(descuentoact.toFixed(2));
        $('#dev_impuesto').text(impuestoact.toFixed(2));
        $('#dev_total').text(totalact.toFixed(2));
        $(this).prop('disabled',true);
        swal({
              type: "success",
              title: "El codigo se ha agregado a la devolución correctamente",
              showConfirmButton: true,
              confirmButtonText: "Cerrar"
              }).then(function(result){
                  
              });
        //alert(codigo+' agregado '+cantidad+' cantidad');
      });
  });

  function IngresarDevolucion(consecutivo,codigo,cantidad,precio,descuento,impuesto){
    var param = {
                      nfac: consecutivo,
                      codigo: codigo,
                      cantidad: cantidad,
                      precio: precio,
                      descuento: descuento,
                      impuesto: impuesto,
                  };

              url="ajax/insertar_devolucion.ajax.php";
              data="";
              data=param;

              myAjaxHH(url,data)
              .done( function(data) {
                console.log(data);
              }); 

  }  

</script>

<?php 
  }
 ?>