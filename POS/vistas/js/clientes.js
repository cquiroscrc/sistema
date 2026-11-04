/*=============================================
EDITAR CLIENTE
=============================================*/
$(".tablas").on("click", ".btnEditarCliente", function(){

	var idCliente = $(this).attr("idCliente");

	var datos = new FormData();
    datos.append("idCliente", idCliente);

    $.ajax({

      url:"ajax/clientes.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType:"json",
      success:function(respuesta){
      
      	 $("#idCliente").val(respuesta["codigo"]);
	       $("#editarCliente").val(respuesta["nombre"]);
	       $("#editarDocumentoId").val(respuesta["cedula"]);
	       $("#editarEmail").val(respuesta["correo"]);
	       $("#editarTelefono").val(respuesta["telefono"]);
	       $("#editarCelular").val(respuesta["celular"]);
         $("#editarComentario").val(respuesta["comentario"]);
         $("#editarActividad").val(respuesta["actividad_comercial"]);
	  }

  	})

})
/*=============================================
SALDO CLIENTE
=============================================*/
$(".tablas").on("click", ".btnSaldoCliente", function(){

    var idCliente = $(this).attr("idCliente");
    $("#modalSaldo").modal();
    $('#resultadoSaldo').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
    $.ajax('vistas/modulos/clientes_saldos.php?codigo='+idCliente)
        .done(function(response){
            //console.log(response);
            $('#resultadoSaldo').html(response);
        });



})
/*=============================================
ELIMINAR CLIENTE
=============================================*/
$(".tablas").on("click", ".btnEliminarCliente", function(){

	var idCliente = $(this).attr("idCliente");
	
	swal({
        title: '¿Está seguro de borrar el cliente?',
        text: "¡Si no lo está puede cancelar la acción!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Si, borrar cliente!'
      }).then(function(result){
        if (result.value) {
          
            window.location = "index.php?ruta=clientes&idCliente="+idCliente;
        }

  })

})