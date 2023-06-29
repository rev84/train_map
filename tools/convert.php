<?php

setlocale(LC_ALL, 'C');

define('CSV_STATION', dirname(__FILE__).'/csvs/station20230327free.csv');
define('CSV_LINE', dirname(__FILE__).'/csvs/line20230327free.csv');
define('CSV_JOIN', dirname(__FILE__).'/csvs/join20230327.csv');
define('OUTPUT', dirname(__FILE__).'/../js/data.js');

$stations = stationCsv2Ary(CSV_STATION);
$lines = lineCsv2Ary(CSV_LINE);
$joins = joinCsv2Ary(CSV_JOIN);

$jsonAry = [
  'stations' => $stations,
  'lines' => $lines,
  'joins' => $joins,
];

$outputStr = 'const DATA = '.json_encode($jsonAry, JSON_UNESCAPED_UNICODE).';';

file_put_contents(OUTPUT, $outputStr);

function stationCsv2Ary($filepath)
{
  $stations = csv2Ary($filepath, [
    'station_cd', 'station_g_cd', 'station_name', 'line_cd', 'lon', 'lat', 'e_status', 
  ]);
  $results = [];
  foreach ($stations as $station) {
    if ($station['e_status'] == "0") {
      $results[] = $station;
    }
  }

  return $results;
}

function joinCsv2Ary($filepath)
{
  $joins = csv2Ary($filepath);
  $results = [];

  foreach ($joins as $join) {
    $lineCd = $join['line_cd'];
    $st1 = $join['station_cd1'];
    $st2 = $join['station_cd2'];

    if (!isset($results[$st1])) {
      $results[$st1] = [];
    }
    if (!isset($results[$st1][$st2])) {
      $results[$st1][$st2] = [];
    }
    $results[$st1][$st2][] = $lineCd;

    if (!isset($results[$st2])) {
      $results[$st2] = [];
    }
    if (!isset($results[$st2][$st1])) {
      $results[$st2][$st1] = [];
    }
    $results[$st2][$st1][] = $lineCd;
  }

  return $results;
}

function lineCsv2Ary($filepath)
{
  $lines = csv2Ary($filepath, [
    'line_cd', 'line_name',
  ]);
  $res = [];
  foreach ($lines as $line) {
    $res[$line['line_cd']] = $line['line_name'];
  }
  return $res;
}

function csv2Ary($filepath, $importHeaders = [])
{
  $handle = fopen($filepath, 'r');

  $results = [];

  $header = fgetcsv($handle);
  while (($data = fgetcsv($handle)) !== FALSE) {
    $num = count($data);
    $line = [];
    for ($c = 0; $c < $num; $c++) {
      if (empty($importHeaders) || in_array($header[$c], $importHeaders)) {
        $line[$header[$c]] = $data[$c];
      }
    }
    $results[] = $line;
  }

  return $results;
}