<?php

declare(strict_types=1);

namespace core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\lang\Translatable;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use pocketmine\player\Player;

use core\command\argument\BaseArgument;
use core\command\argument\ArgumentParser;

use core\command\error\ErrorCode;
use core\command\error\ErrorFormatter;

abstract class BaseCommand extends Command implements PluginOwned {

    protected Plugin $plugin;

    /** @var BaseArgument[] */
    protected array $arguments = [];

    /** @var SubCommand[] */
    protected array $subCommands = [];

    /** @var string[] */
    protected array $subCommandAliases = [];

    protected ErrorFormatter $errorFormatter;

    protected bool $requiresPlayer = false;

    protected string $commandName = "";
    protected Translatable|string $commandDescription = "";
    protected array $commandAliases = [];

    public function __construct(Plugin $plugin){
        $this->plugin = $plugin;
        $this->errorFormatter = new ErrorFormatter();
        $this->setup();
        parent::__construct($this->commandName, $this->commandDescription, null, $this->commandAliases);
        $this->setUsage($this->generateUsage());
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }

    abstract protected function setup() : void;

    abstract protected function onExecute(CommandSender $sender, string $label, array $args) : void;

    public function setName(string $name) : void{
        $this->name = $name;
    }

    public function setDescription(Translatable|string $description) : void{
        $this->description = $description;
    }

    public function setAliases(array $aliases) : void{
        $this->aliases = $aliases;
    }

    public function setPlayerOnly(bool $value = true) : void{
        $this->requiresPlayer = $value;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
        if($this->requiresPlayer && !($sender instanceof Player)){
            $sender->sendMessage($this->errorFormatter->format(ErrorCode::INVALID_SENDER, "players"));
            return;
        }
        if(!$this->testPermission($sender)){
            return;
        }
        if(count($args) > 0){
            $subCommandName = strtolower($args[0]);
            if(isset($this->subCommandAliases[$subCommandName])){
                $subCommandName = $this->subCommandAliases[$subCommandName];
            }
            if(isset($this->subCommands[$subCommandName])){
                array_shift($args);
                $this->subCommands[$subCommandName]->execute($sender, $commandLabel, $args);
                return;
            }
        }
        $parser = new ArgumentParser($this->arguments, $this->errorFormatter);
        $result = $parser->parse($args, $sender);
        if(!$result->isValid()){
            foreach($result->getErrors() as $error){
                $sender->sendMessage($error);
            }
            $sender->sendMessage("Usage: " . $this->getUsage());
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

    protected function registerSubCommand(SubCommand $subCommand) : void{
        $this->subCommands[$subCommand->getName()] = $subCommand;
        foreach($subCommand->getAliases() as $alias){
            $this->subCommandAliases[strtolower($alias)] = $subCommand->getName();
        }
    }

    public function getSubCommands() : array{
        return $this->subCommands;
    }

    public function getArguments() : array{
        return $this->arguments;
    }

    protected function generateUsage() : string{
        $usage = "/" . $this->getName();
        if(!empty($this->subCommands)){
            $subNames = array_keys($this->subCommands);
            $usage .= " <" . implode("|", $subNames) . ">";
        }
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

    public function setErrorFormat(int $errorCode, string $format) : void{
        $this->errorFormatter->setFormat($errorCode, $format);
    }

    public function setErrorFormats(array $formats) : void{
        foreach($formats as $code => $format){
            $this->errorFormatter->setFormat($code, $format);
        }
    }
}