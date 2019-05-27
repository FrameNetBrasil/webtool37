<?php
include "offline.php";

$app = 'webtool';
$db = 'webtool';

$documentEntry = $argv[1];
$fileName = $argv[2];

$_REQUEST['documentEntry'] = $documentEntry;
$_REQUEST['filename'] = $fileName;
// Endereco do servico a ser executado
$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . "{$app}/api/data/exportDocumentToXML";

$configFile = Manager::getHome() . "/apps/{$app}/conf/conf.php";
Manager::loadConf($configFile);
Manager::setConf('logs.level', 2);
Manager::setConf('logs.port', 9998);

Manager::setConf('fnbr.db', $db);

mdump("documentEntry = " . $documentEntry);
mdump("fileName = " . $fileName);

Manager::processRequest(true);

