<?php

require_once "conexion.php";

class ModeloProductos{

	/*=============================================
	MOSTRAR PRODUCTOS
	=============================================*/

	static public function mdlMostrarProductos($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = '$valor' ORDER BY $orden ASC");

			//$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
			//var_dump($stmt);
			$stmt -> execute();
			
			return $stmt -> fetch();

		}else{

			//$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY $orden DESC");

			$stmt = Conexion::conectar()->prepare("SELECT CODIGO,DESCRIPCION,EXISTENCIA,VENTA,ubicacion,
            substring(CODIGO,1,1),substring(CODIGO,6,3 ),substring(CODIGO,10,15),Case substring(CODIGO, 1, 1)
            WHEN '0' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            WHEN '1' then substring(CODIGO,1,1)+substring(CODIGO,5,4 )+substring(CODIGO,9,4 )+' '+substring(CODIGO,1,4 )+' '+ltrim(rtrim(substring(CODIGO,13,12 )))
            Else CODIGO END As Filtro FROM inventario ORDER BY Filtro");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	REGISTRO DE PRODUCTO
	=============================================*/
	static public function mdlIngresarProducto($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(CODIGO, DESCRIPCION, EXISTENCIA,COSTO, VENTA,BODEGA, DESC_ALT, descuento, proveedor, IV, ubicacion, costodol, potencia, UNIDAD_MEDIDA, CODIGO_IMPUESTO, MARCA) VALUES (:codigo, :descripcion, :stock ,:precio_compra, :precio_venta,:bodega, :tecnica, :descuento, :proveedor, :iv, :ubicacion, :costodol, :potencia, :unidad, :codiva, :marca)");

		$bodega=1;
		$descuento=10;
		$iv=13;
		$costodol=0;
		$unidad="Unid";
		$codiva='01';

		$stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt->bindParam(":stock", $costodol, PDO::PARAM_INT);
		$stmt->bindParam(":precio_compra", $datos["precio_compra"], PDO::PARAM_INT);
		$stmt->bindParam(":precio_venta", $datos["precio_venta"], PDO::PARAM_INT);
		$stmt->bindParam(":bodega", $bodega, PDO::PARAM_INT);
		$stmt->bindParam(":tecnica", $datos["tecnica"], PDO::PARAM_STR);
		$stmt->bindParam(":descuento", $descuento, PDO::PARAM_INT);
		$stmt->bindParam(":proveedor", $datos["proveedor"], PDO::PARAM_STR);
		$stmt->bindParam(":iv", $iv, PDO::PARAM_INT);
		$stmt->bindParam(":ubicacion", $datos["ubicacion"], PDO::PARAM_STR);
		$stmt->bindParam(":costodol", $costodol, PDO::PARAM_INT);
		$stmt->bindParam(":potencia", $datos["potencia"], PDO::PARAM_INT);
		$stmt->bindParam(":unidad", $unidad, PDO::PARAM_STR);
		$stmt->bindParam(":codiva", $codiva, PDO::PARAM_STR);
		$stmt->bindParam(":marca", $datos["marca"], PDO::PARAM_STR);
		
		

		if($stmt->execute()){

			return "ok";

		}else{

			return "error";
		
		}

		$stmt->close();
		$stmt = null;

	}

	/*=============================================
	EDITAR PRODUCTO
	=============================================*/
	static public function mdlEditarProducto($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET DESCRIPCION = :descripcion, DESC_ALT = :tecnica, EXISTENCIA = :stock, COSTO = :precio_compra, VENTA = :precio_venta, MARCA = :marca, proveedor = :proveedor, ubicacion = :ubicacion, potencia = :potencia, Carro= :Carro ,  CODIGO_CABYS= :cabys WHERE CODIGO = :codigo");

		$stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt->bindParam(":tecnica", $datos["tecnica"], PDO::PARAM_STR);
		$stmt->bindParam(":imagen", $datos["imagen"], PDO::PARAM_STR);
		$stmt->bindParam(":stock", $datos["stock"], PDO::PARAM_INT);
		$stmt->bindParam(":precio_compra", $datos["precio_compra"], PDO::PARAM_INT);
		$stmt->bindParam(":precio_venta", $datos["precio_venta"], PDO::PARAM_INT);
		$stmt->bindParam(":marca", $datos["marca"], PDO::PARAM_STR);
		$stmt->bindParam(":proveedor", $datos["proveedor"], PDO::PARAM_STR);
		$stmt->bindParam(":ubicacion", $datos["ubicacion"], PDO::PARAM_STR);
		$stmt->bindParam(":potencia", $datos["potencia"], PDO::PARAM_INT);
		$stmt->bindParam(":cabys", $datos["cabys"], PDO::PARAM_INT);
		$stmt->bindParam(":Carro", $datos["Carro"], PDO::PARAM_INT);

		if($stmt->execute()){

			return "ok";

		}else{

			return "error";
		
		}

		$stmt->close();
		$stmt = null;

	}

	/*=============================================
	BORRAR PRODUCTO
	=============================================*/

	static public function mdlEliminarProducto($tabla, $datos){



		$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE CODIGO = :id");

		$stmt -> bindParam(":id", $datos, PDO::PARAM_INT);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	MOSTRAR SUMA VENTAS
	=============================================*/	

	static public function mdlMostrarSumaVentas($tabla){

		$stmt = Conexion::conectar()->prepare("SELECT SUM(ventas) as total FROM $tabla");

		$stmt -> execute();

		return $stmt -> fetch();

		$stmt -> close();

		$stmt = null;
	}

	

}