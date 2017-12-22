<?php

namespace CoolCrates\heart;


use CoolCrates\Loader;

abstract class HeartTask {
    
    /** @var int */
    private $ticked;
    
    /** @var int */
    private $toTick;
    
    /**
     * HeartTask constructor
     *
     * @param int $period
     */
    public function __construct(int $period) {
        $this->ticked = 0;
        $this->toTick = $period;
    }
    
    /**
     * @return int
     */
    public function getPeriod(): int {
        return $this->toTick;
    }
    
    /**
     * @param int $period
     */
    public function setPeriod(int $period) {
        $this->toTick = $period;
    }
    
    public final function run() {
        if($this->ticked >= $this->toTick) {
            $this->ticked = 0;
            $this->tick();
        } else {
            $this->ticked++;
        }
    }
    
    public abstract function tick();
    
    public function stop() {
        Loader::getInstance()->getHeart()->stopTask($this);
    }
    
}
