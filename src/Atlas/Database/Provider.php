<?php
namespace Atlas\Database;

use Atlas\Exception;

class Provider
{
    public static function factory($config)
    {
        if (class_exists('Zend_Db')) {
            return new Provider\Zend\V1($config);
        }

        //todo: Detect Zend-Db V2

        if (class_exists('Zend\Db')) {
            return new Provider\Zend\V3($config);
        }

        //todo: fallback to PDO?

        throw new Exception('A supported database driver could not be found'); 
    }
}
