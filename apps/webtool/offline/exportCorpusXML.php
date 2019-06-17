<?php
$dirScript = dirname(__FILE__);

include $dirScript . "/offline.php";
include $dirScript . "/../services/EmailService.php";

$app = 'webtool';
$db = 'webtool';

$corpusEntry = $argv[1];
$idLanguage = $argv[2];
$idUser = $argv[3];
$email = $argv[4];

$dirName = realpath(Manager::getAbsolutePath("apps/{$app}/files")). '/' . $idUser;

if(!file_exists($dirName)) {
    mkdir($dirName);
}

$_REQUEST['corpusEntry'] = $corpusEntry;
$_REQUEST['idLanguage'] = $idLanguage;
$_REQUEST['dirName'] = $dirName;

// Endereco do servico a ser executado
var_dump($_REQUEST);
$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . "{$app}/api/data/exportCorpusToXML";

$configFile = Manager::getHome() . "/apps/{$app}/conf/conf.php";
Manager::loadConf($configFile);
Manager::setConf('logs.level', 2);
Manager::setConf('logs.port', 9998);

Manager::setConf('fnbr.db', $db);

// mdump("documentEntry = " . $documentEntry);
// mdump("fileName = " . $fileName);

Manager::processRequest(true);

try {
    $fname = $corpusEntry . '_' . date('Ymd') . '_' . date('Hi') . '_xml.zip';
    $fileName = $dirName . '/' . $fname;
    if (file_exists($fileName)) {
        unlink($fileName);
    }

    $pd = new \PharData($fileName);
    $scandir = scandir($dirName) ?: [];
    $scandir = array_diff($scandir, ['..', '.']);
    foreach ($scandir as $filePath) {
        $pathParts = pathinfo($filePath);
        if ($pathParts['extension'] == 'xml') {
            $pd->addFile($dirName . '/' . $filePath, $filePath);
        }
    }
    foreach ($scandir as $filePath) {
        $pathParts = pathinfo($filePath);
        if ($pathParts['extension'] == 'xml') {
            unlink($dirName . '/' . $filePath);
        }
    }

    $emailService = new EmailService();
    $emailService->sendSystemEmail($email, 'Webtool: Export Corpus to XML', 'test webtool sending email ' . $fname);



} catch (Exception $e) {
    var_dump($e->getMessage());
}