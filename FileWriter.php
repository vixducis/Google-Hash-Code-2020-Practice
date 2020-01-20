<?php
	
class FileWriter {
	
	private string $path;
	
	public function __construct($path) {
		$this -> path = $path;
	}
	
	/**
	* Requests the optimal set from the given SliceSet and writes it to a file with the given filename
	* @param string $filename
	* @param SliceSet $set
	*/
	public function write(string $filename, SliceSet $set): void {
		$file = fopen($this -> path . $filename, 'w') or die('Unable to open file.');
		$slices = $set -> getOptimalSlices();
		$data = $slices -> getTotal() . "\n" . implode(' ', $slices -> getTerms());
		fwrite($file, $data);
		fclose($file);
	}
	
	/**
	* Provides an output file. This will replace the .in-extension with an .out-extension
	* @param string $input_filename
	* @return string
	*/
	public function getOutputFilename(string $input_filename): string {
		return str_replace('.in', '.out', $input_filename);
	}	
}