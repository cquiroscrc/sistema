<?php

require_once "conexion.php";

class ModeloClientes{

	/*=============================================
	CREAR CLIENTE
	=============================================*/

	static public function mdlIngresarCliente($tabla, $datos){

		$tipo=$datos["tipo"];
		$ejecute="NO";
	    $mensaje="";
	    $largotipo= strlen($datos['cedula']);
	    $direccion="CASA";
	    $credito=0;
	    $descuento=10;
	    $saldo=0;
	    $comentario="NUEVO";
	    $nacimiento='2016-01-01 00:00:00.000';
	    $ubicacion='1';
	    $codPais='506';
	    $gln='0000000000000';
	    $acepta='S';
	    $emite='N';

	    // Cedula Fisica
	    if ($tipo=='01') {
	       if ($largotipo<>9) {
	            $mensaje="Número de cedula mal ingresado 9 Digitos";
	            $ejecute="NO";
	       }else{
	            $ejecute="SI";
	       }
	    }

	    // Cedula Juridica
	    if ($tipo=='02') {
	       if ($largotipo<>10) {
	            $mensaje="Número de cedula Juridica mal ingresado 10 Digitos";
	            $ejecute="NO";
	       }else{
	            $ejecute="SI";
	       }
	    }
	    
	    //DIMEX
	    if ($tipo=='03') {
	      if ($largotipo==11 or $largotipo==12) {
	          $ejecute="SI";    
	     }else{
	          $mensaje="Número de DIMEX mal ingresado 11 o 12 Digitos";
	          $ejecute="NO";
	     }
	    }
	    
	    //NITE
	    if ($tipo=='04') {
	     if ($largotipo<>10) {
	          $mensaje="Número de NITE mal ingresado 10 Digitos";
	          $ejecute="NO";
	     }else{
	          $ejecute="SI";
	     }
	    }


	    if ($ejecute=="SI") {

	    	$consulta = "SELECT * FROM Consecutivos WHERE TIPO= '07'";

        	$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);

              foreach ($ejecutar as $key => $value) {
                $codigo = $value["numero"];
              }


        	$consulta="SELECT * FROM fechas WHERE movimiento ='Facturacion'" ; 

        	$ejecutar = ControladorUtilitaria::ctrMostrarUtilitariaVar($consulta);

              foreach ($ejecutar as $key => $value) {
                $afiliacion = $value['Fecha'];
              } 


			$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(codigo,nombre,telefono,direccion,cedula,credito,descuento,saldo,comentario,celular,correo,nacimiento,afiliacion,provincia,canton,distrito,barrio,otras_senas,TIPO_NIT,TEL_COD_PAIS_ISO,GLN,ACEPTA_DOC_ELECTRONICO,EMITE_DOC_ELECTRONICO, actividad_comercial) VALUES (:codigo, :nombre, :telefono, :direccion, :cedula, :credito, :descuento, :saldo, :comentario, :celular, :correo, :nacimiento, :afiliacion, :provincia, :canton, :distrito, :barrio, :otras_senas, :TIPO_NIT, :TEL_COD_PAIS_ISO, :GLN, :ACEPTA_DOC_ELECTRONICO, :EMITE_DOC_ELECTRONICO :actividad_comercial)");

			$stmt->bindParam(":codigo", $codigo, PDO::PARAM_INT);
			$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
			$stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
			$stmt->bindParam(":direccion",$direccion, PDO::PARAM_STR);
			$stmt->bindParam(":cedula", $datos["cedula"], PDO::PARAM_STR);
			$stmt->bindParam(":credito",$credito, PDO::PARAM_INT);
			$stmt->bindParam(":descuento",$descuento, PDO::PARAM_INT);
			$stmt->bindParam(":saldo", $saldo, PDO::PARAM_INT);
			$stmt->bindParam(":comentario", $comentario, PDO::PARAM_STR);
			$stmt->bindParam(":celular", $datos["celular"], PDO::PARAM_STR);
			$stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
			$stmt->bindParam(":nacimiento",$nacimiento, PDO::PARAM_STR);
			$stmt->bindParam(":afiliacion", $afiliacion, PDO::PARAM_STR);
			$stmt->bindParam(":provincia", $ubicacion, PDO::PARAM_STR);
			$stmt->bindParam(":canton", $ubicacion, PDO::PARAM_STR);
			$stmt->bindParam(":distrito", $ubicacion, PDO::PARAM_STR);
			$stmt->bindParam(":barrio", $ubicacion, PDO::PARAM_STR);
			$stmt->bindParam(":otras_senas", $direccion, PDO::PARAM_STR);
			$stmt->bindParam(":TIPO_NIT", $tipo, PDO::PARAM_STR);
			$stmt->bindParam(":TEL_COD_PAIS_ISO", $codPais, PDO::PARAM_STR);
			$stmt->bindParam(":GLN", $gln, PDO::PARAM_STR);
			$stmt->bindParam(":ACEPTA_DOC_ELECTRONICO", $acepta, PDO::PARAM_STR);
			$stmt->bindParam(":EMITE_DOC_ELECTRONICO", $emite, PDO::PARAM_STR);
			$stmt->bindParam(":actividad_comercial", $actividad_comercial, PDO::PARAM_STR);


			print_r($stmt);




			if($stmt->execute()){

				return "ok";

			}else{

				return "error";
			
			}
			
			print_r($stmt->errorInfo());
			
			$stmt->close();
			$stmt = null;

		}else{
			return $mensaje;
		}

			

	}

	/*=============================================
	MOSTRAR CLIENTES
	=============================================*/

	static public function mdlMostrarClientes($tabla, $item, $valor){

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

	/*=============================================
	EDITAR CLIENTE
	=============================================*/

	static public function mdlEditarCliente($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, cedula = :cedula, correo = :correo, telefono = :telefono, celular = :celular, comentario = :comentario, actividad_comercial = :actividad_comercial WHERE codigo = :codigo");

		$stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_INT);
		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":cedula", $datos["cedula"], PDO::PARAM_STR);
		$stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
		$stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
		$stmt->bindParam(":celular", $datos["celular"], PDO::PARAM_STR);
		$stmt->bindParam(":actividad_comercial", $datos["actividad_comercial"], PDO::PARAM_STR);
        if ($datos["Activo"]=='Desactivado') {
            $estado="**DESACTIVADO**";
            $stmt->bindParam(":comentario", $estado, PDO::PARAM_STR);
        }else{

            $stmt->bindParam(":comentario", $datos["comentario"], PDO::PARAM_STR);
        }


		if($stmt->execute()){

			return "ok";

		}else{

			return "error";
		
		}

		$stmt->close();
		$stmt = null;

	}

	/*=============================================
	ELIMINAR CLIENTE
	=============================================*/

	static public function mdlEliminarCliente($tabla, $datos){

        $stmt1 = Conexion::conectar()->prepare("UPDATE cxc SET IDCliente = '0' WHERE IDCliente= :id");

        $stmt1 -> bindParam(":id", $datos, PDO::PARAM_INT);
        if($stmt1 -> execute()){
            $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE codigo = :id");

            $stmt -> bindParam(":id", $datos, PDO::PARAM_INT);

            if($stmt -> execute()){

                return "ok";

            }else{

                return "error";

            }

            $stmt -> close();

            $stmt = null;

        }
        $stmt1 -> close();

        $stmt1 = null;



	}

	/*=============================================
	ACTUALIZAR CLIENTE
	=============================================*/

	static public function mdlActualizarCliente($tabla, $item1, $valor1, $valor){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE id = :id");

		$stmt -> bindParam(":".$item1, $valor1, PDO::PARAM_STR);
		$stmt -> bindParam(":id", $valor, PDO::PARAM_STR);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;

	}

}