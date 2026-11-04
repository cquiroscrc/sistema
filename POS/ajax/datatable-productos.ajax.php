<?php

require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";

require_once "../controladores/categorias.controlador.php";
require_once "../modelos/categorias.modelo.php";


class TablaProductos{

 	/*=============================================
 	 MOSTRAR LA TABLA DE PRODUCTOS
  	=============================================*/ 

	public function mostrarTablaProductos(){

		$item = null;
    	$valor = null;
    	$orden = "id";

    	$inserta = "SELECT CODIGO,DESCRIPCION,EXISTENCIA,VENTA,ubicacion,
            substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
            WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            Else CODIGO END As Filtro FROM inventario ORDER BY Filtro");

        $ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($inserta);

  		$productos = ControladorProductos::ctrMostrarProductos($item, $valor, $orden);	

  		if(count($productos) == 0){

  			echo '{"data": []}';

		  	return;
  		}
		
  		$datosJson = '{
		  "data": [';

		  for($i = 0; $i < count($productos); $i++){

		  	/*=============================================
 	 		TRAEMOS LA IMAGEN
  			=============================================*/ 

		  	//$imagen = "<img src='".$productos[$i]["imagen"]."' width='40px'>";
		  	//$imagen = "<img src='vistas/img/productos/default/anonymous.png' width='40px'>";
  			include "../controladores/fotos.php";
		 
		          //$archivo="<img src='".$filename."' width='40px'>";
		         if (file_exists($filename)) {
		              $existe = 'SI';
		              $archivo="<img src='".$filename."' width='40px'>";
		          } else {
		              $archivo="<img src='vistas/img/productos/default/anonymous.png' width='40px'>";
		          	$existe = 'NO';
		          }	
		          	//echo $existe;

		          $imagen=$archivo;    


		  	/*=============================================
 	 		TRAEMOS LA CATEGORÍA
  			=============================================*/ 

		  	//$item = "id";
		  	//$valor = $productos[$i]["id_categoria"];

		  	//$categorias = ControladorCategorias::ctrMostrarCategorias($item, $valor);

		  	/*=============================================
 	 		STOCK
  			=============================================*/ 

  			if($productos[$i]["EXISTENCIA"] <= 5){

  				$stock = "<button class='btn btn-danger'>".$productos[$i]["EXISTENCIA"]."</button>";

  			}else if($productos[$i]["EXISTENCIA"] > 6 && $productos[$i]["EXISTENCIA"] <= 10){

  				$stock = "<button class='btn btn-warning'>".$productos[$i]["EXISTENCIA"]."</button>";

  			}else{

  				$stock = "<button class='btn btn-success'>".$productos[$i]["EXISTENCIA"]."</button>";

  			}

		  	/*=============================================
 	 		TRAEMOS LAS ACCIONES
  			=============================================*/ 

  			if(isset($_GET["perfilOculto"]) && $_GET["perfilOculto"] == "Especial"){

  				$botones =  "<div class='btn-group'><button class='btn btn-warning btnEditarProducto' idProducto='".$productos[$i]["CODIGO"]."' data-toggle='modal' data-target='#modalEditarProducto'><i class='fa fa-pencil'></i></button></div>"; 

  			}else{

  				 $botones =  "<div class='btn-group'><button class='btn btn-warning btnEditarProducto' idProducto='".$productos[$i]["CODIGO"]."' data-toggle='modal' data-target='#modalEditarProducto'><i class='fa fa-pencil'></i></button><button class='btn btn-danger btnEliminarProducto' idProducto='".$productos[$i]["CODIGO"]."' codigo='".$productos[$i]["CODIGO"]."' imagen='vistas/img/productos/default/anonymous.png'><i class='fa fa-times'></i></button></div>"; 

  			}

		 	///Aqui carga todo
		  	$datosJson .='[
			      "'.($i+1).'", 
			      "'.$productos[$i]["CODIGO"].'",
			      "'.$productos[$i]["DESCRIPCION"].'",
			      "'.$stock.'",
			      "'.$productos[$i]["VENTA"].'",
			      "'.$productos[$i]["ubicacion"].'",
			      "'.$imagen.'",
			      "'.$botones.'"
			    ],';

		  }

		  $datosJson = substr($datosJson, 0, -1);

		 $datosJson .=   '] 

		 }';
		
		echo $datosJson;


	}



}

/*=============================================
ACTIVAR TABLA DE PRODUCTOS
=============================================*/ 
$activarProductos = new TablaProductos();
$activarProductos -> mostrarTablaProductos();

