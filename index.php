<?php
$dir = dirname(__FILE__);
ini_set("error_reporting", "E_ALL & ~E_NOTICE & ~E_STRICT");
ini_set("display_errors", 1);
ini_set("log_errors",1);
ini_set("error_log","{$dir}/core/var/log/php_error.log");
ini_set("session.save_path",  sys_get_temp_dir());
$conf = dirname(__FILE__).'/core/conf/conf.php';
require_once($dir . '/vendor/autoload.php');
set_error_handler('Manager::errorHandler');
$dotenv = Dotenv\Dotenv::createMutable($dir);
$dotenv->load();
Manager::init($conf, $dir);
Manager::processRequest();