<?php

namespace CoolCrates;


use CoolCrates\crate\CrateManager;
use CoolCrates\heart\Heart;
use CoolCrates\session\SessionManager;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase {
    
    /** @var Loader */
    private static $instance;
    
    /** @var Heart */
    private $heart;
    
    /** @var SessionManager */
    private $sessionManager;
    
    /** @var CrateManager */
    private $crateManager;
    
    public function onLoad() {
        if(!is_dir($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }
        self::$instance = $this;
    }
    
    public function onEnable() {
        $this->heart = new Heart($this);
        $this->sessionManager = new SessionManager($this);
        $this->crateManager = new CrateManager($this);
        $this->getServer()->getPluginManager()->registerEvents(new Listener($this), $this);
        $this->getServer()->getCommandMap()->register("crate", new Command($this));
        $this->getLogger()->info("CoolCrates has been enabled");
    }
    
    public function onDisable() {
        $this->sessionManager->closeAll();
        $this->getLogger()->info("CoolCrates has been disabled");
    }
    
    /**
     * @return Loader
     */
    public static function getInstance(): Loader {
        return self::$instance;
    }
    
    /**
     * @return Heart
     */
    public function getHeart(): Heart {
        return $this->heart;
    }
    
    /**
     * @return SessionManager
     */
    public function getSessionManager(): SessionManager {
        return $this->sessionManager;
    }
    
    /**
     * @return CrateManager
     */
    public function getCrateManager(): CrateManager {
        return $this->crateManager;
    }
    
}
