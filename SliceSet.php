<?php
	
class SliceSet {
	
	private int $maximum;
	private array $slice_options = [];
	private ?array $optimal_slices = null;
	private float $precision;
	
	function __construct(float $precision=0.001) {
		$this -> setPrecision($precision);
	}
	
	/**
	* Sets the maximum number of slices that should be achieved (or as close to)
	* @param int $maximum
	*/
	public function setMaximum(int $maximum) {
		$this -> maximum = $maximum;
	}
	
	/**
	* Sets the array with all the numbers that can be added to reach the maximum.
	* @param array $slice_options
	*/
	public function setSliceOptions(array $slice_options) {
		$this -> slice_options = $slice_options;
	}
	
	/**
	* Sets the precision that should be used to perform the calculation
	* Lower precision should yield better results
	* Precision should always be between 0 and 1.
	* @param float $precision
	*/
	public function setPrecision(float $precision) {
		$this -> precision = $precision;
	}
	
	/**
	* Returns the optimal slices for the given set
	* This will execute the calculation and cache the result
	* @return array
	*/
	public function getOptimalSlices(): array {
		if($this -> optimal_slices === null) {
			$this -> calculate();
		}
		$output = [];
		foreach($this -> slice_options as $key => $val) {
		    if(in_array($val, $this -> optimal_slices, false)) {
		        $output[] = $key;
            }
        }
		return $output;
	}
	
	/**
	* Returns the total sum for the optimal solution found
	* @return int
	*/
	public function getOptimalSlicesTotal(): int {
        if($this -> optimal_slices === null) {
            $this -> calculate();
        }
		return array_sum($this -> optimal_slices);
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
	* Provides a deep copy of an array.
	* This makes sure the objects in the array or cloned instead of referenced
	* Then it adds a given integer to all elments of the array
	* @param array $input
	* @param int $to_add
	* @return array
	*/
	private function cloneArrayAndAdd(array $input, int $to_add): array {
		foreach ($input as $k => $v) {
		    $output[$k] = clone $v;
			$output[$k] -> add($to_add);
		}
		return $output;
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
	private function calculate() {
		$starting_slice = new Slices();
		$calc_array = [$starting_slice];
		$values_to_process = $this -> slice_options;
		rsort($values_to_process);
		foreach($values_to_process as $key => $val) {
			$temp_array = $this -> cloneArrayAndAdd($calc_array, intval($val));
			$calc_array = array_merge($calc_array, $temp_array);
			$calc_array = $this -> removeValuesLargerThan($calc_array, $this -> maximum);
			
			//let's sort the array by asceding totals
			usort($calc_array, function($a,$b) {
				if ($a -> getTotal() == $b -> getTotal()) {
						return 0;
				}
				return ($a -> getTotal() < $b -> getTotal()) ? -1 : 1;
			});
			
			//trim the array for values too close together
			$delta = $this -> precision;
			$calc_array = $this -> trim($calc_array, $delta);
			echo "progress: ".$key.'/'.count($this -> slice_options). ', best solution: ' . end($calc_array) -> getTotal().PHP_EOL ;
		}
		$winner = end($calc_array);
		$this -> optimal_slices = $winner -> getTerms();
	}
	
}