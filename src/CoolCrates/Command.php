<?php

namespace CoolCrates;

use CoolCrates\crate\Crate;
use pocketmine\command\Command as PMCommand;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Command extends PMCommand {
    
    /** @var Loader */
    private $loader;
    
    
    /**
     * Command constructor.
     * @param Loader $loader
     */
    public function __construct(Loader $loader) {
        $this->loader = $loader;
        parent::__construct("crate", "Crate command", null, []);
    }
    
    public function spawnCrate(Crate $crate, Position $position) {
        $position = $this->loader->getCrateManager()->addCrateBlock($crate, new Position($position->getFloorX(), $position->getFloorY(), $position->getFloorZ(), $position->level));
        $config = new Config($this->loader->getDataFolder() . "blocks.json");
        $all = $config->getAll();
        $all[] = [$crate->getIdentifier(), Utils::createPositionString($position)];
        $config->setAll($all);
        $config->save();
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if(isset($args[0])) {
            switch($args[0]) {
                case "keys":
                    if($sender instanceof Player) {
                        $session = $this->loader->getSessionManager()->getSession($sender);
                        if(empty($session->getCrateKeys())) {
                            $sender->sendMessage(TextFormat::RED . "You don't any crate key!");
                        } else {
                            $sender->sendMessage(Utils::translateColors("{GREEN}You have:"));
                            foreach(($crateKeys = $session->getCrateKeys()) as $key => $crateIdentifier) {
                                $crate = $this->loader->getCrateManager()->getCrate($key);
                                if($crate != null) {
                                    $sender->sendMessage(TextFormat::GRAY . " - " . TextFormat::WHITE . TextFormat::BOLD . $crateIdentifier . TextFormat::YELLOW . " " .
                                        $crate->getName() . " Keys");
                                }
                            }
                        }
                    } else {
                        $sender->sendMessage("Please, run this command in game");
                    }
                    break;
                case "sc":
                case "spawncoord":
                    if(!isset($args[4])) {
                        $sender->sendMessage(TextFormat::RED . "Usage: /crate spawncoord (x) (y) (z) (crate identifier) [level = default]");
                        return;
                    }
                    for($i = 1; $i < 4; $i++) {
                        if(!is_numeric($args[$i])) {
                            $sender->sendMessage(TextFormat::RED . "{$args[$i]} is not a valid coordinate!");
                            return;
                        }
                        $args[$i] = (int) $args[$i];
                    }
                    $crate = $this->loader->getCrateManager()->getCrate($args[4]);
                    if($crate == null) {
                        $sender->sendMessage(TextFormat::RED . "{$args[4]} is not a valid crate identifier");
                        return;
                    }
                    if(isset($args[5])) {
                        $level = $this->loader->getServer()->getLevelByName($args[5]);
                        $level = ($level != null) ? $level : $this->loader->getServer()->getDefaultLevel();
                    } else {
                        $level = $this->loader->getServer()->getDefaultLevel();
                    }
                    $this->spawnCrate($crate, new Position($args[1], $args[2], $args[3], $level));
                    $sender->sendMessage(TextFormat::GREEN . "Successfully spawn a {$crate->getName()} in {$level->getName()}");
                    break;
                case "spawn":
                    if($sender instanceof Player and $sender->isOp()) {
                        if(isset($args[1])) {
                            $crate = $this->loader->getCrateManager()->getCrate($args[1]);
                            if($crate != null) {
                                $this->spawnCrate($crate, $sender);
                                $sender->sendMessage(TextFormat::GREEN . "Successfully spawned a {$crate->getName()}");
                            } else {
                                $sender->sendMessage(TextFormat::RED . "{$args[1]} is not a valid crate identifier");
                            }
                        } else {
                            $sender->sendMessage(TextFormat::RED . "Usage: /crate spawn (crate identifier)");
                        }
                    }
                    break;
                case "give":
                    if($sender->isOp()) {
                        if(isset($args[1], $args[2])) {
                            $player = $this->loader->getServer()->getPlayerExact($args[1]);
                            if($player instanceof Player) {
                                $crate = $this->loader->getCrateManager()->getCrate($args[2]);
                                if($crate != null) {
                                    $session = $this->loader->getSessionManager()->getSession($player);
                                    if(isset($args[3]) and is_numeric($args[3])) {
                                        $amount = (int) $args[3];
                                    } else {
                                        $amount = 1;
                                    }
                                    $session->addCrateKey($crate->getIdentifier(), $amount);
                                    $sender->sendMessage(TextFormat::GREEN . "Added " . TextFormat::WHITE . TextFormat::BOLD . $amount . " {$crate->getName()} Key " . TextFormat::RESET . TextFormat::GREEN . "to {$player->getName()} successfully");
                                } else {
                                    $sender->sendMessage(TextFormat::RED . "{$args[2]} is not a valid crate identifier");
                                }
                            } else {
                                $sender->sendMessage(TextFormat::RED . "{$args[1]} is not a valid player!");
                            }
                        } else {
                            $sender->sendMessage(TextFormat::RED . "Usage: /crate give (player) (crate identifier) [amount=1]");
                        }
                    }
                    break;
                default:
                    if($sender->isOp()) {
                        $sender->sendMessage(TextFormat::RED . "Usage: /crate (spawn/give/wins)");
                    } else {
                        $sender->sendMessage(TextFormat::RED . "Usage: /crate wins");
                    }
                    break;
            }
        } else {
            if($sender->isOp()) {
                $sender->sendMessage(TextFormat::RED . "Usage: /crate (spawn/give/wins)");
            } else {
                $sender->sendMessage(TextFormat::RED . "Usage: /crate wins");
            }
        }
    }
    
}
