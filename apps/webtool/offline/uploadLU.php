<?php
include "offline.php";
//require_once($dir . '/apps/fnbr/vendor/autoload.php');

$app = 'fnbr';
$db = 'mfn';

$idLanguage = $argv[1];
$fileName = $argv[2];
$start = $argv[3] ?: 1;
$end = $argv[4] ?: 1000000;

$_REQUEST['idLanguage'] = $idLanguage;
// Endereco do servico a ser executado
$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . "{$app}/utils/import/importLUOffline";

$configFile = Manager::getHome() . "/apps/{$app}/conf/conf.php";
Manager::loadConf($configFile);
Manager::setConf('logs.level', 2);
Manager::setConf('logs.port', 9998);

Manager::setConf('fnbr.db', $db);

mdump("fileName = " . $fileName);
mdump("idLanguage = " . $idLanguage);
mdump("start = " . $start);
mdump("end = " . $end);

$rows = [];
$numLine = 0;
$count = 0;
$fh = fopen($fileName, "r");
if ( $fh ) {
    while ( !feof($fh) ) {
        $line = fgets($fh);
        ++$numLine;
        if (($numLine >= $start) && ($numLine <= $end)) {
            $rows[] = mb_ereg_replace("\s","",$line);
            if (($numLine % 500) == 0) {
                echo "sending... numline = " . $numLine . " rows = " . count($rows) . "\n";
                $_REQUEST['idLanguage'] = $idLanguage;
                $_REQUEST['rows'] = $rows;
                Manager::processRequest(true);
                $rows = [];
                $_SESSION = [];
            }
        } elseif ($numLine > $end) {
            break;
        }
    }
    fclose($fh);
}
if (count($rows) > 0) {
    print_r($rows);
    echo "sending... numline = " . $numLine . " rows = " . count($rows) . "\n";
    $_REQUEST['idLanguage'] = $idLanguage;
    $_REQUEST['rows'] = $rows;
    Manager::processRequest(true);
}