<?php

require ($_SERVER['DOCUMENT_ROOT'] . '/ADMIN/vendor/autoload.php');
require  ($_SERVER['DOCUMENT_ROOT'] . '/ADMIN/src/inc/_classes/pdo.php');


if(isset($_POST['functionName']) && function_exists($_POST['functionName'])) {
    $functionName = $_POST['functionName'];
    if ($functionName=='getUbicaciones'){
        call_user_func('getUbicaciones',$_POST["codCol"],$_POST["descripcion"],$_POST["orderby"],$_POST["where"]);
    }
}

function getUbicaciones($codCol,$descripcion,$orderby,...$where)
{

    // P1 C1 D1 B1

    $cn = new \App\Data\pdoCN();
    $stmt = $cn->pdo->prepare("SELECT   DISTINCT $codCol,$descripcion FROM UBICACIONES order by $orderby ");
    $data =$stmt->execute();
    $json = json_encode( $stmt->fetchAll(PDO::FETCH_KEY_PAIR )) ;

    echo  $json ;

}
