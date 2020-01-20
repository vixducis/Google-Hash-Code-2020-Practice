<?php
	
require('SliceSet.php');
require('FileReader.php');
require('FileWriter.php');
require('Slices.php');

$input_path = './input/';
$output_path = './output/';

/*
* The precision that should be used to perform the calculation
* Lower precision should yield better results 
* but will also increase processing time and memory usage exponentially
* Precision should always be between 0 and 1.
*/
$precision = 0.0001;

$reader = new FileReader($input_path);
$writer = new FileWriter($output_path);
$files = $reader -> getFiles();

foreach($files as $file){
	$set = new SliceSet($precision);
	$reader -> readFile($file, $set);
	$writer -> write($writer -> getOutputFilename($file), $set);
}