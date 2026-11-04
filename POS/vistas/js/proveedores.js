/*=============================================
EDITAR PROVEEDORES
=============================================*/
$(".tablasProveedores").on("click", ".btnEditarProveedores", function(){
	
	var idProveedores = $(this).attr("idProveedores");
	
	var datos = new FormData();
	datos.append("idProveedores", idProveedores);

	$.ajax({
		url: "ajax/proveedores.ajax.php",
		method: "POST",
      	data: datos,
      	cache: false,
     	contentType: false,
     	processData: false,
     	dataType:"json",
     	success: function(respuesta){
     		
     		$("#editarCodigo").val(respuesta["codigo"]);
     		$("#editarProveedor").val(respuesta["nombre"]);
     		$("#editarDireccion").val(respuesta["direccion"]);
     		$("#editarTelefono").val(respuesta["telefono"]);
     		$("#editarPais").val(respuesta["pais"]);
     		$("#editarCredito").val(respuesta["credito"]);
     		$("#editarComentarios").val(respuesta["comentarios"]);
     		$("#editarShortname").val(respuesta["shortname"]);
     		$("#editarTipo").html(respuesta["tipo"]);
     		$("#editarTipo").val(respuesta["tipo"]);

     	}

	})


})

/*=============================================
ELIMINAR PROVEEDOR
=============================================*/
$(".tablasProveedores").on("click", ".btnEliminarProveedores", function(){

	 var idProveedores = $(this).attr("idProveedores");

	 swal({
	 	title: '¿Está seguro de borrar el Proveedor?',
	 	text: "¡Si no lo está puede cancelar la acción!",
	 	type: 'warning',
	 	showCancelButton: true,
	 	confirmButtonColor: '#3085d6',
	 	cancelButtonColor: '#d33',
	 	cancelButtonText: 'Cancelar',
	 	confirmButtonText: 'Si, borrar proveedor!'
	 }).then(function(result){

	 	if(result.value){

	 		window.location = "index.php?ruta=proveedores&idProveedores="+idProveedores;

	 	}

	 })

})