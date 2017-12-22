<?php

namespace CoolCrates\session;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

class SessionListener implements Listener {
    
    /** @var SessionManager */
    private $manager;
    
    /**
     * SessionListener constructor.
     * @param SessionManager $manager
     */
    public function __construct(SessionManager $manager) {
        $this->manager = $manager;
    }
    
    /**
     * @param PlayerLoginEvent $event
     */
    public function onLogin(PlayerLoginEvent $event) {
        $this->manager->openSession($event->getPlayer());
    }
    
    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event) {
        $this->manager->closeSession($event->getPlayer());
    }
    
}
