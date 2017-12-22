<?php

namespace CoolCrates\crate;

use CoolCrates\Loader;
use CoolCrates\task\PlayCrateTask;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Crate {
    
    /** @var Loader */
    private $loader;
    
    /** @var string */
    private $name;
    
    /** @var string */
    private $identifier;
    
    /** @var CrateContent[] */
    private $content = [];
    
    /**
     * Crate constructor.
     * @param Loader $loader
     * @param string $name
     * @param string $identifier
     * @param array $content
     */
    public function __construct(Loader $loader, string $name, string $identifier, array $content) {
        $this->loader = $loader;
        $this->name = $name;
        $this->identifier = $identifier;
        $this->content = $content;
    }
    
    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
    
    /**
     * @return string
     */
    public function getIdentifier(): string {
        return $this->identifier;
    }
    
    /**
     * @return CrateContent[]
     */
    public function getContent(): array {
        return $this->content;
    }
    
    /**
     * @param CrateBlock $block
     * @param Player $player
     */
    public function execute(CrateBlock $block, Player $player) {
        $session = $this->loader->getSessionManager()->getSession($player);
        if(!$session->isInCrate()) {
            if($session->hasCrateKey($this->identifier)) {
                $this->loader->getHeart()->startTask(new PlayCrateTask($this->loader, $block, $player));
            } else {
                $player->sendMessage(TextFormat::DARK_PURPLE . TextFormat::BOLD . "[Crates]" . TextFormat::RESET . " You need a {$this->name} key to open this crate");
            }
        }
    }
    
}
