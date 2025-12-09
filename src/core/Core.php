<?php

declare(strict_types=1);

namespace core;

use pocketmine\plugin\PluginBase;

use core\database\SQL;

use core\economy\command\BalanceCommand;
use core\economy\command\SeeBalanceCommand;

class Core extends PluginBase {

    protected static self $instance;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->registerAll("core", [
            new BalanceCommand($this),
            new SeeBalanceCommand($this)
        ]);
    }

    protected function onDisable() : void{
        SQL::getInstance()->close();
    }

    public static function getInstance() : self{
        return self::$instance;
    }
}