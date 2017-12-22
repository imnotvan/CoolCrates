<?php

namespace CoolCrates\heart;


use pocketmine\scheduler\Task;

class HeartBeat extends Task {
    
    /** @var Heart */
    private $heart;
    
    /**
     * HeartBeat constructor.
     * @param Heart $heart
     */
    public function __construct(Heart $heart) {
        $this->heart = $heart;
    }
    
    public function onRun($currentTick) {
        $this->heart->run();
    }
    
}
