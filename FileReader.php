<?php
	
class FileReader {
	
	private string $path;
	
	public function __construct($path) {
		$this -> path = $path;
	}
	
	/**
	* This reads the set input directory and provides a list of files that can be processed
	* @return array
	*/
	public function getFiles(): array {
		$files = array();
		if ($handle = opendir($this -> path)) {
		    while (false !== ($file = readdir($handle))) {
		        if ('.' === $file) {
                    continue;
                }
		        if ('..' === $file) {
                    continue;
                }
				$files[] = $file;
		    }
		    closedir($handle);
		}
		return $files;
	}

    /**
     * Reads a given file and imports it into the given set
     * @param string $filename
     * @param SliceSet $set
     * @throws Exception
     */
	public function readFile(string $filename, SliceSet $set): void {
		$handle = fopen($this -> path . $filename, 'r');
		if ($handle) {
			$ln = 0;
		    while (($line = fgets($handle)) !== false && $ln++<=2) {
			    if($ln === 1) {
				    $vars = explode(' ', $line);
				    $set -> setMaximum((int)$vars[0]);
			    }
			    else if($ln === 2) {
				    $set -> setSliceOptions(explode(' ', $line));
			    }
		    }
		    fclose($handle);
		} else {
		    throw new \RuntimeException('This is not a valid file');
		} 
	}
	
}