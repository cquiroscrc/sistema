<?php
//session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente

$date_now = date('Y-m-d');
$date_future = strtotime('-5 day', strtotime($date_now));
$date_future = date('Y-m-d', $date_future);
$Fecha1=$date_future." 00:00:00.000";
$Fecha2=$date_now." 23:59:59.999";

$sql_Compras= "SELECT DISTINCT f.Factura , f.Proveedor , p.nombre, f.Fecha 
    FROM hmcompras f, Proveedores p where Fecha BETWEEN '$Fecha1' 
    AND '$Fecha2' AND p.codigo = f.Proveedor 
    ORDER BY Fecha";

$sql_Pedidos = "SELECT DISTINCT p.factura, p.Proveedor, e.nombre FROM Pedidos p, Proveedores e WHERE p.Proveedor = e.codigo"
?>

<script type="text/javascript" src="../BrowserPrint-2.0.0.75.min.js"></script>
<script type="text/javascript">
var selected_device;
var devices = [];


// var encabezado='^XA^CF0,36^FO120,50^FDREPUESTOS LOS PATICAS^FS';
// var codigo='^FO160,90^FD11H0 413 031 CEU^FS';
// var descripcion1='^FO55,130^CF0,25^FDAMORTIGUADOR DELANTERO A2-A3 GOLF/JETTA^FS';
// var descripcion2='^FO55,160^FD88-98 POLO CLASSIC 00/02 CADDY 00/04/FAW^FS';
// var datos='^FO150,193^CF0,23^FD29070-842428-140219-2-06B05^FS^XZ';


function setup()
{
    //Get the default device from the application as a first step. Discovery takes longer to complete.
    BrowserPrint.getDefaultDevice("printer", function(device)
            {
        
                //Add device to list of devices and to html select element
                selected_device = device;
                devices.push(device);
                var html_select = document.getElementById("selected_device");
                var option = document.createElement("option");
                option.text = device.name;
                html_select.add(option);
                
                //Discover any other devices available to the application
                BrowserPrint.getLocalDevices(function(device_list){
                    for(var i = 0; i < device_list.length; i++)
                    {
                        //Add device to list of devices and to html select element
                        var device = device_list[i];
                        if(!selected_device || device.uid != selected_device.uid)
                        {
                            devices.push(device);
                            var option = document.createElement("option");
                            option.text = device.name;
                            option.value = device.uid;
                            html_select.add(option);
                        }
                    }
                    
                }, function(){alert("Error getting local devices")},"printer");
                
            }, function(error){
                alert(error);
            })
}

function getConfig(){
    BrowserPrint.getApplicationConfiguration(function(config){
        alert(JSON.stringify(config))
    }, function(error){
        alert(JSON.stringify(new BrowserPrint.ApplicationConfiguration()));
    })
}


function writeToSelectedPrinter(dataToWrite)
{   
    selected_device.send(dataToWrite, undefined, errorCallback);
    //console.log("Imprime");
}
var readCallback = function(readData) {
    if(readData === undefined || readData === null || readData === "")
    {
        alert("No Response from Device");
    }
    else
    {
        alert(readData);
    }
    
}
var errorCallback = function(errorMessage){
    alert("Error: " + errorMessage);    
}
function readFromSelectedPrinter()
{

    selected_device.read(readCallback, errorCallback);
    
}
function getDeviceCallback(deviceList)
{
    alert("Devices: \n" + JSON.stringify(deviceList, null, 4))
}

