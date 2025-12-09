<?php

declare(strict_types=1);

namespace core\command\argument\type;

use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

use pocketmine\Server;

use core\command\argument\BaseArgument;

class PlayerArgument extends BaseArgument {

    public function getTypeName() : string{
        return "player";
    }

    public function getNetworkType() : int{
        return AvailableCommandsPacket::ARG_TYPE_TARGET;
    }

    public function canParse(string $input, CommandSender $sender) : bool{
        return Server::getInstance()->getPlayerExact($input) !== null;
    }

    public function parse(string $input, CommandSender $sender) : string{
        return $input;
    }

    public function getEnumValues() : array{
        $players = [];
        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            $players[] = $player->getName();
        }
        return $players;
    }

    public function hasEnumValues() : bool{
        return true;
    }
}