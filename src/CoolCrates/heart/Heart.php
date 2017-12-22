<?php

namespace CoolCrates\heart;


use CoolCrates\Loader;

class Heart {
    
    /** @var HeartTask[] */
    private $taskPool = [];
    
    /**
     * Heart constructor.
     * @param Loader $loader
     */
    public function __construct(Loader $loader) {
        $loader->getServer()->getScheduler()->scheduleRepeatingTask(new HeartBeat($this), 1);
    }
    
    /**
     * @param HeartTask $task
     */
    public function startTask(HeartTask $task) {
        $this->taskPool[] = $task;
    }
    
    /**
     * @param HeartTask $task
     */
    public function stopTask(HeartTask $task) {
        if(in_array($task, $this->taskPool)) {
            unset($this->taskPool[array_search($task, $this->taskPool)]);
        }
    }
    
    public function run() {
        foreach($this->taskPool as $task) {
            $task->run();
        }
    }
    
}
