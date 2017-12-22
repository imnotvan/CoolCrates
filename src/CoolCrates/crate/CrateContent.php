<?php

namespace CoolCrates\crate;


use CoolCrates\Utils;

class CrateContent {
 
    /** @var string */
    private $rouletteMessage;
    
    /** @var array */
    private $commands = [];
    
    /** @var string */
    private $wonMessage;
    
    /**
     * CrateContent constructor.
     * @param string $rouletteMessage
     * @param array $commands
     * @param string $wonMessage
     */
    public function __construct(string $rouletteMessage, array $commands, string $wonMessage) {
        $this->rouletteMessage = Utils::translateColors($rouletteMessage);
        $this->commands = $commands;
        $this->wonMessage = Utils::translateColors($wonMessage);
    }
    
    /**
     * @return string
     */
    public function getRouletteMessage(): string {
        return $this->rouletteMessage;
    }
    
    /**
     * @return array
     */
    public function getCommands(): array {
        return $this->commands;
    }
    
    /**
     * @return string
     */
    public function getWonMessage(): string {
        return $this->wonMessage;
    }
    
    /**
     * @param string $rouletteMessage
     */
    public function setRouletteMessage(string $rouletteMessage) {
        $this->rouletteMessage = $rouletteMessage;
    }
    
    /**
     * @param array $commands
     */
    public function setCommands(array $commands) {
        $this->commands = $commands;
    }
    
    /**
     * @param string $wonMessage
     */
    public function setWonMessage(string $wonMessage) {
        $this->wonMessage = $wonMessage;
    }
    
}
