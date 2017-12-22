<?php

namespace CoolCrates\session;


use CoolCrates\crate\CrateBlock;
use CoolCrates\crate\CrateFloatingText;
use CoolCrates\Loader;
use CoolCrates\Utils;
use pocketmine\Player;
use pocketmine\utils\Config;

class Session {
    
    /** @var Loader */
    private $loader;
    
    /** @var Player */
    private $owner;
    
    /** @var Config */
    private $config;
    
    /** @var array */
    private $crateKeys = [];
    
    /** @var bool */
    private $inCrate = false;
    
    /** @var CrateFloatingText[] */
    private $crateParticles = [];
    
    /**
     * Session constructor.
     * @param Loader $loader
     * @param Player $owner
     */
    public function __construct(Loader $loader, Player $owner) {
        $this->loader = $loader;
        $this->owner = $owner;
        $name = strtolower($owner->getName());
        $this->config = new Config($loader->getDataFolder() . "users/{$name}.json", Config::JSON, [
            "crateKeys" => []
        ]);
        $this->crateKeys = $this->config->get("crateKeys");
    }
    
    /**
     * @return Player
     */
    public function getOwner(): Player {
        return $this->owner;
    }
    
    /**
     * @return array
     */
    public function getCrateKeys(): array {
        return $this->crateKeys;
    }
    
    /**
     * @return bool
     */
    public function isInCrate(): bool {
        return $this->inCrate;
    }
    
    /**
     * @param string $identifier
     * @return int
     */
    public function getCrateKey(string $identifier): int {
        return $this->crateKeys[$identifier] ?? 0;
    }
    
    /**
     * @param string $identifier
     * @return bool
     */
    public function hasCrateKey(string $identifier) {
        if(isset($this->crateKeys[$identifier])) {
            return $this->crateKeys[$identifier] > 0;
        } else {
            return false;
        }
    }
    
    /**
     * @return CrateFloatingText[]
     */
    public function getCrateParticles(): array {
        return $this->crateParticles;
    }
    
    /**
     * @param string $identifier
     * @param int $amount
     */
    public function addCrateKey(string $identifier, int $amount = 1) {
        $currentAmount = ($this->crateKeys[$identifier] ?? 0) + $amount;
        if($currentAmount < 0) {
            $currentAmount = 0;
        }
        $this->crateKeys[$identifier] = $currentAmount;
        $this->updateCrateParticles();
    }
    
    /**
     * @param string $identifier
     * @param int $amount
     */
    public function removeCrateKey(string $identifier, int $amount = 1) {
        $this->addCrateKey($identifier, -$amount);
    }
    
    /**
     * @param bool $inCrate
     */
    public function setInCrate(bool $inCrate = true) {
        $this->inCrate = $inCrate;
    }
    
    /**
     * @param CrateBlock $crateBlock
     */
    public function addCrateParticle(CrateBlock $crateBlock) {
        $particle = new CrateFloatingText($crateBlock->getCrate(), $crateBlock->add(0.5, 0.5, 0.5), Utils::translateColors("{GRAY}You have {DARK_GRAY}{$this->getCrateKey($crateBlock->getCrate()->getIdentifier())} {GRAY}keys"));
        $crateBlock->level->addParticle($particle, [$this->owner]);
        $this->crateParticles[] = $particle;
    }
    
    public function updateCrateParticles() {
        foreach($this->crateParticles as $particle) {
            $particle->setTitle(Utils::translateColors("{GRAY}You have {DARK_GRAY}{$this->getCrateKey($particle->getCrate()->getIdentifier())} {GRAY}keys"));
            foreach($particle->encode() as $packet) {
                $this->owner->dataPacket($packet);
            }
        }
    }
    
    public function despawnCrateParticles() {
        foreach($this->crateParticles as $particle) {
            $particle->setInvisible();
            foreach($particle->encode() as $packet) {
                $this->owner->dataPacket($packet);
            }
        }
    }
    
    /**
     * Session destructor.
     */
    public function __destruct() {
        $this->config->set("crateKeys", $this->crateKeys);
        $this->config->save();
    }
    
}
