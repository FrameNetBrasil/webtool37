<?php
/**
 * Created by PhpStorm.
 * User: ematos
 * Date: 19/07/2018
 * Time: 10:29
 */
use ORM\Service\FrameService;

$container = require __DIR__ . '/../../DI/bootstrap.php';

$frame = $container->get(FrameService::class);
foreach($frame->listAllSQL() as $f) {
    print_r($f);
};