function sendImage(imageUrl)
{
    url = window.location.href.substring(0, window.location.href.lastIndexOf("/"));
    url = url + "/" + imageUrl;
    selected_device.sendUrl(url, undefined, errorCallback)
}
function sendImageHttp(imageUrl)
{
    url = window.location.href.substring(0, window.location.href.lastIndexOf("/"));
    url = url + "/" + imageUrl;
    url = url.replace("https", "http");
    selected_device.sendUrl(url, undefined, errorCallback)
}
function onDeviceSelected(selected)
{
    for(var i = 0; i < devices.length; ++i){
        if(selected.value == devices[i].uid)
        {
            selected_device = devices[i];
            return;
        }
    }
}
window.onload = setup;
</script>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Imprimir Etiquetas
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Imprimir Etiquetas</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

          <div class="box-header with-border">
            Impresora Seleccionada: <select id="selected_device"  onchange=onDeviceSelected(this);></select> <input type="button" value="Cambiar" onclick="changeDevice();"> <br/><br/>
            <!-- <input class="btn btn-primary" type="button" value="Imprimir etiqueta" onclick="writeToSelectedPrinter(encabezado+codigo+descripcion1+descripcion2+datos)"><br/><br/> -->
             <div class="col-sm-4">  
              <div class="box-body">
                    <select   id="seleccionarfactura" name="seleccionarfactura" required>
                    <option value="">SELECCIONAR FACTURA</option>
                    <?php 
                    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_Compras);
                    foreach ($ejecutar as $key => $value) {
                        echo '<option value="'.$value["Factura"].'">'.$value["Factura"].' - '.$value["nombre"].'</option>';
                    }
                     ?>
                      </select></i>
                      <div id="codigofactura" style="display:none;"></div>
                    <button class="btn btn-primary" onclick="CargarFactura('compra')">Cargar</button>
              </div>
        
        </div>
        <div class="col-sm-4">  
              <div class="box-body">
                    <select   id="seleccionarpedido" name="seleccionarpedido" required>
                    <option value="">SELECCIONAR PEDIDOS</option>
                    <?php 
                    $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($sql_Pedidos);
                    foreach ($ejecutar as $key => $value) {
                        echo '<option value="'.$value["factura"].'">'.$value["factura"].' - '.$value["nombre"].'</option>';
                    }

                     ?>
                      </select></i>
                    <button class="btn btn-primary" onclick="CargarFactura('pedido')">Cargar</button>
                    <button class="btn btn-danger" onclick="LimpiarPedio()">Limpiar</button>
              </div>
        
        </div>
        <div class="col-sm-4">  
              <div class="box-body">
                   <div class="input-group mb-3">
                                <input type="text" id="selector" class="form-control" placeholder="Articulo" aria-label="Username" autocomplete="off" style="text-transform: uppercase;">
                                <hr>
                                <div style="font-size:large;color:#3989c6;">5 = Familia / 6 y Marca = Descripcion</div>
                                <button onclick="myFunctionCodigo()" id="consulta" class="btn btn-primary" data-toggle ="modal"  ><i class="fa fa-search-plus"></i>        
                                    Buscar Código!
                                    </button>
                                    
                           </div>
              </div>
        
        </div>
         <!-- Imprimir etiqueta nombre -->
         <div class="col-sm-5">  
              <div class="box-body">
                   <div class="input-group mb-3">
                                <input type="text" id="ID_Nombre" class="form-control" placeholder="Nombre" aria-label="Nombre" autocomplete="off" style="text-transform: uppercase;">
                                <hr>
                                <button onclick="myNombre()" class="btn btn-primary"><i class="fa fa-search-plus"></i>Imprimir
                                    </button>
                                    
                           </div>
              </div>
        
        </div>
        <button class="btn btn-warning" onclick="RecorrerTabla()">Actualizar</button>
        <button class="btn btn-success" onclick="ArmarEtiquetas()">Imprimir</button>
          </div>
         
    </div>
    
    <center>
      <h3>Listado de Codigos para etiquetas</h3>
      <div id="resultado_compra" class="box-body">
            <!-- AQUI VA LA CONSULTA -->
      </div>
      </center>

  </section>

</div>

<!--=====================================
MODAL BUSCAR CODIGO
======================================-->

