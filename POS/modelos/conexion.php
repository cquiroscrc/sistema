<?php

class Conexion{

	static public function conectar(){
		 

		$link = new PDO("sqlsrv:Server=localhost;Database=inventario", "sa", "PLUMA"  );
		
		//$link = new PDO("mysql:host=localhost;dbname=pos",
		//	            "root",
		//	            "");

		//$link->exec("set names utf8");

		return $link;

	}

}