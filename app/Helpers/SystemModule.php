<?php

namespace App\Helpers;

class SystemModule
{
    /**
     * @var string|null Nome do módulo do sistema
     */
    private static ?string $module = null;
    
    /**
     * Define o nome do módulo do sistema
     *
     * @param string|null $module Nome do módulo do sistema
     * @param bool $forceUpdate Forçar atualização do módulo caso já tenha sido setado
     * @return void
     */
    public static function set(?string $module, bool $forceUpdate = false): void
    {
        if (isset(self::$module) && $forceUpdate === false) {
            return;
        }
        
        self::$module = $module ? mb_strtoupper($module) : null;
    }
    
    /**
     * Retorna o nome do módulo do sistema
     *
     * @return string|null
     */
    public static function get(): ?string
    {
        return self::$module;
    }
    
    /**
     * Verifica se o módulo do sistema foi definido
     *
     * @return bool
     */
    public static function isSet(): bool
    {
        return isset(self::$module);
    }
    
    /**
     * Limpa o nome do módulo do sistema
     *
     * @return void
     */
    public static function clear(): void
    {
        self::$module = null;
    }
}