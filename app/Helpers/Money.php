<?php

namespace App\Helpers;

class Money
{
    /**
     * Converte um valor em reais para centavos.
     *
     * @param mixed $value
     * @return int
     */
    public static function toCents(string|float $value): int
    {
        return intval(round($value * 100));
    }

    /**
     * Converte um valor em centavos para reais.
     *
     * @param mixed $value
     * @return string
     */
    public static function toReal(int $value): string
    {
        return number_format($value / 100, 2, '.', '');
    }
    
    /**
     * Formata o valor para o formato monetário BRL.
     *
     * @param mixed $value
     * @return string
     */
    public static function format(string|float $value): string
    {
        return number_format((float) $value, 2, ',', '.');
    }
}
