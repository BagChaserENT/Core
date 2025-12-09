<?php

declare(strict_types=1);

namespace core\command\argument\type;

use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

use core\command\argument\BaseArgument;

class TextArgument extends BaseArgument {

    public function getTypeName() : string{
        return "text";
    }

    public function getNetworkType() : int{
        return AvailableCommandsPacket::ARG_TYPE_RAWTEXT;
    }

    public function canParse(string $input, CommandSender $sender) : bool{
        return $input !== "";
    }

    public function parse(string $input, CommandSender $sender) : string{
        return $input;
    }

    public function getSpanLength() : int{
        return PHP_INT_MAX;
    }
}