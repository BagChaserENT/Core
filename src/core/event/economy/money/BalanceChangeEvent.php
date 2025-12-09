<?php

declare(strict_types=1);

namespace core\event\economy\money;

use pocketmine\event\Event;

class BalanceChangeEvent extends Event {

    public const TYPE_ADD = 0;
    public const TYPE_REMOVE = 1;
    public const TYPE_SET = 2;

    public function __construct(
        protected string $name,
        protected int $type
    ){}

    public function getName() : string{
        return $this->name;
    
    public function getType() : int{
        return $this->type;
    }
}