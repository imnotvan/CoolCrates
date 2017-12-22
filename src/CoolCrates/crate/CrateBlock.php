<?php

namespace CoolCrates\crate;


use CoolCrates\Loader;
use CoolCrates\Utils;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\tile\Tile;

class CrateBlock extends Position {
    
    /** @var FloatingTextParticle */
    private $header;
    
    /** @var Crate */
    private $crate;
    
    /**
     * CratePosition constructor.
     * @param Crate $crate
     * @param int $x
     * @param int $y
     * @param int $z
     * @param Level $level
     */
    public function __construct(Crate $crate, $x, $y, $z, Level $level) {
        $this->crate = $crate;
        $this->header = new FloatingTextParticle(new Vector3($x + 0.5, $y + 1, $z + 0.5), "", Utils::translateColors($crate->getName()));
        parent::__construct($x, $y, $z, $level);
        
        if($level->getBlock($this)->getId() == Block::AIR) {
            $nbt = new CompoundTag("", [
                new ListTag("Items", []),
                new StringTag("id", Tile::CHEST),
                new IntTag("x", $x),
                new IntTag("y", $y),
                new IntTag("z", $z)
            ]);
            $nbt->Items->setTagType(NBT::TAG_Compound);
            $level->setBlock($this, Block::get(Block::CHEST));
            $level->addTile(Tile::createTile("Chest", $level, $nbt));
        }
        
        $this->spawnToAll();
        
    }
    
    /**
     * @return FloatingTextParticle
     */
    public function getHeader(): FloatingTextParticle {
        return $this->header;
    }
    
    /**
     * @return Crate
     */
    public function getCrate(): Crate {
        return $this->crate;
    }
    
    /**
     * @param Position $position
     * @return bool
     */
    public function isTouching(Position $position): bool {
        return $position->equals($this) and $this->level === $position->level;
    }
    
    /**
     * @param Player $player
     */
    public function spawnTo(Player $player) {
        $this->level->addParticle($this->header, [$player]);
        Loader::getInstance()->getSessionManager()->getSession($player)->addCrateParticle($this);
    }
    
    public function spawnToAll() {
        foreach(Server::getInstance()->getOnlinePlayers() as $player) {
            $this->spawnTo($player);
        }
    }
    
}
