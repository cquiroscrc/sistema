<?php

require_once "conexion.php";

class ModeloPlacas{

	/*=============================================
	CREAR CLIENTE
	=============================================*/

	static public function mdlIngresarPlacas
	($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(Placa, Marca, Tipo, Year, Vin, Motor, Tipo2) VALUES (:Placa, :Marca, :Tipo, :Year, :Vin, :Motor,:Tipo2)");

		$stmt->bindParam(":Placa", $datos["Placa"], PDO::PARAM_STR);
		$stmt->bindParam(":Marca", $datos["Marca"], PDO::PARAM_STR);
		$stmt->bindParam(":Tipo", $datos["Tipo"], PDO::PARAM_STR);
		$stmt->bindParam(":Year", $datos["Year"], PDO::PARAM_STR);
		$stmt->bindParam(":Vin", $datos["Vin"], PDO::PARAM_STR);
		$stmt->bindParam(":Motor", $datos["Motor"], PDO::PARAM_STR);
		$stmt->bindParam(":Tipo2", $datos["Tipo2"], PDO::PARAM_STR);
		
		if($stmt->execute()){

			return "ok";

		}else{

			return "error";
		
		}

		$stmt->close();
		$stmt = null;

	}

	/*=============================================
	MOSTRAR CLIENTES
	=============================================*/

	static public function mdlMostrarPlacas($tabla, $item, $valor){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

}