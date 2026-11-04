
<?php

require("db.php");

$directorio = opendir("subida_cat/2"); //ruta actual


while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
{
    if (is_dir($archivo))//verificamos si es o no un directorio
    {
        //echo "[".$archivo . "]<br />"; //de ser un directorio lo envolvemos entre corchetes
    }
    else
    {

        $tamano= strlen($archivo);
        $pos=strpos($archivo, ".");

        //echo $archivo . "<br />";
        $final= substr($archivo,0,($pos));
        $marca=2;  // Cambiar por tipo
        $final=$marca.$final;
        $encontro=false;
        
        /*
        $nuevo=substr($archivo,0,2);
        if ($nuevo=='XX') {
        	rename ("subida_cat/2/malos/".$archivo, "subida_cat/2/malos/".substr($archivo,2,$pos));
        }
        */
        
        $sql="SELECT CODIGO from inventario where CODIGO LIKE '$final%'";
        if(($pedi = odbc_exec($id,$sql)) === false)
            die("Error en Select: ". odbc_errormsg($id));  
        
        while($arr3 = odbc_fetch_array($pedi)){
              $encontro=true;
        }

        if ($encontro==false) {
            echo $archivo. "<br />";
            //mueve el archivo de lugar
            rename ("subida_cat/2/".$archivo, "subida_cat/2/malos/".$archivo);

        }
    
    }

}
?>
