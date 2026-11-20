/*=============================================
CARGAR LA TABLA DINÁMICA DE VENTAS
=============================================*/
 function reeviarFecha() {
    

    var fechaInicial = $("#fecha_inicio_reporte").val();
	var dateString = moment(fechaInicial).format('M/D/Y');
	fechaInicial=dateString;

    var fechaFinal = $("#fecha_fin_reporte").val();
	var dateString = moment(fechaFinal).format('M/D/Y');
    fechaFinal= dateString;
    window.location = "index.php?ruta=ventas&fechaInicial="+fechaInicial+"&fechaFinal="+fechaFinal;

  }


$('.tablaVentas').DataTable( {
    "ajax": "ajax/datatable-ventas.ajax.php",
    "deferRender": true,
	"retrieve": true,
	"processing": true,
	"lengthMenu": [[5,10,20, -1],[5,10,50,"Mostrar Todo"]],
	 "language": {

			"sProcessing":     "Procesando...",
			"sLengthMenu":     "Mostrar _MENU_ registros",
			"sZeroRecords":    "No se encontraron resultados",
			"sEmptyTable":     "Ningún dato disponible en esta tabla",
			"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
			"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
			"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
			"sInfoPostFix":    "",
			"sSearch":         "Buscar:",
			"sUrl":            "",
			"sInfoThousands":  ",",
			"sLoadingRecords": "Cargando...",
			"oPaginate": {
			"sFirst":    "Primero",
			"sLast":     "Último",
			"sNext":     "Siguiente",
			"sPrevious": "Anterior"
			},
			"oAria": {
				"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
				"sSortDescending": ": Activar para ordenar la columna de manera descendente"
			}

	}

} );



/*=============================================
ANULAR BORRAR VENTA
=============================================*/
$(".tablas").on("click", ".btnEliminarVenta", function(){

  var idVenta = $(this).attr("idVenta");

  swal({
        title: '¿Está seguro de anular la venta?',
        text: "¡Si no lo está puede cancelar la accíón!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Si, Anular venta!'
      }).then(function(result){
        if (result.value) {
          
            window.open("vistas/modulos/anular_proceso.php?consecutivo="+idVenta, "_blank");
        }

  })

})

/*=============================================
REENVIAR VENTA
=============================================*/
$(".tablas").on("click", ".btnReenviarVenta", function(){

	var idVenta = $(this).attr("idVenta");

	swal({
		title: '¿Está seguro de reenviar esta factura?',
		text: "¡Si no lo está puede cancelar la accíón!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Si, Reenviar!'
	}).then(function(result){
		if (result.value) {

			window.open("vistas/modulos/reenviar_factura.php?consecutivo="+idVenta, "_blank");
		}

	})

})

/*=============================================
REIMPRIMIR FACTURA
=============================================*/

$(".tablas").on("click", ".btnImprimirFactura", function(){

	var codigoVenta = $(this).attr("codigoVenta");
	swal({
		title: '¿Está seguro de reimprimir esta factura?',
		text: "¡Si no lo está puede cancelar la accíón!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Si, reimprimir!'
	}).then(function(result){
		if (result.value) {

			window.open("vistas/modulos/reimprimir_imprime.php?codigo="+codigoVenta, "_blank");
		}

	})


	

})



/*=============================================
ABRIR ARCHIVO XML EN NUEVA PESTAÑA
=============================================*/

$(".abrirXML").click(function(){

	var archivo = $(this).attr("archivo");
	window.open(archivo, "_blank");


})


