<?php

declare(strict_types=1);

namespace core\command\argument;

use pocketmine\command\CommandSender;

use core\command\error\ErrorCode;
use core\command\error\ErrorFormatter;

class ArgumentParser {

    /** @var BaseArgument[] */
    private array $arguments = [];
    private ErrorFormatter $errorFormatter;

    public function __construct(array $arguments, ErrorFormatter $errorFormatter){
        $this->arguments = $arguments;
        $this->errorFormatter = $errorFormatter;
    }

    public function parse(array $rawArgs, CommandSender $sender) : ParseResult{
        $parsedArgs = [];
        $errors = [];
        ksort($this->arguments);
        $positions = array_keys($this->arguments);
        $argIndex = 0;
        foreach($positions as $position){
            $possibleArgs = $this->arguments[$position];
            if($argIndex >= count($rawArgs)){
                foreach($possibleArgs as $arg){
                    if(!$arg->isOptional()){
                        $errors[] = $this->errorFormatter->format(
                            ErrorCode::MISSING_ARGUMENT,
                            $arg->getName(),
                            $arg->getTypeName()
                        );
                    }
                }
                continue;
            }
            $matched = false;
            foreach($possibleArgs as $arg){
                $spanLength = $arg->getSpanLength();
                if($spanLength === PHP_INT_MAX){
                    $input = implode(" ", array_slice($rawArgs, $argIndex));
                }else{
                    $input = $rawArgs[$argIndex];
                }
                if($arg->canParse($input, $sender)){
                    $parsedArgs[$arg->getName()] = $arg->parse($input, $sender);
                    if($spanLength === PHP_INT_MAX){
                        $argIndex = count($rawArgs);
                    }else{
                        $argIndex++;
                    }
                    $matched = true;
                    break;
                }
            }
            if(!$matched){
                $arg = $possibleArgs[0];
                if(!$arg->isOptional()){
                    $errors[] = $this->errorFormatter->format(
                        ErrorCode::INVALID_ARGUMENT,
                        $arg->getName(),
                        $arg->getTypeName(),
                        $rawArgs[$argIndex] ?? ""
                    );
                    $argIndex++;
                }
            }
        }
        if($argIndex < count($rawArgs)){
            $errors[] = $this->errorFormatter->format(ErrorCode::TOO_MANY_ARGUMENTS);
        }
        return new ParseResult($parsedArgs, $errors);
    }
}