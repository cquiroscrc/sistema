

/*=============================================
SUBIENDO LA FOTO DEL PRODUCTO
=============================================*/

$(".nuevaImagen").change(function(){

	var imagen = this.files[0];
	
	/*=============================================
  	VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
  	=============================================*/

  	if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){

  		$(".nuevaImagen").val("");

  		 swal({
		      title: "Error al subir la imagen",
		      text: "¡La imagen debe estar en formato JPG o PNG!",
		      type: "error",
		      confirmButtonText: "¡Cerrar!"
		    });

  	}else if(imagen["size"] > 2000000){

  		$(".nuevaImagen").val("");

  		 swal({
		      title: "Error al subir la imagen",
		      text: "¡La imagen no debe pesar más de 2MB!",
		      type: "error",
		      confirmButtonText: "¡Cerrar!"
		    });

  	}else{

  		var datosImagen = new FileReader;
  		datosImagen.readAsDataURL(imagen);

  		$(datosImagen).on("load", function(event){

  			var rutaImagen = event.target.result;

  			$(".previsualizar").attr("src", rutaImagen);

  		})

  	}
})

/*=============================================
PASE PRODUCTO
=============================================*/

$("#resultado_codigo").on("click", "button.btnPaseProducto", function(){
    $("#ModalPase").modal();
    var idProducto = $(this).attr("idProducto");
    $("#paseorigen").text(idProducto);

})

/*=============================================
MOVIMIENTOS PRODUCTO
=============================================*/
$("#resultado_codigo").on("click", "button.btnMovimientoProducto", function(){
  var idProducto = $(this).attr("idProducto");
  $('#resultadoM').html('<div class="loading"><img src="vistas/img/plantilla/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
  $.ajax('vistas/modulos/consulta_movimientos.php?codigo='+idProducto)
  .done(function(response){
    //console.log(response);
    $('#resultadoM').html(response);
  });
})

/*=============================================
EDITAR PRODUCTO
=============================================*/

$("#resultado_codigo").on("click", "button.btnEditarProducto", function(){

	var idProducto = $(this).attr("idProducto");
	
	var datos = new FormData();
    datos.append("idProducto", idProducto);

     $.ajax({

      url:"ajax/productos.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType:"json",
      success:function(respuesta){
          
          //console.log(respuesta);

           $("#editarCodigo").val(respuesta["CODIGO"]);

           $("#editarDescripcion").val(respuesta["DESCRIPCION"]);

           $("#editarTecnica").val(respuesta["DESC_ALT"]);

           $("#editarStock").val(respuesta["EXISTENCIA"]);

           $("#editarPrecioCompra").val(respuesta["COSTO"]);

           $("#editarPrecioVenta").val(respuesta["VENTA"]);

           $("#editarMarca").val(respuesta["MARCA"]);

           $("#editarProveedor").val(respuesta["proveedor"]);

           $("#editarUbicacion").val(respuesta["ubicacion"]);

           $("#editarPotencia").val(respuesta["potencia"]);

           $("#editarCabys").val(respuesta["CODIGO_CABYS"]);

           $("#id_tecnicas").text(respuesta["Carro"]);

      }

  })

})

/*=============================================
ELIMINAR PRODUCTO
=============================================*/

$("#resultado_codigo").on("click", "button.btnEliminarProducto", function(){

	var idProducto = $(this).attr("idProducto");
	var cantidad = $(this).attr("existencia");
	
  if (cantidad==0) {
     swal({

      title: '¿Está seguro de borrar el producto?',
      text: "¡Si no lo está puede cancelar la accíón!",
      type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          cancelButtonText: 'Cancelar',
          confirmButtonText: 'Si, borrar producto!'
          }).then(function(result) {
          if (result.value) {

            window.location = "index.php?ruta=productos&idProducto="+idProducto;

          }


    })
    


  }else{

    swal({
          title: "Error al eliminar",
          text: "¡La cantidad del producto debe ser 0!",
          type: "error",
          confirmButtonText: "¡Cerrar!"
        });

	 
  }
})
	
