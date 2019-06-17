<?php
$program = $argv[1];
$cmd = "php " . $program;
$timer = popen("start /B ". $cmd . ' ' . $argv[2] . ' ' . $argv[3] . ' ' . $argv[4], "r");
sleep(30);
pclose($timer);
