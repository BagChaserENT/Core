<?php

declare(strict_types=1);

namespace core\command;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\lang\Translatable;

use core\command\argument\BaseArgument;
use core\command\argument\ArgumentParser;

use core\command\error\ErrorCode;
use core\command\error\ErrorFormatter;

abstract class SubCommand {

    /** @var BaseArgument[] */
    protected array $arguments = [];

    protected ErrorFormatter $errorFormatter;

    protected bool $requiresPlayer = false;

    protected string $commandName = "";
    protected Translatable|string $commandDescription = "";
    protected array $commandAliases = [];
    protected string $permission = "";

    public function __construct(){
        $this->errorFormatter = new ErrorFormatter();
        $this->setup();
    }

    abstract protected function setup() : void;

    abstract protected function onExecute(CommandSender $sender, string $label, array $args) : void;

    public function setName(string $name) : void{
        $this->commandName = $name;
    }

    public function setDescription(Translatable|string $description) : void{
        $this->commandDescription = $description;
    }

    public function setAliases(array $aliases) : void{
        $this->commandAliases = array_map("strtolower", $aliases);
    }

    public function setPlayerOnly(bool $value = true){
        $this->requiresPlayer = $value;
    }

    public function setPermission(string $permission) : void{
        $this->permission = $permission;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
        if($this->requiresPlayer && !($sender instanceof Player)){
            $sender->sendMessage($this->errorFormatter->format(ErrorCode::INVALID_SENDER, "players"));
            return;
        }
        if($this->permission !== "" && !$sender->hasPermission($this->permission)){
            $sender->sendMessage($this->errorFormatter->format(ErrorCode::NO_PERMISSION));
            return;
        }
        $parser = new ArgumentParser($this->arguments, $this->errorFormatter);
        $result = $parser->parse($args, $sender);
        if(!$result->isValid()){
            foreach($result->getErrors() as $error){
                $sender->sendMessage($error);
            }
            $sender->sendMessage("Usage: " . $this->generateUsage());
            return;
        }
        $this->onExecute($sender, $commandLabel, $result->getParsedArgs());
    }

    protected function registerArgument(int $position, BaseArgument $argument) : void{
        if(!isset($this->arguments[$position])){
            $this->arguments[$position] = [];
        }
        $this->arguments[$position][] = $argument;
    }

    public function getName() : string{
        return $this->commandName;
    }

    public function getDescription() : Translatable|string{
        return $this->commandDescription;
    }

    public function getAliases() : array{
        return $this->commandAliases;
    }

    public function getPermission() : string{
        return $this->permission;
    }

    public function getArguments() : array{
        return $this->arguments;
    }

    public function getUsage(string $parentCommand = "") : string{
        $usage = "/" . $parentCommand . " " . $this->commandName;
        ksort($this->arguments);
        foreach($this->arguments as $position => $args){
            $argNames = [];
            foreach($args as $arg){
                $argNames[] = $arg->getName() . ":" . $arg->getTypeName();
            }
            $argStr = implode("|", $argNames);
            $isOptional = $args[0]->isOptional();
            $usage .= $isOptional ? " [" . $argStr . "] " : " <" . $argStr . "> ";
        }
        return $usage;
    }
}