<?php

namespace App\Actions;

class StatusAction
{
    public static function getNextStatuses(string $status): array
    {
        return match ($status) {
            "Pending", "Pendente"   => ["Em Progresso", "Arquivar"],
            "Em Progresso"          => ["Resolvido", "Arquivar"],
            "Arquivar", "Arquivado" => ["Pendente"],
            "Resolvido"             => [],
            default                 => [],
        };
    }
}
