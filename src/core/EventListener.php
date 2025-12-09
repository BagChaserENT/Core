<?php

declare(strict_types=1);

namespace core;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

use core\economy\Money;

class EventListener implements Listener {

    public function onJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $money = Money::getInstance();
        if($money->isNew($player)){
            $money->create($player);
        }
    }
}