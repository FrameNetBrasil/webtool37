<?php
ini_set("error_reporting", "E_ALL & ~E_NOTICE & ~E_STRICT");
ini_set("log_errors", "on");
ini_set("error_log", "php_error.log");

require 'bootstrap.php';
require 'C5Server.php';

use Thruway\Transport\PawlTransportProvider;

$client = new C5Server('c5realm');
$client->addTransportProvider(new PawlTransportProvider("ws://127.0.0.1:9090/"));
$client->start();

