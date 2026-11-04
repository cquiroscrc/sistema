<?php
namespace App\Data;


class pdoCN {

        public $pdo;

        function  __construct()
        {

           // $dotenv = \Dotenv\Dotenv::createImmutable(  realpath($_SERVER['DOCUMENT_ROOT']));
           // $dotenv->load();
           // $dotenv->required(['DB_HOSTNAME', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD']);

            //var_export($_ENV['DB_HOSTNAME']);
            //var_export($dotenv);
            $serverName='170.81.32.82';
            $database = 'inventario';
            $uid = 'sa';
            $pwd = 'PLUMA';

            try {

                $this->pdo  = new \PDO("sqlsrv:server=$serverName;Database=$database", $uid,$pwd);


            } catch (PDOException $e) {

                die("PDO   ERROR: " . $e->getMessage() ."s=".$serverName . "<br/>");

            }
        }



    }
