<?php

namespace CoolCrates\session;


use CoolCrates\Loader;
use pocketmine\Player;

class SessionManager {
    
    /** @var Loader */
    private $loader;
    
    /** @var Session[] */
    private $sessionPool = [];
    
    /**
     * SessionManager constructor.
     * @param Loader $loader
     */
    public function __construct(Loader $loader) {
        if(!is_dir($loader->getDataFolder() . "users")) {
            mkdir($loader->getDataFolder() . "users");
        }
        $this->loader = $loader;
        $this->openAll();
        $loader->getServer()->getPluginManager()->registerEvents(new SessionListener($this), $loader);
    }
    
    /**
     * @return Session[]
     */
    public function getSessionPool(): array {
        return $this->sessionPool;
    }
    
    /**
     * @param Player $owner
     * @return Session|null
     */
    public function getSession(Player $owner) {
        return $this->sessionPool[$owner->getName()] ?? null;
    }
    
    /**
     * @param Player $owner
     */
    public function openSession(Player $owner) {
        $this->sessionPool[$owner->getName()] = new Session($this->loader, $owner);
    }
    
    public function openAll() {
        foreach($this->loader->getServer()->getOnlinePlayers() as $player) {
            $this->openSession($player);
        }
    }
    
    /**
     * @param Player $owner
     */
    public function closeSession(Player $owner) {
        if(isset($this->sessionPool[$owner->getName()])) {
            unset($this->sessionPool[$owner->getName()]);
        }
    }
    
    public function closeAll() {
        foreach($this->sessionPool as $session) {
            $session->despawnCrateParticles();
            $this->closeSession($session->getOwner());
        }
    }
    
}
