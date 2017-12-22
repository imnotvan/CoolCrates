<?php

namespace CoolCrates\task;


use CoolCrates\crate\CrateBlock;
use CoolCrates\crate\CrateContent;
use CoolCrates\heart\HeartTask;
use CoolCrates\Loader;
use lib\Selector;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\level\particle\BubbleParticle;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\sound\ClickSound;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class PlayCrateTask extends HeartTask {
    
    /** @var Loader */
    private $loader;
    
    /** @var CrateBlock */
    private $block;
    
    /** @var FloatingTextParticle */
    private $previousParticle;
    
    /** @var FloatingTextParticle */
    private $currentParticle;
    
    /** @var FloatingTextParticle */
    private $nextParticle;
    
    /** @var Player */
    private $player;
    
    /** @var Selector */
    private $selector;
    
    /** @var bool */
    private $ended = false;
    
    /**
     * PlayCrateTask constructor.
     * @param Loader $loader
     * @param CrateBlock $block
     * @param Player $player
     */
    public function __construct(Loader $loader, CrateBlock $block, Player $player) {
        $loader->getSessionManager()->getSession($player)->setInCrate();
        
        $this->loader = $loader;
        $this->block = $block;
        $this->player = $player;
        
        $this->previousParticle = new FloatingTextParticle($block->add(0.5, 2.25, 0.5), "");
        $this->currentParticle = new FloatingTextParticle($block->add(0.5, 2, 0.5), "");
        $this->nextParticle = new FloatingTextParticle($block->add(0.5, 1.75, 0.5), "");
        $this->selector = new Selector($block->getCrate()->getContent());
        
        $block->level->addParticle($this->previousParticle, [$player]);
        $block->level->addParticle($this->currentParticle, [$player]);
        $block->level->addParticle($this->nextParticle, [$player]);
        
        parent::__construct(1);
    }
    
    private function move() {
        $this->block->level->addSound(new ClickSound($this->block), [$this->player]);
        $this->selector->next();
        $this->previousParticle->setTitle("" . $this->selector->getPrevious()->getRouletteMessage());
        $this->currentParticle->setTitle("§l§a»»»  " . $this->selector->current()->getRouletteMessage() . "  §a§l«««§r");
        $this->nextParticle->setTitle("" . $this->selector->getNext()->getRouletteMessage());
        $this->updateParticles();
        if($this->getPeriod() >= 16) {
            $this->ended = true;
        } elseif($this->getPeriod() > 10) {
            $this->setPeriod($this->getPeriod() + 5);
        } else {
            $this->setPeriod($this->getPeriod() + 1);
        }
    }
    
    public function tick() {
        if($this->ended) {
            $session = $this->loader->getSessionManager()->getSession($this->player);
            if($session != null) {
                /** @var CrateContent $content */
                $content = $this->selector->current();
                $username = $this->player->getName();
                $victoryMessage = str_replace("{player}", $username, $content->getWonMessage());
                $this->previousParticle->setInvisible();
                $this->nextParticle->setInvisible();
                $this->currentParticle->setTitle($victoryMessage);
                $level = $this->block->level;
                $level->addSound(new EndermanTeleportSound($this->block), [$this->player]);
                $level->addParticle(new BubbleParticle($this->block), [$this->player]);
                $this->updateParticles();
                $this->player->sendMessage($victoryMessage);
                $server = $this->loader->getServer();
                $session->removeCrateKey($this->block->getCrate()->getIdentifier());
                foreach($content->getCommands() as $command) {
                    $server->dispatchCommand(new ConsoleCommandSender(), str_replace("{player}", $username, $command));
                }
                $server->getScheduler()->scheduleDelayedTask(new RemoveCrateTask($this->loader, $this->currentParticle, $this->player), 120);
            }
            $this->stop();
        } else {
            $this->move();
        }
    }
    
    private function updateParticles() {
        foreach($this->previousParticle->encode() as $packet) {
            $this->player->dataPacket($packet);
        }
        foreach($this->currentParticle->encode() as $packet) {
            $this->player->dataPacket($packet);
        }
        foreach($this->nextParticle->encode() as $packet) {
            $this->player->dataPacket($packet);
        }
    }
    
}
