<?php
/*
 * Copyright (C) 2017-2020 CRLibre <https://crlibre.org>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/** @ingroup Constants
 *  @{
 */

# Could not find restults in the db
define('ERROR_DB_NO_RESULTS_FOUND', '-200');

# Errors in the query
define('ERROR_DB_ERROR', '-201');

# No db connection
define('ERROR_DB_NOT_CONNECTED', '-202');


# What do you expect to get from the database?
# Retrieve nothing: updates, inserts and so on
define('DB_RETRIEVE_NONE', '201');
# Select where you expect only one result
define('DB_RETRIEVE_ONE',  '202');
# Select where you expect many results
define('DB_RETRIEVE_MANY', '203');

/** @}*/

/* Global Vars */
global $dbConn;
global $Error;
/**
 * Boot up procedure
 */
function db_bootMeUp()
{
    db_Connect();
}

//! Test if the connection is good, just for debug
function db_allGood()
{
    global $dbConn;
    global $Error;

    if (!$Error=="")
    {
        grace_error(__METHOD__ ."->". "DB Connection error: " . $Error);
        return false;
    }

    return true;
}

function db_Connect():bool
{
    global $dbConn;
    global $Error;

    # Create connection
    grace_debug(__METHOD__ ."-> config['db']['name']: ");
    grace_debug(conf_get('name', 'db'));
    grace_debug(__METHOD__ ."-> config['db']['pwd']: ");
    grace_debug(conf_get('pwd', 'db'));
    grace_debug(__METHOD__ ."-> config['db']['user']: ");
    grace_debug(conf_get('user', 'db'));
    grace_debug(__METHOD__ ."-> config['db']['host']: ");
    grace_debug(conf_get('host', 'db'));

    $cnString='sqlsrv:server='.conf_get('host', 'db') . ';Database='. conf_get('name', 'db') . ';TrustServerCertificate=1'; // migracion: ODBC 18 cifra por defecto

    try {
        $dbConn = new PDO($cnString, conf_get('user', 'db'), conf_get('pwd', 'db'), [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ] );
    }
    
    catch (\PDOException $e){
        $Error='Error:'.$e->getMessage(). 'Codigo:'.(int)$e->getCode();
        grace_error($Error);
        return false;
    }

    # Check connection
    grace_debug(__METHOD__ ."-> Conectado con :".$cnString );
    return  true;
}

/**
 * I make queries
 * @param $q The query to be excecuted
 * @param $returnType The number of return items you want 0: none, 1: Just one, >1: All that you have
 * @param bool $EsUDI Indicates if it's a Update Delete Insert operation
 * @param bool $retornaJSON Indicates returning a PDO::FETCH_ASSOC=true or a PDO::FETCH_OBJ=false
 */

function sql_exec(string $q, $returnType = 1, $EsUDI=false, $retornaJSON=false) //Si $EsUDI(EsUpdateDeleteInsert)=true es que es NO es un query de tipo SELECT
{
    global $dbConn;
    //Sanear el sql cuando viene con  " o '


    grace_debug($q);

        if(db_allGood() === true)
        {
        try {
            $stmt  = $dbConn->prepare($q);
            if($stmt->execute()){

            }else{
                grace_error( "Error ejecutando SQL:". print_r($stmt->errorInfo(), true));
                $result = ERROR_DB_ERROR;
                return $result;
            }
        }
        catch(\PDOException $ex)
        {
            $result = ERROR_DB_ERROR;
            grace_error(__METHOD__ ."->". $ex->getMessage() );
            //Bitacora(_METHOD__ ."->". $ex->getMessage());
            return  $result;
        }

        //Inicia proceso de extraccion y devolucion de datos.
        try {

            if ($stmt) //Si hay algo por obtener
            {
                if ($returnType ==1) //1=Solo una fila
                 {

                     if($retornaJSON)

                        $result=$stmt->fetch(PDO::FETCH_ASSOC);
                     else
                        $result=$stmt->fetch(PDO::FETCH_OBJ);
                 }
                else // returnType>1 = Todos los datos
                {
                    if($EsUDI) { //Solo retorna errorInfo en el caso de que haya.
                        $arr = $stmt->errorInfo();
                        if (isset($arr[2]) )
                            return $arr[2];
                        else
                            return 1;
                    }
                    else{   //Normal query
                        if($retornaJSON)
                            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
                     else
                            $result=$stmt->fetchAll(PDO::FETCH_OBJ);
                    }

                }
            }
            else{ //No $stmt
                $result = ERROR_DB_NO_RESULTS_FOUND;
            }

            if(!$result) //Result vacio!!!
                return ERROR_DB_NO_RESULTS_FOUND;
            else
                return $result;
        }
        catch(\PDOException $ex)
        {
            $result = ERROR_DB_ERROR;
            grace_error(__METHOD__ ."Error al ejecutar SQL: $q ->". $ex->getMessage() );
            return  $result;
        }
    }
    else
        return ERROR_DB_NOT_CONNECTED;
}
