<?php

class Slices {

	private $total = 0;
	private $terms = array();

	/**
	* Ads a number to the slices. 
	* This will update the internal total and it to the terms for later retrieval
	* @param int $num
	*/
	public function add(int $num) {
		$this -> total += $num;
		$this -> terms [] = $num;
	}

	/**
	* returns the internal total
	* @return int
	*/
	public function getTotal(): int {
		return $this -> total;
	}	

	/*
	* Returns an array with all the terms that make up the total
	* @return array
	*/
	public function getTerms(): array {
		return $this -> terms;
	}
	
}