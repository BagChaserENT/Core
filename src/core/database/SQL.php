<?php

declare(strict_types=1);

namespace core\database;

use SQLite3;

use pocketmine\utils\SingletonTrait;

use core\Core;

final class SQL {
    use SingletonTrait;

    protected SQLite3 $sql;

    public function __construct(){
        $this->sql = new SQLite3(Core::getInstance()->getDataFolder() . "database.db");
        $this->sql->exec("CREATE TABLE IF NOT EXISTS players (name TEXT PRIMARY KEY, balance INTEGER);");
    }

    public function close() : void{
        $this->sql->close();
    }

    public function getDatabase() : SQLite3{
        return $this->sql;
    }
}