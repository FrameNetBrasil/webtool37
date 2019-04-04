<?php

namespace Thruway\Peer;

require 'bootstrap.php';

use Thruway\ClientSession;
use Thruway\Peer\Client;
use Thruway\Transport\PawlTransportProvider;

$client = new Client("c5realm");
$client->addTransportProvider(new PawlTransportProvider("ws://127.0.0.1:9090/"));

$client->on('open', function (ClientSession $session) {

    /*
    // 1) subscribe to a topic
    $onevent = function ($args) {
        echo "Event {$args[0]}\n";
    };
    $session->subscribe('com.myapp.hello', $onevent);

    // 2) publish an event
    $session->publish('com.myapp.hello', ['Hello, world from PHP!!!'], [], ["acknowledge" => true])->then(
        function () {
            echo "Publish Acknowledged!\n";
        },
        function ($error) {
            // publish failed
            echo "Publish Error {$error}\n";
        }
    );

    // 3) register a procedure for remoting
    $add2 = function ($args) {
        return $args[0] + $args[1];
    };
    $session->register('com.myapp.add2', $add2);

    // 4) call a remote procedure
    */

    $procedureName = 'br.ufjf.framenetbr.c5.loadFullNetwork';
    $params =  ['cxn_dativo_com_infinitivo_obrigacao'];

    $session->call($procedureName, $params)->then(
        function ($res) {
            echo "Result: {$res}\n";
            die;
        },
        function ($error) {
            echo "Call Error: {$error}\n";
            die;
        }
    );

});


$client->start();
