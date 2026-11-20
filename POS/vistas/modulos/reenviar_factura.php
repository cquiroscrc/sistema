<?php
    global $dbConn;
    //include("db.php");

    include_once "../../../fe/API/www/settings.php";
//    include_once "../fe/API/api/contrib/admin/admin.php";
    include_once "../../../fe/API/api/core/conf.php";
    include_once "../../../fe/API/api/modules/db/module.php";
    include_once "../../../fe/API/api/core/grace.php";
    include_once "../../../fe/API/api/core/params.php";
    include_once "../../../fe/API/api/contrib/fpdf/factura.php";

if(isset($_REQUEST['consecutivo']) && !empty($_REQUEST['consecutivo'])){
    $consecutivo = $_REQUEST['consecutivo'];
}
if (substr($consecutivo,0,1)=='F'){
    $tD='FE';
}else{
    $tD='NC';
}

function UpdateEsteDBPDF($sessionID, $Documento, $TipoDoc, $xPDF)
{
    try {
        global $dbConn;
        $sql = "select NUMERO_DOC from DOC_ELECTRONICO_PROCESADO where NUMERO_DOC='$Documento' and TIPO_DOC='$TipoDoc'";
        $result = sql_exec($sql, 2, false, true);
        //Ya existe el documento
        $data = [
            'pdf' => $xPDF,
        ];
        if ($result != '-200') {
            $sql = "UPDATE DOC_ELECTRONICO_PROCESADO SET  PDF=convert(varbinary(max),:pdf)
               where NUMERO_DOC='$Documento' and TIPO_DOC='$TipoDoc' ";

            if (db_allGood() === true) {
                $stmt = $dbConn->prepare($sql);
                $stmt->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_SYSTEM);
                $stmt->execute($data);
            }
            // sql_exec($sql,0,true,true);
        }

    } catch (Exception $ex) {
        //SetContinuar(false);
        echo $ex->getMessage();
        //Bitacora($sessionID, "UpdateDBPDF Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}


 function GeneraEstePDF($sessionID, $Documento, $TipoDoc)
{
    try {
        $pdf = new FacturaPDF('P', 'mm', 'A4');
        $strPDF = $pdf->CrearDocumento($sessionID, $Documento, $TipoDoc);
        //echo $strPDF;


        UpdateEsteDBPDF($sessionID, $Documento, $TipoDoc, $strPDF);
        //string $q, $returnType = 1, $EsUDI=false, $retornaJSON=false
        sql_exec(" UPDATE DOC_ELECTRONICO_PROCESADO SET EMAIL_ENVIADO ='NO' WHERE NUMERO_DOC='$Documento'",0,true,false);

        //return $strPDF;
    } catch (Exception $ex) {
        echo $ex->getMessage();
        //SetContinuar(false);
        //Bitacora($sessionID, "GenerarPDF Error: {$ex->getMessage()}({$ex->getLine()})", $Documento, $TipoDoc);
    }
}
if(db_Connect()) {
    $strPDF = GeneraEstePDF("999999", $consecutivo, $tD);
    //ProcesarCorreosPendientes("999999", "F-00000000027904", "FE");
}

    //$strPDF = GenerarPDF($sessionID, $Documento, $TipoDoc);

   // $sql="SELECT * FROM hfacturas WHERE nfac like 'F-00000000028136";

     //if(($res = odbc_exec($id,$sql)) === false)
       // die("Error en Select: ". odbc_errormsg($id));

      //while($arr = odbc_fetch_array($res)){

      //}
 ?>
 <SCRIPT LANGUAGE="javascript"> // Redirecciona al home
      function closed() {
        window.open('','_parent','');
        window.close();
      }
      setTimeout(closed(),1500);
    </SCRIPT>

