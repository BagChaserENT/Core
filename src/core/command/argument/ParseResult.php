<?php

declare(strict_types=1);

namespace core\command\argument;

class ParseResult {

    public function __construct(
        protected array $parsedArgs,
        protected array $errors
    ){}

    public function isValid() : bool{
        return empty($this->errors);
    }

    public function getParsedArgs() : array{
        return $this->parsedArgs;
    }

    public function getErrors() : array{
        return $this->errors;
    }

    public function hasArg(string $name) : bool{
        return isset($this->parsedArgs[$name]);
    }

    public function getArg(string $name, mixed $default = null) : mixed{
        return $this->parsedArgs[$name] ?? $default;
    }
}