<?php
use Doctrine\ORM\Tools\Setup;

require_once "../../../vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotation Mapping
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/Entity"), $isDevMode);
// or if you prefer yaml or XML
$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/Entity/Map/"), $isDevMode);
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);

// database configuration parameters
$conn = array(
    'driver' => 'pdo_mysql',
    'user' => 'fnbrasil',
    'password' => 'OssracF1982',
    'host' => '127.0.0.1',
    'port' => '3306',
    'dbname' => 'c5_db'
);

// obtaining the entity manager
$entityManager = \Doctrine\ORM\EntityManager::create($conn, $config);
