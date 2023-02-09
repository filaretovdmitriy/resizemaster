<?php

require '../vendor/autoload.php';

$inputDir = __DIR__ . '/input/';
$outputDir = __DIR__ . '/output/';

echo 'Start' . PHP_EOL;

function convert($size): string
{
    $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
    return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

$file = 'test1.jpg';
//$file = 'test2.png';
//$file = 'test3.svg';
//$file = 'test4.webp';

$resize1 = new \aisol\resize\Resize($inputDir . $file);
$resize1->autoRotateImage();
//$resize1->cover(4000, 3000);
//$resize1->fitAuto(450, 400);
$resize1->containBox(450, 400);
$resize1->toFile($outputDir . $file);

echo 'Memory - ' . convert(memory_get_peak_usage(true)) . PHP_EOL;

echo 'End' . PHP_EOL;
