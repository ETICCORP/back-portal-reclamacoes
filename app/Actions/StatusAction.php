<?php 

namespace App\Actions;

class StatusAction
{
    public static function getNextStatuses(string $status): array
    {
        return match ($status) {
            "Pending", "Pendente"      => ["Detalhes", "Triagem ou Classificação"],
            "Aprovada Classificação"   => ["Detalhes", "Solicitar Opinião", "Responder ao Reclamante", "Encaminhar ao Provedor"],
            "Negada Classificação"     => ["Detalhes"],
            "Encaminhado ao Provedor"  => ["Detalhes", "Solicitar Opinião"],
            "Respondida pelo Provedor" => ["Detalhes", "Solicitar Opinião", "Responder ao Reclamante", "Encaminhar ao Provedor"],
            "Respondida ao Reclamante" => ["Detalhes"],
            default                    => [],
        };
    }

    public static function getStatusDescription(string $status): string
    {
        return match ($status) {
            "Aprovada Classificação"   => "A sua exposição foi validada tecnicamente e seguirá para análise interna profunda.",
            "Negada Classificação"     => "Após análise, informamos que a sua exposição não preenche os requisitos para prosseguimento.",
            "Encaminhado ao Provedor"  => "Enviámos os detalhes ao provedor responsável para obter esclarecimentos adicionais.",
            "Respondida pelo Provedor" => "Recebemos o feedback do provedor e estamos a consolidar a resposta final.",
            "Respondida ao Reclamante" => "O seu processo foi concluído. A resposta oficial já se encontra disponível.",
            default                    => "O seu processo registou uma nova movimentação e está a ser processado pela nossa equipa."
        };
    }
}