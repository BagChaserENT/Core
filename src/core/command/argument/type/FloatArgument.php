<?php

declare(strict_types=1);

namespace core\command\argument\type;

use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

use core\command\argument\BaseArgument;

class FloatArgument extends BaseArgument {

    protected ?float $min;
    protected ?float $max;

    public function __construct(
        string $name,
        bool $isOptional = false,
        ?float $min = null,
        ?float $max = null
    ){
        parent::__construct($name, $isOptional);
        $this->min = $min;
        $this->max = $max;
    }

    public function getTypeName() : string{
        return "float";
    }

    public function getNetworkType() : int{
         return AvailableCommandsPacket::ARG_TYPE_FLOAT;
    }

    public function canParse(string $input, CommandSender $sender) : bool{
        if(!is_numeric($input)){
            return false;
        }
        $value = (float) $input;
        if($this->min !== null && $value < $this->min){
            return false;
        }
        if($this->max !== null && $value > $this->max){
            return false;
        }
        return true;
    }

    public function parse(string $input, CommandSender $sender) : float{
        return (float) $input;
    }
}