<div id="modalBuscarCodigo" class="modal fade" tabindex="-1" role="dialog">
  
    <div class="modal-dialog modal-lg" >

        <div class="modal-content">
            <!--=====================================
            CABEZA DEL MODAL
            ======================================-->

            <div class="modal-header" style="background:#3c8dbc; color:white">

              <button type="button" class="close" data-dismiss="modal">&times;</button>

              <h4 class="modal-title">Resultado Busqueda Código</h4>

            </div>

            <!--=====================================
            CUERPO DEL MODAL
            ======================================-->

            <div class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">  

                    <div class="box-body">

                        <div class="form-group">
                            <center>
                                <div id="result">  
                                
                                <!-- AQUI VA EL AJAX -->
                                </div>   
                            </center>
                        </div>      
                    </div>
                </div>
                <!--=====================================
                PIE DEL MODAL
                ======================================-->
                <div class="modal-footer">

                  <button id="btn_salir" type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>

                </div>

            </div>
        </div>
    </div>
</div>
<script>
/*=============================================
  DOCUMENT READY
  =============================================*/
var TIPO='';
var CONSULTO=false;
$(document).ready(function(){

    $('select#seleccionarfactura').on('change',function(){
          var valor = $(this).val();
          CONSULTO=false;
           $('#codigofactura').text(valor);
    });
    $('select#seleccionarpedido').on('change',function(){
          var valor = $(this).val();
          CONSULTO=false;
           $('#codigofactura').text(valor);
    });
});
/*=============================================
 ENTER BUSCAR CODIGO
  =============================================*/
document.getElementById('selector').addEventListener('keydown', 
    function (event) {
      //console.log(document.getElementById("selector").value);
      
        if (event.which == 13 || event.keyCode == 13) {
            myFunctionCodigo();
            return false;
        }
    return true;
      
    }); 

/*=============================================
  MY FUNCTION CODIGO
  =============================================*/
function myFunctionCodigo() {
  
  var dataToPass = {codigo:dispatchMask.masked.currentMask._value};
  $("#modalBuscarCodigo").modal()
  $('#result').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
  $.ajax('vistas/modulos/consulta_etiquetas.php?codigo='+dataToPass.codigo)
  .done(function(response){
    $('#result').html(response);
  });
}

/*=============================================
  MY Nombre (Pregunta cantidad)
  =============================================*/
function myNombre(){
              
                  var encabezado='^XA^CF0,36^FO120,50^FDREPUESTOS LOS PATICAS^FS';
                 //var encabezado='^XA^CF0,36^FO120,50^FDGEL ANTIBACTERIAL^FS';
                  
                  /*var codigop='^FO'+pos+',90^FD'+Nombre.value+'^FS';
                  var codigop = '^FO40FD'+ valorDelLabel  +'^FS' ;
                    +'^FS^XZ'
                  */
                  //var codigo ='GENERACION 75 - 2026';
                  var codigo =$('#ID_Nombre').val();
                  var pos = 55;
                  var codigop='^FO'+pos+',90^FD'+codigo+'^FS';
                  
                  colilla=encabezado+codigop +'^XZ';
                  console.log(colilla);
                  /// Habilitar esto para que imprima ****
                  setTimeout(() => { writeToSelectedPrinter(colilla); }, 1000); 
               
}

/*=============================================
  MY CODIGO (Pregunta cantidad)
  =============================================*/
