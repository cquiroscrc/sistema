<?php
		$server="TAI";
		$database="inventario";
		$user="sa";
		$password="PLUMA";
		//DRIVER={MySQL ODBC 5.1 Driver};Server=myserver;Database=mydatabase;Uid=myusername;Pwd=mypassword;Option=3
		//if(($id=odbc_connect("Driver={SQL Server Native Client 10.0};Server=$server;Database=$database;", $user, $password))==false )
		//	die("Error de conexion");
		$id=odbc_connect($server, $user, $password);
	?>