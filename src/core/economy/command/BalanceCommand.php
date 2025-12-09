<?php

declare(strict_types=1);

namespace core\economy\command;

use pocketmine\command\CommandSender;

use core\command\BaseCommand;

use core\economy\Money;

class BalanceCommand extends BaseCommand {

    protected function setup() : void{
        $this->setName("balance");
        $this->setDescription("Check your balance");
        $this->setAliases(["bal"]);
        $this->setPlayerOnly(true);
        $this->setPermission("core.cmd.balance");
    }

    protected function onExecute(CommandSender $sender, string $label, array $args) : void{
        $money = Money::getInstance();
        $sender->sendMessage("Your balance is " . $money->format($money->getBalance($sender)));
    }
}