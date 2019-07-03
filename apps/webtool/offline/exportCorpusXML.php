<?php
$dirScript = dirname(__FILE__);

include $dirScript . "/offline.php";
include $dirScript . "/../services/EmailService.php";

$app = 'webtool';
$db = 'webtool';

$corpusEntry = $argv[1];
$documents = $argv[2];
$idLanguage = $argv[3];
$idUser = $argv[4];
$email = $argv[5];

try {

    $dirName = realpath(Manager::getAbsolutePath("apps/{$app}/files")) . '/' . $idUser;

    if (!file_exists($dirName)) {
        if (!mkdir($dirName, 0777, true)) {
            throw new Exception('Fail on folder creation.');
        }
    }

    $_REQUEST['corpusEntry'] = $corpusEntry;
    $_REQUEST['documents'] = $documents;
    $_REQUEST['idLanguage'] = $idLanguage;
    $_REQUEST['dirName'] = $dirName;

// Endereco do servico a ser executado
    $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . "{$app}/api/data/exportCorpusToXML";

    $configFile = Manager::getHome() . "/apps/{$app}/conf/conf.php";
    Manager::loadConf($configFile);
    Manager::setConf('logs.level', 2);
    Manager::setConf('logs.port', 9999);
var_dump(Manager::getConf('logs'));
    Manager::setConf('fnbr.db', $db);

    Manager::processRequest(true);


    $fname = $corpusEntry . '_' . date('Ymd') . '_' . date('Hi') . '_xml.zip';
    $fileName = $dirName . '/' . $fname;
    if (file_exists($fileName)) {
        unlink($fileName);
    }

    $i = 0;
    $pd = new \PharData($fileName);
    $scandir = scandir($dirName) ?: [];
    $scandir = array_diff($scandir, ['..', '.']);
    foreach ($scandir as $filePath) {
        $pathParts = pathinfo($filePath);
        if ($pathParts['extension'] == 'xml') {
            $pd->addFile($dirName . '/' . $filePath, $filePath);
            ++$i;
        }
    }
    foreach ($scandir as $filePath) {
        $pathParts = pathinfo($filePath);
        if ($pathParts['extension'] == 'xml') {
            //unlink($dirName . '/' . $filePath);
        }
    }

    if ($i > 0) {
        $url = Manager::getConf('options.baseURL') . "/apps/webtool/files/{$idUser}/{$fname}";

        $emailService = new EmailService();
        $emailService->sendSystemEmail($email, 'Webtool: Export Corpus to XML', "The requested XML file is available at <a href='{$url}'>{$fname}</a><br>FNBr Webtool Team");
    }


} catch (Exception $e) {
    var_dump($e->getMessage());
}