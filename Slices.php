<?php

class Slices
{

    private int $total = 0;
    private array $terms = array();

    /**
     * Ads a number to the slices.
     * This will update the internal total and it to the terms for later retrieval
     * @param int $num
     * @param int $index
     */
    public function add(int $num, int $index): void
    {
        $this->total += $num;
        $this->terms [] = $index;
    }

    /**
     * returns the internal total
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Returns the number of types of pizza
     * @return int
     */
    public function getSliceCount(): int
    {
        return count($this->terms);
    }

    /**
     * Returns an array with all the terms that make up the total
     * @return array
     */
    public function getTerms(): array
    {
        sort($this->terms);
        return $this->terms;
    }

}