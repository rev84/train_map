<?php

define('CSV_STATION', dirname(__FILE__).'/csvs/station20230327free.csv');
define('CSV_LINE', dirname(__FILE__).'/csvs/line20230327free.csv');
define('OUTPUT', dirname(__FILE__).'/../js/stations.js');

$stations = csv2Ary(CSV_STATION);
$lines = lineCsv2Ary(CSV_LINE);

$json = [];



function lineCsv2Ary($filepath)
{
  $lines = csv2Ary($filepath);
  $res = [];
  foreach ($lines as $line) {
    $res[$line['line_cd']] = $line['line_name'];
  }
  return $res;
}

function csv2Ary($filepath)
{
  $results = [];
  $handle = fopen($filepath, "r");
  $header = fgetcsv($handle);
  while (($data = fgetcsv($handle)) !== FALSE) {
    $num = count($data);
    $line = [];
    for ($c = 0; $c < $num; $c++) {
      $line[$header[$c]] = $data[$c];
    }
    $results[] = $line;
  }

  return $results;
}