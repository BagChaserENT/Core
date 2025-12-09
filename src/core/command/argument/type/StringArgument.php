<?php

declare(strict_types=1);

namespace core\command\argument\type;

use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

use core\command\argument\BaseArgument;

class StringArgument extends BaseArgument {

    public function getTypeName() : string{
        return "string";
    }

    public function getNetworkType() : int{
        return AvailableCommandsPacket::ARG_TYPE_STRING;
    }

    public function canParse(string $input, CommandSender $sender) : bool{
        return $input !== "";
    }

    public function parse(string $input, CommandSender $sender) : string{
        return $input;
    }
}