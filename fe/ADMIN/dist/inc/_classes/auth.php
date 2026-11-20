<?php

//require  ( 'pdo.php');

class Login

{

    private $conn;

    public function __construct(\PDO $pdo) {

        $this->conn = $pdo;

    }

    public  function checkCredentials($username,$password)

    {

        $userExists=false;

        $sql = $this->conn->prepare("SELECT usuario,password,nombre,acceso,ip,foto,id_grupo FROM acc_usuarios WHERE usuario=:usuario and password=:password and estado=1");
        $data =$sql->execute(
                array(
                       ':usuario'=>$username,
                       ':password'=>$password
                ));
        while ($row = $sql->fetch(PDO::FETCH_LAZY))
        {
            $userExists=true;
            break;
        }
        return $userExists;

    }

}
