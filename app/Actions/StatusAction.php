<?php

namespace App\Actions;

class StatusAction
{
    public static function getNextStatuses(string $status): array
    {
        return match ($status) {
            "Pending", "Pendente"      => ["Detalhes", "Triagem ou Classificação", "Devolver ao Reclamante"],
            "Aprovada Classificação"   => ["Detalhes", "Solicitar Opinião", "Responder ao Reclamante", "Devolver ao Reclamante", "Encaminhar ao Provedor"],
            "Negada Classificação"     => ["Detalhes"],
            "Devolvida ao Reclamante" => ["Detalhes", "Triagem ou Classificação"],
            "Encaminhado ao Provedor"  => ["Detalhes", "Solicitar Opinião"],
            "Respondida pelo Provedor" => ["Detalhes", "Solicitar Opinião", "Responder ao Reclamante", "Encaminhar ao Provedor"],
            "Respondida ao Reclamante" => ["Detalhes"],
            default                    => [],
        };
    }

    /**
     * retorna o assunto do email com base no status e código da reclamação
     * @param string $status o status atual da reclamação
     * @param string $code o código da reclamação para incluir no assunto caso seja necessário
     * @return string
     */
    public static function getStatusSubject(string $status, string $code): string
    {
        return match ($status) {
            "Aprovada Classificação"   => "Exposição Aprovada para Análise",
            "Negada Classificação"     => "Atualização: Exposição Não Classificada",
            "Devolvida ao Reclamante"  => "Ação Necessária: Complemento de Informações",
            "Devolvida ao Provedor"    => "Reclamação Reencaminhada para Revisão",
            "Respondida ao Reclamante" => "Resposta Final Disponível",
            "Encaminhado ao Provedor"  => "A sua exposição foi encaminhada ao Provedor",
            default                    => "Atualização de Status: Protocolo #{$code}"
        };
    }

    public static function getStatusDescription(string $status): string
    {
        return match ($status) {
            "Aprovada Classificação"   => "A sua exposição foi validada tecnicamente e seguirá para análise interna profunda.",
            "Negada Classificação"     => "Após análise, informamos que a sua exposição não preenche os requisitos para prosseguimento.",
            "Encaminhado ao Provedor"  => "Enviámos os detalhes ao provedor responsável para obter esclarecimentos adicionais.",
            "Devolvida ao Reclamante"  => "Identificámos a necessidade de informações complementares para avançar com a análise.",
            "Respondida pelo Provedor" => "Recebemos o feedback do provedor e estamos a consolidar a resposta final.",
            "Respondida ao Reclamante" => "O seu processo foi concluído. A resposta oficial já se encontra disponível.",
            default                    => "O seu processo registrou uma nova movimentação e está a ser processado pela nossa equipa."
        };
    }
}
