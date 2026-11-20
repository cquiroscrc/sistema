<?php

    require("db.php");

    
    $actualizar="SI";

    if((isset($_POST['nombre']))and(!empty($_POST['nombre']))){
        $nombre = $_POST['nombre'];
    }

    if((isset($_POST['cedula']))and(!empty($_POST['cedula']))){
        $cedula = $_POST['cedula'];
    }

    
    if((isset($_POST['celular']))and(!empty($_POST['celular']))){
        $celular = $_POST['celular'];
    }

    if((isset($_POST['correo']))and(!empty($_POST['correo']))){
        $correo = $_POST['correo'];
    }

    //*** Revisar si el correo ya existe
    $sql="SELECT * FROM tbl_premios WHERE Correo ='$correo'";

        if(($res = odbc_exec($id1,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id1));
        
        while($arr = odbc_fetch_array($res)){
            $actualizar="NO";
            echo "Ya existe el correo";
            header("location: fin.php");   
        }
        
        if ($actualizar=="SI") {
            //*** Revisar si quedan premios    
            $sql_busqueda="SELECT count(*) as premios_v FROM tbl_premios WHERE Correo IS NULL";

            if(($res_busqueda = odbc_exec($id1,$sql_busqueda)) === false)
                die("Error en Select: ". odbc_errormsg($id1));
            
            while($arr_premio = odbc_fetch_array($res_busqueda)){
                if ($arr_premio["premios_v"]<=0) {
                   $actualizar="NO";    
                    echo "No quedan premios" ;
                    header("location: premios/fin.php");
                }
                
            }
        }
            



    //**** Revisa si tiene que actualizar actualiza
    if ($actualizar=="SI") {

        
        $sql_busqueda="SELECT * FROM tbl_premios WHERE Correo IS NULL AND Id IS NOT NULL";

        if(($res_busqueda = odbc_exec($id1,$sql_busqueda)) === false)
            die("Error en Select: ". odbc_errormsg($id1));
        
        while($arr_premio = odbc_fetch_array($res_busqueda)){
            $id_premio=$arr_premio["ID"];
            $premio= $arr_premio["Premio"];
        }

        
        $sql_update="UPDATE tbl_premios SET Nombre = '$nombre', Identificacion='$cedula', Telefono = '$celular', Correo='$correo' WHERE ID='$id_premio' AND Premio='$premio'";

        if(($res_update = odbc_exec($id1,$sql_update)) === false)
            die("Error en Select: ". odbc_errormsg($id1));
        Echo "Incluido";

        header("location: http://www.lospaticas.com/premio.php?correo=".$correo."&nombre=".$nombre."&id_premio=".$id_premio."&premio=".$premio."");
    }    
    //** Termina Revision
    
    
       
    







?>