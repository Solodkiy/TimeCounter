#! /usr/bin/php
<?php
require_once "TimeCounter.php";

$stdin = fopen('php://stdin', 'r');
$stdout = fopen('php://stdout', 'w');

$text = '';

while (!feof($stdin)) {
    $text = $text.fgetc($stdin);
}
fputs($stdout, str_pad('', 20, '-').PHP_EOL);
$timeCounter = new TimeCounter($text);

$totalTime = $timeCounter->getTotalTime();
fputs($stdout, $totalTime);