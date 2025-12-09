<?php

declare(strict_types=1);

namespace core\economy\command;

use pocketmine\command\CommandSender;

use core\command\BaseCommand;
use core\command\argument\type\PlayerArgument;

use core\economy\Money;

class SeeBalanceCommand extends BaseCommand {

    protected function setup() : void{
        $this->setName("seebalance");
        $this->setDescription("Check someone's balance");
        $this->setAliases(["seebal"]);
        $this->setPermission("core.cmd.balance");
        $this->registerArgument(0, new PlayerArgument("player"));
    }

    protected function onExecute(CommandSender $sender, string $label, array $args) : void{
        $money = Money::getInstance();
        if($money->isNew($args["player"])){
            $sender->sendMessage("Player not found!");
            return;
        }
        $sender->sendMessage($args["player"] . " balance is " . $money->format($money->getBalance($args["player"])));
    }
}