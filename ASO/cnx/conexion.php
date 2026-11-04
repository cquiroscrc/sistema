<?php

class Conexion{

	static public function conectar(){
		 

		// Migracion 2026-11: SQL Server 172.16.12.100 (login paticas_app); credenciales en /etc/lospaticas/db.php
		// Antes: new PDO("sqlsrv:Server=localhost;Database=inventario", "sa", ...) en el servidor Windows
		$__lp_db = require '/etc/lospaticas/db.php';
		// PHP 8 cambio el modo de error por defecto de PDO a EXCEPTION (en 7.2 era SILENT).
		// ERRMODE_WARNING mantiene el comportamiento anterior y deja el error en el log de PHP.
		$link = new PDO($__lp_db['pdo_dsn'], $__lp_db['user'], $__lp_db['password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
		
		//$link = new PDO("mysql:host=localhost;dbname=pos",
		//	            "root",
		//	            "");

		//$link->exec("set names utf8");

		return $link;

	}

}