<?php

require_once "conexion.php";

class ModeloUtilitaria{


	/*=============================================
	SELECT SENCILLO
	=============================================*/

	static public function mdlMostrarUtilitaria($tabla, $item, $valor){

		if($item != null){
			
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = '$valor'");

			//$stmt -> bindParam(":".$valor, $valor, PDO::PARAM_STR);
		

			$stmt -> execute();


			return $stmt -> fetchAll();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");

			$stmt -> execute();
			
			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	SELECT VARIBLE
	=============================================*/

	static public function mdlMostrarUtilitariaVar($valor){

					
			$stmt = Conexion::conectar()->prepare("$valor");

			//$stmt -> bindParam(":".$valor, $valor, PDO::PARAM_STR);
		
			//print_r($stmt);

			$stmt -> execute();


			return $stmt -> fetchAll();


		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	ELIMINAR BD
	=============================================*/

	static public function mdlEliminarUtil($tabla, $datos){

		 
		 



		$pdo= Conexion::conectar();
		$statement=$pdo->prepare("DELETE FROM hpregeneral WHERE factura = :factura");		
		//$statement ->bindParam(":tabla", $tabla, PDO::PARAM_STR);
		$statement ->bindParam(":factura", $datos, PDO::PARAM_STR);
		//var_dump($stmt);
		$delete=$statement -> execute();

		echo 'Rows deleted='.$delete; 

		//$statement -> close();

		$statement = null;

	}



/*=============================================
	INSERT
	=============================================*/


	static public function mdlIngresarUtilitariaPrefactura($tabla, $datos){
	try {
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(Factura, fecha, codigo, cantidad, movimiento, tipo, codcliente, maquina, vendedor, precio, estado) VALUES (:Factura, :fecha, :codigo, :cantidad, :movimiento, :tipo, :codcliente, :maquina, :vendedor, :precio, :estado)");


		$stmt->bindParam(":Factura", $datos["Factura"], PDO::PARAM_STR);
		$stmt->bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);
		$stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
		$stmt->bindParam(":cantidad", $datos["cantidad"], PDO::PARAM_STR);
		$stmt->bindParam(":movimiento", $datos["movimiento"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
		$stmt->bindParam(":codcliente", $datos["codcliente"], PDO::PARAM_STR);
		$stmt->bindParam(":maquina", $datos["maquina"], PDO::PARAM_STR);
		$stmt->bindParam(":vendedor", $datos["vendedor"], PDO::PARAM_STR);
		$stmt->bindParam(":precio", $datos["precio"], PDO::PARAM_STR);
		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

		print_r($stmt); 
		//var_dump($stmt);

		if($stmt->execute()){
			
			return "ok";	

		}else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;
	}
	catch(PDOException  $e) {
		echo $e->getMessage();
	}

	}
}
