<?php
require_once "TimeCounter.php";

$stdin = fopen('php://stdin', 'r');
$stdout = fopen('php://stdout', 'r');

$text = '';

while(!feof($stdin)) {
    $text = $text.fgets($stdin);
}

$timeCounter = new TimeCounter($text);

$totalTime = $timeCounter->getTotalTime();
fputs($stdout, $totalTime);