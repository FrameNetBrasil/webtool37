<?php
$program = $argv[1];
$cmd = "php " . $program;
$timer = popen("start /B ". $cmd . ' ' . $argv[2] . ' ' . $argv[3] . ' ' . $argv[4]. ' ' . $argv[5] . ' ' . $argv[6] , "r");
sleep(30);
pclose($timer);
