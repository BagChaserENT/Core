<?php

declare(strict_types=1);

namespace core\economy;

use pocketmine\player\Player;

use pocketmine\utils\SingletonTrait;

use core\database\SQL;

use core\event\economy\money\BalanceChangeEvent;

final class Money {
    use SingletonTrait;

    public function isNew($player) : bool{
        $player = $player instanceof Player ? $player->getName() : $player;
        $query = SQL::getInstance()->getDatabase()->query("SELECT * FROM players WHERE name = '$player';");
        $result = $query->fetchArray(SQLITE3_ASSOC);
        if($result === false){
            return true;
        }
        return false;
    }

    public function create($player) : void{
        $player = $player instanceof Player ? $player->getName() : $player;
        SQL::getInstance()->getDatabase()->exec("INSERT INTO players (name, balance) VALUES ('$player', 1000);");
    }

    public function getBalance($player) : int{
        $player = $player instanceof Player ? $player->getName() : $player;
        $query = SQL::getInstance()->getDatabase()->query("SELECT * FROM players WHERE name = '$player';");
        $result = $query->fetchArray(SQLITE3_ASSOC);
        return $result["balance"];
    }

    public function addMoney($player, int $amount) : void{
        $player = $player instanceof Player ? $player->getName() : $player;
        $balance = $this->getBalance($player);
        $balance += $amount;
        $e = new BalanceChangeEvent($player, BalanceChangeEvent::TYPE_ADD);
        SQL::getInstance()->getDatabase()->exec("UPDATE players SET balance = $balance WHERE name = '$player';");
        $e->call();
    }

    public function removeMoney($player, int $amount) : void{
        $player = $player instanceof Player ? $player->getName() : $player;
        $balance = $this->getBalance($player);
        $balance -= $amount;
        $e = new BalanceChangeEvent($player, BalanceChangeEvent::TYPE_REMOVE);
        SQL::getInstance()->getDatabase()->exec("UPDATE players SET balance = $balance WHERE name = '$player';");
        $e->call();
    }

    public function setMoney($player, int $amount) : void{
        $player = $player instanceof Player ? $player->getName() : $player;
        $e = new BalanceChangeEvent($player, BalanceChangeEvent::TYPE_SET);
        SQL::getInstance()->getDatabase()->exec("UPDATE players SET balance = $amount WHERE name = '$player';");
        $e->call();
    }

    public function format(int $amount) : string{
        return "$" . number_format($amount);
    }
}