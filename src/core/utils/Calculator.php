<?php

declare(strict_types=1);

namespace core\utils;

final class Calculator {

    public static function handleAddition(int $a, int $b) : int{
        return $a + $b;
    }

    public static function handleSubtraction(int $a, int $b) : int{
        return $a - $b;
    }

    public static function handleMultiplication(int $a, int $b) : int{
        return $a * $b;
    }

    public static function handleDivision(int $a, int $b) : int{
        return $a / $b;
    }

    public static function handleModulo(int $a, int $b) : int{
        return $a % $b;
    }

    public static function handleExponentiation(int $a, int $b) : int{
        return $a ** $b;
    }

    public static function handleSquareRoot(int $a) : int{
        return sqrt($a);
    }

    public static function handleAbsoluteValue(int $a) : int{
        return abs($a);
    }
}