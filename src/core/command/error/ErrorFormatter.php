<?php

declare(strict_types=1);

namespace core\command\error;

class ErrorFormatter {

    private array $formats = [
        ErrorCode::MISSING_ARGUMENT => "Missing required argument: {name} ({type})",
        ErrorCode::INVALID_ARGUMENT => "Invalid value for argument '{name}' (expected {type}, got '{value}')",
        ErrorCode::INVALID_SENDER => "This command can only be executed by {expected}",
        ErrorCode::NO_PERMISSION => "You don't have permission to use this command",
        ErrorCode::PLAYER_NOT_FOUND => "Player '{name}' not found",
        ErrorCode::TOO_MANY_ARGUMENTS => "Too many arguments provided"
    ];

    public function setFormat(int $errorCode, string $format) : void{
        $this->formats[$errorCode] = $format;
    }

    public function getFormat(int $errorCode) : string{
        return $this->formats[$errorCode] ?? "Unknown error";
    }

    public function format(int $errorCode, string ...$values) : string{
        $format = $this->getFormat($errorCode);
        $replacements = match($errorCode){
            ErrorCode::MISSING_ARGUMENT => ["{name}" => $values[0] ?? "", "{types}" => $values[1] ?? ""],
            ErrorCode::INVALID_ARGUMENT => ["{name}" => $values[0] ?? "", "{type}" => $values[1] ?? "", "{value}" => $values[2] ?? ""],
            ErrorCode::INVALID_SENDER => ["{expected}" => $values[0] ?? ""],
            ErrorCode::PLAYER_NOT_FOUND => ["{name}" => $values[0] ?? ""],
            default => []
        };
        return str_replace(array_keys($replacements), array_values($replacements), $format);
    }
}