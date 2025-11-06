<?php

namespace App\Actions;

class StatusAction
{
    public static function getNextStatuses(string $status): array
    {
        return match ($status) {
            "Pending", "Pendente"   => ["Detalhes", "Triagem ou Classificação"],
            "Aprovada Classificação" => ["Detalhes", "Solicitar Opinião", "Responder ao Reclamante", "Encaminhar ao Provedor"],
            "Negada Classificação" => ["Detalhes"],
            "Encaminhado ao Provedor" => ["Detalhes", "Solicitar Opinião"],
            "Respondida pelo Provedor" => ["Detalhes", "Solicitar Opinião", "Responder ao Reclamante", "Encaminhar ao Provedor"],
            "Respondida ao Reclamante" => ["Detalhes"],
            default                 => [],
        };
    }
}