function myCodigo(codigo,descripcion,ubicacion){
      console.log(descripcion);         
      var la_cantidad;
                   
      swal({
        title: "Facturacion",
        text: "Digite la cantidad de etiquetas",
        input: 'text',
        type: "question",
        
      }).then(resultado => {
          if (resultado.value) {
              let la_cantidad = resultado.value;

              for (i = 0; i < la_cantidad; i++) {
                  var encabezado='^XA^CF0,36^FO120,50^FDREPUESTOS LOS PATICAS^FS';
                  var tamdescripcion=codigo.length;

                  tamdescripcion=50-tamdescripcion;
                  tamdescripcion=tamdescripcion/2;
                  tamdescripcion=tamdescripcion*10;
                  var pos=20+tamdescripcion;
                  
                  var codigop='^FO'+pos+',90^FD'+codigo+'^FS';
                  var res = descripcion.substring(0, 38);

                  var tamdescripcion=descripcion.length;

                  if (tamdescripcion<38) {
                  
                    tamdescripcion=38-tamdescripcion;
                    tamdescripcion=tamdescripcion/2;
                    tamdescripcion=tamdescripcion*10;
                    var pos=55+tamdescripcion;
                  
                  }else{
                    var pos=55;
                  }

                  var descripcion1='^FO'+pos+',130^CF0,25^FD'+res+'^FS';
                  if (descripcion.length<38) {
                    var descripcion2='';
                  }else{
                    var descripcion2= descripcion.substring(39, 76);
                    var tamdescripcion=descripcion2.length;

                    if (tamdescripcion<38) {
                    
                      tamdescripcion=38-tamdescripcion;
                      tamdescripcion=tamdescripcion/2;
                      tamdescripcion=tamdescripcion*10;
                      var pos=55+tamdescripcion;
                    
                    }else{
                      var pos=55;
                    }
                  }
                  var today = moment().format('DDMMYY');
                  descripcion2='^FO'+pos+',160^FD'+descripcion2+'^FS';
                  var datos='^FO150,193^CF0,23^FD00000-00000-'+today+'-'+la_cantidad+'-'+ubicacion+'^FS^XZ';
                  colilla=encabezado+codigop+descripcion1+descripcion2+datos;
                  console.log(colilla);
                  /// Habilitar esto para que imprima ****
                  setTimeout(() => { writeToSelectedPrinter(colilla); }, 1000); 
              }
          }
      });
       $('#modalBuscarCodigo').modal('hide');
     
}

/*=============================================
  MY AJAX HH (ejecuta AJAX)
  =============================================*/
function myAjaxHH(url,data){
    
    var ajaxhh=$.ajax({
        "method":"POST",
        "url": url,
        "data": data
    });
    return ajaxhh;
    console.log(ajaxhh);
}
/*=============================================
  RECORRER TABLA
  =============================================*/

function RecorrerTabla(){
  if (CONSULTO==false) {

  }else{

    swal({
          title: '¿Está seguro de actualizar las estiquetas?',
          text: "¡Si no lo está puede cancelar la acción!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          cancelButtonText: 'Cancelar',
          confirmButtonText: 'Si, actualizar etiquetas!'
          }).then(function(result){
            if (result.value) {
                var linea="";
                var conteo=0;
                var imp_eti="";
                var rowCount = $('#codigos tr').length;
                var rowsProcessed=0;

                var table1 = document.getElementById('codigos'),
                    rows = table1.getElementsByTagName('tr'),
                    i, j, cells, customerId;

                for (i = 0, j = rows.length; i < j; ++i) {
                    cells = rows[i].getElementsByTagName('td');
                    if (!cells.length) {
                        continue;
                    }

                    linea= cells[2].innerHTML;
                    imp_eti = cells[6].firstChild.value;
                    //imp_eti = table.cell(i,6).nodes().to$().find('input').val()
                    //console.log('imp_eti='+imp_eti);
                    var codigo= $('#codigofactura').text();
                    var n = codigo.indexOf("-");
                    var factura = codigo
                    var idproducto= cells[1].firstChild.textContent;
                    var m = codigo.length;
                    var proveedor = codigo.substr(n+1,m)
                    var param = {
                        Factura: factura,
                        Codigo:linea,
                        ID:idproducto,
                        Etiquetas:imp_eti,
                        proveedor:proveedor,
                        tipo:TIPO
                    };


                    url="ajax/actualizar_etiquetas.ajax.php";
                    data="";
                    data=param;
                    myAjaxHH(url,data)
                        .done( function(data) {
                            console.log(data);
                            rowsProcessed=rowsProcessed+1;
                            // if(rowsProcessed==rowCount-2){
                            //     ArmarEtiquetas();
                            // }else{
                            //
                            // }
                        });
                }

            }
        })
  }
}

