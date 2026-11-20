<?php


function getUserData($username)

{

    $cn = new \App\Data\pdoCN();

    $userExists=false;


    $sql = $cn->pdo->prepare("SELECT usuario,password,nombre,acceso,ip,foto,id_grupo FROM acc_usuarios WHERE usuario=:usuario  and estado=1");
    $data =$sql->execute(
        array(
            ':usuario'=>$username,
        ));
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return array($row);

}
