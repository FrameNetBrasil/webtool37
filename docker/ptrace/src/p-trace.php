<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php';
require 'Pusher.php';

$loop = React\EventLoop\Factory::create();
$pusher = new Pusher();

$socket = new React\Socket\TcpServer('0.0.0.0:9999');
$socket->on('connection', function (React\Socket\ConnectionInterface $connection) use ($pusher) {
    print_r("Hello " . $connection->getRemoteAddress() . "!\n");

    $connection->on('data', function ($data) use ($connection, $pusher) {
        print_r("-- received -- " . $data . "\n");
        $lines = explode("<record_start>", $data);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                print_r($line . PHP_EOL);
                $pusher->push($line);
            }
        }
    });

    $connection->on('end', function () {
        print_r('ended' . "\n");
    });

    $connection->on('error', function (Exception $e) {
        print_r('error: ' . $e->getMessage() . "\n");
    });

    $connection->on('close', function () {
        print_r('closed' . "\n");
    });
});

$wsServer = new IoServer(
    new HttpServer(new WsServer($pusher)),
    new React\Socket\SocketServer('0.0.0.0:9998'),
    $loop
);

echo "Running p-trace \n";
$loop->run();
