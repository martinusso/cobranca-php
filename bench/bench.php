<?php

require_once __DIR__ . '/../autoload.php';

use Boleto\Barcode;

$dir = sprintf(__DIR__ . '\images');
(!file_exists($dir)) && mkdir($dir);

$times = [1, 10, 100, 1000, 5000];

foreach ($times as $t) {
  $startTime = getTime();
  gerarBarcode($t);
  $endTime = getTime();
  $totalTime = ($endTime - $startTime);
  echo sprintf("%u times (%f) \n", $t, $totalTime);
}
array_map('unlink', glob($dir . "\*"));
rmdir($dir);


function gerarBarcode($t)
{
  for ($i=1; $i <= $t; $i++) {
    $numero = str_pad($t, 10, '0', STR_PAD_LEFT) .
    str_pad($i, 10, '0', STR_PAD_LEFT);
    $codigo_barras = '238012345640420006190018' . $numero;
    Barcode::generate($codigo_barras);
  }
}

function getTime()
{
  $mtime = microtime();
  $mtime = explode(" ",$mtime);
  return $mtime[1] + $mtime[0];
}
