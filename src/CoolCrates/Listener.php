<?php

namespace CoolCrates;

use pocketmine\event\Listener as PluginListener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;

class Listener implements PluginListener {
    
    /** @var Loader */
    private $loader;
    
    /**
     * Listener constructor.
     * @param Loader $loader
     */
    public function __construct(Loader $loader) {
        $this->loader = $loader;
    }
    
    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event) {
        foreach($this->loader->getCrateManager()->getBlockPool() as $block) {
            $block->spawnTo($event->getPlayer());
        }
    }
    
    /**
     * @param PlayerInteractEvent $event
     */
    public function onTouch(PlayerInteractEvent $event) {
        foreach($this->loader->getCrateManager()->getBlockPool() as $block) {
            if($block->isTouching($event->getBlock())) {
                $block->getCrate()->execute($block, $event->getPlayer());
                $event->setCancelled();
                break;
            }
        }
    }
    
}
