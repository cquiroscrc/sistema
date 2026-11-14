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

function consultar()
{
        $curl   = curl_init();
        $clave  = params_get('clave');
        $access_token=params_get('access_token');
        if ($clave == "" || strlen($clave) == 0)
            return "La clave no puede estar en blanco";
        $unclean_url=params_get("api_url");
        $url = str_replace("\r","",$unclean_url);

        if ($url == null)
            return "Ha ocurrido un error en el  url.";


        curl_setopt_array($curl, array(
            CURLOPT_URL             => $url ,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => "",
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_SSL_VERIFYHOST  => 0,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => "GET",
            CURLOPT_HEADER=>true,
            CURLOPT_VERBOSE=> false, //True para ver errores
          
    
            CURLOPT_HTTPHEADER      => array(
                "Authorization: Bearer " . $access_token,
                "Cache-Control: no-cache",
                //"Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        
        $response   = curl_exec($curl);
        $status     = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err        = curl_error($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);


        foreach(explode("\r\n", trim(substr($response, 0, $header_size))) as $row) {
            if(preg_match('/(.*?): (.*)/', $row, $matches)) {
                $headers[$matches[1]] = $matches[2];
            }
        }
        if ($response === false or $response=="" or $status=="400") 
            {     
                $arrayResp = array(
                    "Status" => $status,                  
                    "text" => $headers
                );
                return json_encode($arrayResp);
            }
        if ($err)
        {
            $arrayResp = array(
                "Status" => $status,                
                "text" => $err
            );
            return json_encode($arrayResp);
        }
        else
        {             
            return  ($body);
        }
}
   
?>
