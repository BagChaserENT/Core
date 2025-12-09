<?php

declare(strict_types=1);

namespace core\command\argument\type;

use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

use core\command\argument\BaseArgument;

class EnumArgument extends BaseArgument {

    protected array $values;
    protected bool $caseSensitive;
    protected string $enumName;

    public function __construct(
        string $name, array $values,
        bool $isOptional = false,
        bool $caseSensitive = false
    ){
        parent::__construct($name, $isOptional);
        $this->values = $values;
        $this->caseSensitive = $caseSensitive;
        $this->enumName = $name;
    }

    public function getTypeName() : string{
        return implode("|", $this->values);
    }

    public function getNetworkType() : int{
        return AvailableCommandsPacket::ARG_TYPE_STRING;
    }

    public function canParse(string $input, CommandSender $sender) : bool{
        if($this->caseSensitive){
            return in_array($input, $this->values, true);
        }
        $lower = strtolower($input);
        foreach($this->values as $value){
            if(strtolower($value) === $lower){
                return true;
            }
        }
        return false;
    }

    public function parse(string $input, CommandSender $sender) : string{
        if($this->caseSensitive){
            return $input;
        }
        $lower = strtolower($input);
        foreach($this->values as $value){
            if(strtolower($value) === $lower){
                return $value;
            }
        }
        return $input;
    }

    public function getValues() : array{
        return $this->values;
    }

    public function getEnumName() : string{
        return $this->enumName;
    }

    public function setEnumName(string $name) : void{
        $this->enumName = $name;
    }
}