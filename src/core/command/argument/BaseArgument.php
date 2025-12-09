<?php

declare(strict_types=1);

namespace core\command\argument;

use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

abstract class BaseArgument {

    protected string $name;
    protected bool $optional;

    public function __construct(string $name, bool $optional = false){
         $this->name = $name;
         $this->optional = $optional;
    }

    public function getName() : string{
        return $this->name;
    }

    public function isOptional() : bool{
        return $this->optional;
    }

    abstract public function getNetworkType() : int;

    abstract public function canParse(string $input, CommandSender $sender) : bool;

    abstract public function parse(string $input, CommandSender $sender) : mixed;

    public function getSpanLength() : int{
        return 1;
    }
}