/*=============================================
  CARGAR FACTURA
  =============================================*/

function CargarFactura(tipo){
    TIPO=tipo;
    CONSULTO=true;
    var codigo= $('#codigofactura').text();
   
   $('#resultado_compra').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
    

    var factura = $('#codigofactura').text();

    var proveedor = codigo;
    
    var param = {
                      Factura: factura,
                      proveedor:proveedor,
                      tipo: TIPO
                      
                  };
              url="vistas/modulos/etiquetas_consulta_facturas.php";
              data="";
              data=param;
              myAjaxHH(url,data)
              .done( function(data) {
                $('#resultado_compra').html(data);
              });

}
/*=============================================
  LIMPIAR PEDIDO
  =============================================*/

function LimpiarPedio(){

     swal({
          title: '¿Está seguro de querer limpiar las estiquetas?',
          text: "¡Si no lo está puede cancelar la acción!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          cancelButtonText: 'Cancelar',
          confirmButtonText: 'Si, limpiar etiquetas!'
          }).then(function(result){
            if (result.value) {
    
                    var codigo= $('#codigofactura').text();
                    var n = codigo.indexOf("-");
                    var factura = codigo;
                    var m = codigo.length;
                    var proveedor = codigo.substr(n+1,m)
                    
                    var param = {
                                      Factura: factura,
                                      proveedor:proveedor,
                                      tipo: TIPO
                                      
                                  };
                              url="vistas/modulos/etiquetas_limpiar_facturas.php";
                              data="";
                              data=param;
                              myAjaxHH(url,data)
                              .done( function(data) {
                                swal({
                                          type: "success",
                                          title: "A el pedido ha sido limpiado de etiquetas correctamente",
                                          showConfirmButton: true,
                                          confirmButtonText: "Cerrar"
                                          }).then(function(result){
                                              console.log(result);
                                              window.location.replace('etiqueta');
                                          });
                              });
                }
            });

}

/*=============================================
  ARMAR ETIQUETAS
  =============================================*/
function ArmarEtiquetas(){
    if (CONSULTO==false) {

    }else {

        swal({
            title: '¿Está seguro de imprimir las estiquetas?',
            text: "¡Si no lo está puede cancelar la acción!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, imprimir etiquetas!'
        }).then(function (result) {
            if (result.value) {
                var codigo= $('#codigofactura').text();
                var n = codigo.indexOf("-");
                var factura = codigo;
                var m = codigo.length;
                var proveedor = codigo.substr(n+1,m)
                var param = {
                    Factura: factura,
                    proveedor:proveedor,
                    tipo: TIPO

                };
                url="vistas/modulos/etiquetas_resultado_facturas.php";
                data="";
                data=param;
                myAjaxHH(url,data)
                    .done( function(data) {
                        ImprimeColilla(data);
                    });
            }
        })
    }
}

function ImprimeColilla(recibido){
  console.log("recibido:"+recibido)
  while (recibido.indexOf("^XZ") != -1) {
      var sln = recibido.length;
      var pos = recibido.indexOf("^XZ");
      var colilla = recibido.slice(0, pos+3);
      recibido = recibido.substr(pos+3,sln);
      
      //

      //setTimeout(() => { console.log(colilla); }, 2000); 
     console.log(colilla);
     writeToSelectedPrinter(colilla);
     sleep(1500)
      
  }

}

function sleep(milliseconds) {
  const date = Date.now();
  let currentDate = null;
  do {
    currentDate = Date.now();
  } while (currentDate - date < milliseconds);
}


</script>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Etiquetas';
</script>