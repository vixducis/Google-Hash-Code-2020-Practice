<?php
	
class SliceSet {
	
	private int $maximum;
	private array $slice_options = [];
	private ?Slices $optimal_slices = null;
	private float $precision;
	
	public function __construct(float $precision=0.001) {
		$this -> setPrecision($precision);
	}
	
	/**
	* Sets the maximum number of slices that should be achieved (or as close to)
	* @param int $maximum
	*/
	public function setMaximum(int $maximum): void {
		$this -> maximum = $maximum;
	}
	
	/**
	* Sets the array with all the numbers that can be added to reach the maximum.
	* @param array $slice_options
	*/
	public function setSliceOptions(array $slice_options): void {
		$this -> slice_options = $slice_options;
	}
	
	/**
	* Sets the precision that should be used to perform the calculation
	* Lower precision should yield better results
	* Precision should always be between 0 and 1.
	* @param float $precision
	*/
	public function setPrecision(float $precision): void {
		$this -> precision = $precision;
	}
	
	/**
	* Returns the optimal slices for the given set
	* This will execute the calculation and cache the result
	* @return Slices
	*/
	public function getOptimalSlices(): Slices {
		if($this -> optimal_slices === null) {
			$this -> calculate();
		}
		return $this -> optimal_slices;
	}
		
	/**
	* Checsk an array for elements that are very closely together.
	* It will remove any elements that are too close to one another.
	* @param array $input The array you want to process
	* @param float $delta The precision you want to reach. 0 < $delta < 1
	* @return array
	*/
	private function trim(array $input, float $delta): array {
		$prev = array_shift($input);
		$last = array_pop($input);
		$output = [$prev];
		$prev = $prev -> getTotal();
		foreach($input as $val) {
			if($val -> getTotal() > $prev * ($delta + 1)) {
				$output[] = $val;
			}
			$prev = $val -> getTotal();
		}
		$output[] = $last;
		return $output;
	}
	
	/**
	* Loops over all the elements in an array
    * Performs an add-operation on the Slice object
    * Add the object to the array again
	* @param array $input
	* @param int $to_add
    * @param int $array_index
	*/
	private function ArrayAddMerge(array &$input, int $to_add, int $array_index): void {
		foreach ($input as $slice) {
		    $new_slice = clone $slice;
		    $new_slice -> add($to_add, $array_index);
		    $input[] = $new_slice;
		}
	}
	
	
	/**
	* Loops over an array of 'Slices' and removes all elements thtat have a total larger then a given maximum.
	* @param array $input
	* @param int $max
	* @return array
	*/
	private function removeValuesLargerThan(array $input, int $max): array {
		foreach ($input as $k => $v) {
		    if($v -> getTotal() > $max) {
		    	unset($input[$k]);
		    }
		}
		$input = array_values($input);
		return $input;
	}
	
	/**
	* This will perform the actual calculation of the optimal solution
	* Also pritns out progress
	*/
	private function calculate(): void {
	    //initialize and empty Slices object and add it to the stack
		$starting_slice = new Slices();
		$calc_array = [$starting_slice];

		//reverse sort the original array, this greatly improves results
        arsort($this -> slice_options);

		foreach($this -> slice_options as $key => $val) {
			$this -> ArrayAddMerge($calc_array, (int)$val, (int)$key );
			$calc_array = $this -> removeValuesLargerThan($calc_array, $this -> maximum);
			
			//let's sort the array by ascending totals
			usort($calc_array, static function($a, $b) {
				if ($a -> getTotal() === $b -> getTotal()) {
						return 0;
				}
				return ($a -> getTotal() < $b -> getTotal()) ? -1 : 1;
			});
			
			//trim the array for values too close together
			$delta = $this -> precision;
			$calc_array = $this -> trim($calc_array, $delta);
			echo 'to process: '
                . $key
                . '/'
                . count($this -> slice_options)
                . ', best solution: '
                . end($calc_array) -> getTotal()
                . PHP_EOL;
		}
		$this -> optimal_slices = end($calc_array);
	}
	
}