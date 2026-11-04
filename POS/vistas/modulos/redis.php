<?php

namespace App\Utils;

use \Redis;

class RedisUtils
{

    public $redis;

    public  function __construct()
    {

        $this->redis=new Redis();
        $this->redis->connect('127.0.0.1', 6379);

    }

    public  function getKey($keyName){

        try {
            if ( $this->redis->isConnected())
            {
                if($this->redis->exists('login-username')){
                    return $this->redis->get('login-username');
                }
                else {
                    return 'No existe el usuario...Houston tenemos un problema!.No existe clave a leer';
                }
            }
            else {
                return 'No conectado a redis...Houston tenemos otro problema!!.No hay conexion a redis';
            }
        }
        catch (RedisException $ex){
            echo 'Error al obterner datos de sesion', +$ex->getMessage(),'\n' ;
            return $ex->getMessage();
        }
    }
    public  function setKey($keyName){

        $redis= new Redis();
        $redis->connect('127.0.0.1', 6379);

        try {
            if ($redis->isConnected())
            {
                if($redis->exists('login-username')){
                     $redis->set('login-username');
                }
                else {
                    return 'No existe la clave...Houston tenemos un problema no existe la clave a guardar!';
                }
            }
            else {
                return 'No conectado a redis...Houston tenemos otro problema!!.No hay conexion a redis';
            }
        }
        catch (RedisException $ex){
            echo 'Error al obterner datos de sesion', +$ex->getMessage(),'\n' ;
            return false;
        }
    }
}