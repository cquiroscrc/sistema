<?php

class  PageData {

    private  $cn;

    function __construct()
    {

        $this->cn = new \App\Data\pdoCN();

    }

    public function getPageData($request){

            $sql = $this->cn->pdo->prepare("SELECT $request FROM compañias ");
            $data =$sql->execute();
            $row = $sql->fetch( PDO::FETCH_ASSOC);
            return  ($row[$request]);

    }



    public  function getTiposIdentificacion(){


        $sql = $this->cn->pdo->prepare("SELECT CODIGO,TIPO_IDENTIFICACION FROM DE_TIPOS_IDENTIFICACION ");
        $data =$sql->execute();
        return   $sql->fetchAll();

     }

    public function compannyUpdateInformation($whatToSave){

    }

}
