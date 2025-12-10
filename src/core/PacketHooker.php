<?php

declare(strict_types=1);

namespace core;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandOverload;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

use pocketmine\Server;

use core\command\BaseCommand;

class PacketHooker implements Listener {

    public function onDataPacketSend(DataPacketSendEvent $event) : void{
        $packets = $event->getPackets();
        foreach($packets as $packet){
            if($packet instanceof AvailableCommandsPacket){
                foreach($packet->commandData as $name => $commandData){
                    $command = Server::getInstance()->getCommandMap()->getCommand($name);
                    if($command instanceof BaseCommand){
                        $commandData->overloads = $this->generateOverloads($command);
                    }
                }
            }
        }
    }

    private function generateOverloads(BaseCommand $command) : array{
        $overloads = [];
        foreach($command->getSubCommands() as $label => $subCommand){
            if($subCommand->getName() !== $label){
                continue;
            }
            $scParam = CommandParameter::enum($label, new CommandEnum($label, [$label]), 0);
            $subOverloads = $this->generateSubCommandOverloads($subCommand);
            if(!empty($subOverloads)){
                foreach($subOverloads as $subOverload){
                    $params = array_merge([$scParam], $subOverload);
                    $overloads[] = new CommandOverload(false, $params);
                }
            }else{
                $overloads[] = new CommandOverload(false, [$scParam]);
            }
        }
        $baseOverloads = $this->generateArgumentOverloads($command->getArgumentList());
        foreach($baseOverloads as $overload){
            $overloads[] = new CommandOverload(false, $overload);
        }
        if(empty($overloads)){
            $overloads[] = new CommandOverload(false, []);
        }
        return $overloads;
    }

    private function generateSubCommandOverloads($subCommand) : array{
        return $this->generateArgumentOverloads($subCommand->getArgumentList());
    }

    private function generateArgumentOverloads(array $argumentList) : array{
        if(empty($argumentList)){
            return [];
        }
        $input = $argumentList;
        $combinations = [];
        $outputLength = array_product(array_map("count", $input));
        if($outputLength === 0){
            return [];
        }
        $indexes = [];
        foreach($input as $k => $charList){
            $indexes[$k] = 0;
        }
        do{
            $set = [];
            foreach($indexes as $k => $index){
                $set[] = clone $input[$k][$index]->getNetworkParameterData();
            }
            $combinations[] = $set;
            foreach($indexes as $k => $v){
                $indexes[$k]++;
                $lim = count($input[$k]);
                if($indexes[$k] >= $lim){
                    $indexes[$k] = 0;
                    continue;
                }
                break;
            }
        }while(count($combinations) !== $outputLength);
        return $combinations;
    }
}
