<?php
include "offline.php";
//require_once($dir . '/apps/fnbr/vendor/autoload.php');

$app = 'fnbr';
$db = 'mfn';

$idLanguage = $argv[1];
$documentEntry = $argv[2];
$fileName = $argv[3];

$_REQUEST['idLanguage'] = $idLanguage;
$_REQUEST['documentEntry'] = $documentEntry;
$_REQUEST['filename'] = $fileName;
// Endereco do servico a ser executado
$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . "{$app}/utils/import/importFullTextOffline";

$configFile = Manager::getHome() . "/apps/{$app}/conf/conf.php";
Manager::loadConf($configFile);
Manager::setConf('logs.level', 2);
Manager::setConf('logs.port', 9998);

Manager::setConf('fnbr.db', $db);

mdump("documentEntry = " . $documentEntry);
mdump("fileName = " . $fileName);

Manager::processRequest(true);

