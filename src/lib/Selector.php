<?php

namespace lib;


/**
 * Class Selector
 * @author Hysland w/ GiantQuartz
 * @package lib
 */
class Selector {
    
    /** @var int */
    private $currentKey;
    
    /** @var array */
    private $values = [];
    
    /**
     * Selector constructor.
     *
     * @param array $values
     * @param null  $defaultIndex
     */
    public function __construct(array $values, $defaultIndex = null) {
        $this->values = $values;
        if($defaultIndex != null) {
            $this->currentKey = $defaultIndex;
        } else {
            $this->currentKey = array_rand($values);
        }
    }
    
    public function prev() {
        if(($this->currentKey - 1) < min(array_keys($this->values))) {
            $this->currentKey = max(array_keys($this->values));
        } else {
            $this->currentKey = $this->currentKey - 1;
        }
    }
    
    public function current() {
        return $this->values[$this->currentKey];
    }
    
    public function next() {
        if(($this->currentKey + 1) > max(array_keys($this->values))) {
            $this->currentKey = min(array_keys($this->values));
        } else {
            $this->currentKey = $this->currentKey + 1;
        }
    }
    
    /**
     * @param int $previousIndex
     *
     * @return mixed
     */
    public function getPrevious(int $previousIndex = 1) {
        $currentKey = $this->currentKey;
        for($i = 0; $i < $previousIndex; $i++) {
            if(($currentKey - 1) < min(array_keys($this->values))) {
                $currentKey = max(array_keys($this->values));
            } else {
                $currentKey = $currentKey - 1;
            }
        }
        return $this->values[$currentKey];
    }
    
    /**
     * @param int $nextIndex
     *
     * @return mixed
     */
    public function getNext(int $nextIndex = 1) {
        $currentKey = $this->currentKey;
        for($i = 0; $i < $nextIndex; $i++) {
            if(($currentKey + 1) > max(array_keys($this->values))) {
                $currentKey = min(array_keys($this->values));
            } else {
                $currentKey = $currentKey + 1;
            }
        }
        return $this->values[$currentKey];
    }
    
    /**
     * @return int
     */
    public function getCurrentKey(): int {
        return $this->currentKey;
    }
    
    /**
     * @return array
     */
    public function getValues(): array {
        return $this->values;
    }
    
}
