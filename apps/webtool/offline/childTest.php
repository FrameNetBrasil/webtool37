<?php
$dirScript = dirname(__FILE__);
var_dump($dirScript);
include $dirScript . "/offline.php";
include $dirScript . "/../services/EmailService.php";
var_dump('a');
$app = 'webtool';
$db = 'webtool';

$documentEntry = $argv[1];
$fileName = $argv[2];
$language = $argv[3];
var_dump('c');


$emailService = new EmailService();
$emailService->sendSystemEmail('ely.matos@gmail.com', 'Test Webtool', 'test webtool sending email');