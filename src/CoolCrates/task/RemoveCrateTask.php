<?php

namespace CoolCrates\task;


use CoolCrates\Loader;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\Player;
use pocketmine\scheduler\Task;

class RemoveCrateTask extends Task {
    
    /** @var Loader */
    private $loader;
    
    /** @var FloatingTextParticle */
    private $particle;
    
    /** @var Player */
    private $player;
    
    /**
     * RemoveCrateTask constructor.
     * @param Loader $loader
     * @param FloatingTextParticle $particle
     * @param Player $player
     */
    public function __construct(Loader $loader, FloatingTextParticle $particle, Player $player) {
        $this->loader = $loader;
        $this->particle = $particle;
        $this->player = $player;
    }
    
    public function onRun($currentTick) {
        $this->particle->setInvisible();
        foreach($this->particle->encode() as $packet) {
            $this->player->dataPacket($packet);
        }
        $this->loader->getSessionManager()->getSession($this->player)->setInCrate(false);
    }
    
}
