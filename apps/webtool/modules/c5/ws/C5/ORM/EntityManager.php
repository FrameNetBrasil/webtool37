<?php
/**
 * Created by PhpStorm.
 * User: ematos
 * Date: 19/07/2018
 * Time: 11:27
 */

namespace C5\ORM;

use Doctrine\ORM\Tools\Setup;

class EntityManager
{
    static private $instance = null;
    static private $datasource = '';

    public static function getInstance($datasource)
    {
        if (self::$instance == null) {
            self::createInstance($datasource);
        } else if (self::$datasource != $datasource) {
            self::createInstance($datasource);
        }
        return self::$instance;
    }

    private static function createInstance($datasource){
        $isDevMode = true;
        $config = Setup::createYAMLMetadataConfiguration(array(__DIR__ . "/Model/Map"), $isDevMode);
        $ds = require 'conf.php';
        $conn = $ds[$datasource];
        self::$datasource = $datasource;
        self::$instance = \Doctrine\ORM\EntityManager::create($conn, $config);
    }

}