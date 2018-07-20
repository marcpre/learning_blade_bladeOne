<?php
require "vendor/autoload.php";

Use eftec\bladeone;
use DaveChild\TextStatistics as TS;

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
define("BLADEONE_MODE", 2); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.
$blade = new bladeone\BladeOne($views,$cache);

$textStatistics = new TS\TextStatistics;

// read in csv data
$rows = array_map(function($v){return str_getcsv($v, ",");}, file('./sampleData.csv'));
$header = array_shift($rows);
$csv = array();
foreach ($rows as $row) {
  $csv[] = array_combine($header, $row);
}

print_r($csv[0]);

foreach ($csv as $key => $value) {
    $output .= $blade->run("hello",$csv[$key]);
    $output .= "######################### \n";
    echo $output;
    echo 'Flesch-Kincaid Reading Ease: ' . $textStatistics->fleschKincaidReadingEase($output);
}

file_put_contents("./CONTENT_OUTPUT.txt", $output);

// echo $blade->run("hello",array("variable1"=>"value1"));
