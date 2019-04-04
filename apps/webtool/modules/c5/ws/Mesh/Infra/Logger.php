<?php
/**
 * Created by PhpStorm.
 * User: ematos
 * Date: 21/07/2018
 * Time: 06:44
 */

namespace Mesh\Infra;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\SocketHandler;
use Monolog\Formatter\LineFormatter;

class Logger extends MonologLogger
{
    public function __construct($channelName)
    {
        parent::__construct($channelName);
        $handler = new SocketHandler('127.0.0.1:9999');
        $handler->setPersistent(true);
        $this->pushHandler($handler, Logger::DEBUG);
        // the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
        $output = "%level_name% > %message%\n";
// finally, create a formatter
        $formatter = new LineFormatter($output, '');

        $handler->setFormatter($formatter);
        //$this->info('My logger is now ready');
    }
}