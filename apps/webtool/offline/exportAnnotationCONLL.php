<?php
// Diretorio do script corrente
$dir = dirname(__FILE__);

// Path do Maestro
$dir = realpath(dirname($dir,3));
require_once($dir . '/vendor/autoload.php');
//
// Configuração para tratamento de erros
ini_set("error_reporting", E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set("log_errors", "on");
ini_set("error_log", $dir . "/core/var/log/php_error.log");
//
//// Inclusão do framework
$conf = $dir . '/core/conf/conf.php';
require_once($dir . '/core/classes/manager.php');
set_error_handler('Manager::errorHandler');

$dotenv = Dotenv\Dotenv::createMutable($dir);
$dotenv->load();
// Inicialização do framework
Manager::init($conf, $dir);
Manager::initialize();

//$app = 'webtool';
//$db = 'webtool';

$documentEntry = $argv[1];
$fileName = $argv[2];

//$_REQUEST['documentEntry'] = $documentEntry;
//$_REQUEST['filename'] = $fileName;
//// Endereco do servico a ser executado
//$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . "{$app}/api/data/exportDocumentToCONLL";

$configFile = $dir . "/apps/webtool/conf/conf.php";

Manager::loadConf($configFile);
Manager::setConf('logs.level', 2);
Manager::setConf('logs.port', 9999);

Manager::setConf('fnbr.db', 'webtool');

mdump("documentEntry = " . $documentEntry);
mdump("fileName = " . $fileName);

require_once($dir . '/apps/webtool/services/DataService.php');

$document = new fnbr\models\Document();
$document->getByEntry($documentEntry);
$service = new DataService();
//            $conll = $service->exportDocumentToCONLL($document);
//            $fileName = $document->getName() . '.conll.txt';
//            $mfile = MFile::file($conll, false, $fileName);
//            $this->renderFile($mfile);
$lines = $service->exportDocumentToCONLL($document);
file_put_contents($fileName, $lines);

