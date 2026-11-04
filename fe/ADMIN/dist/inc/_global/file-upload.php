<?php

    require 'ADMIN/src/inc/_clases/pdo.php';

 // example of a PHP server code that is called in `uploadUrl` above
// file-upload.php script
header('Content-Type: application/json'); // set json response headers
$outData = upload(); // a function to upload the bootstrap-fileinput files
echo json_encode($outData); // return json data
exit(); // terminate

// main upload function used above
// upload the bootstrap-fileinput files
// returns associative array
function upload() {

    $targetDir = '/laragon/www/ADMIN/uploads/hacienda/';
    if (!file_exists($targetDir)) {
       if( !mkdir($targetDir,0777,true) ){
           die('Failed to create folders...');
       }
    }
    $fileBlob = 'certificado';                      // the parameter name that stores the file blob
    if (isset($_FILES[$fileBlob])) {
        $allowTypes = array('p12');

        $file = $_FILES[$fileBlob]['tmp_name'];  // the path for the uploaded file chunk
        $fileName = $_FILES[$fileBlob]['name'];          // you receive the file name as a separate post data
        $fileSize =  $_FILES[$fileBlob]['size'];          // you receive the file size as a separate post data
        $fileId = $_POST['fileId'];              // you receive the file identifier as a separate post data
        $targetFile = $targetDir.'/'.$fileName;  // your target file path


        if(move_uploaded_file($file, $targetFile)) {
            //Insert into database
            $q = "select NOMBRE_ARCHIVO_CERT from COMPAÑIAS where NOMBRE_ARCHIVO_CERT=''" + $fileName +"'";
            $r=$pdo->execute();

            while($r->fetch()){
                $out = prepareDBValue($file);
                $u="UPDATE COMPAÑIAS set CERTIFICADO = $OUT  where NOMBRE_ARCHIVO_CERT='"+ $fileName + "'";
                $r=$pdo->execute();
                return;
            }
        } else {
            return [ 'error' => 'Error subiendo archivo '];
        }
    }
    else {
        return ['error' => 'No se encuentra el archivo'];
    }

}
function prepareDBValue($filepath)
{
    $out = 'null';
    $handle = @fopen($filepath, 'rb');
    if ($handle)
    {
        $content = @fread($handle, filesize($filepath));
        $content = bin2hex($content);
        @fclose($handle);
        $out = "0x".$content;
    }
    return $out;
}


