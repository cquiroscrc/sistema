<?php
		// Migracion 2026-11: credenciales centralizadas en /etc/lospaticas/db.php (fuera del webroot, root:www-data 0640).
		// Servidor SQL nuevo: 172.16.12.100,1433 BD inventario, login paticas_app (antes DSN ODBC "TAI" con sa en 172.16.12.3).
		$__lp_db = require '/etc/lospaticas/db.php';
		// msodbcsql18 en Linux entrega varchar segun LC_CTYPE: ISO-8859-1 replica el driver "SQL Server" de Windows (cp1252).
		setlocale(LC_CTYPE, 'es_CR.ISO-8859-1', 'es_CR.iso88591');
		$server=$__lp_db['odbc']; // antes: "TAI"
		$database=$__lp_db['database'];
		$user=$__lp_db['user']; // antes: sa
		$password=$__lp_db['password']; // antes: password de sa en texto plano
		//DRIVER={MySQL ODBC 5.1 Driver};Server=myserver;Database=mydatabase;Uid=myusername;Pwd=mypassword;Option=3
		//if(($id=odbc_connect("Driver={SQL Server Native Client 10.0};Server=$server;Database=$database;", $user, $password))==false )
		//	die("Error de conexion");
		$id=odbc_connect($server, $user, $password);
	?>