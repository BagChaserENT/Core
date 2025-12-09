<?php

declare(strict_types=1);

namespace core\command\argument\type;

use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

use core\command\argument\BaseArgument;

class BooleanArgument extends BaseArgument {

    private const TRUE_VALUES = ["true"];
    private const FALSE_VALUES = ["false"];

    public function getTypeName() : string{
        return "bool";
    }

    public function getNetworkType() : int{
        return AvailableCommandsPacket::ARG_TYPE_STRING;
    }

    public function canParse(string $input, CommandSender $sender) : bool{
        $lower = strtolower($input);
        return in_array($lower, self::TRUE_VALUES, true) || in_array($lower, self::FALSE_VALUES, true);
    }

    public function parse(string $input, CommandSender $sender) : bool{
        return in_array(strtolower($input), self::TRUE_VALUES, true);
    }
}