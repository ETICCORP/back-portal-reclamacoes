<?php

namespace App\Enum;

enum ClaimStatus: string
{
    // 1. Centralização dos Statuses (Single Source of Truth)
    case PENDING = 'Pending';
    case PENDENTE_PT = 'Pendente'; // Caso precise manter o mapeamento legado em PT
    case APROVADA_CLASSIFICACAO = 'Aprovada Classificação';
    case NEGADA_CLASSIFICACAO = 'Negada Classificação';
    case DEVOLVIDA_RECLAMANTE = 'Devolvida ao Reclamante';
    case DEVOLVIDA_PROVEDOR = 'Devolvida ao Provedor';
    case ENCAMINHADO_PROVEDOR = 'Encaminhado ao Provedor';
    case RESPONDIDA_PROVEDOR = 'Respondida pelo Provedor';
    case RESPONDIDA_RECLAMANTE = 'Respondida ao Reclamante';

    /**
     * Define as transições e ações permitidas para cada status
     */
    public function getNextStatuses(): array
    {
        return match ($this) {
            self::PENDING, self::PENDENTE_PT => ["Detalhes", "Triagem ou Classificação", "Devolver ao Reclamante"],
            self::APROVADA_CLASSIFICACAO     => ["Detalhes", "Solicitar Opinião", "Responder ao Reclamante", "Devolver ao Reclamante", "Encaminhar ao Provedor"],
            self::NEGADA_CLASSIFICACAO       => ["Detalhes"],
            self::DEVOLVIDA_RECLAMANTE       => ["Detalhes", "Triagem ou Classificação"],
            self::ENCAMINHADO_PROVEDOR       => ["Detalhes", "Solicitar Opinião"],
            self::RESPONDIDA_PROVEDOR        => ["Detalhes", "Solicitar Opinião", "Responder ao Reclamante", "Encaminhar ao Provedor"],
            self::RESPONDIDA_RECLAMANTE      => ["Detalhes"],
            default                          => [],
        };
    }

    /**
     * Retorna o assunto do email
     */
    public function getSubject(string $code): string
    {
        return match ($this) {
            self::APROVADA_CLASSIFICACAO => "Exposição Aprovada para Análise",
            self::NEGADA_CLASSIFICACAO   => "Atualização: Exposição Não Classificada",
            self::DEVOLVIDA_RECLAMANTE   => "Ação Necessária: Complemento de Informações",
            self::DEVOLVIDA_PROVEDOR     => "Reclamação Reencaminhada para Revisão",
            self::RESPONDIDA_RECLAMANTE  => "Resposta Final Disponível",
            self::ENCAMINHADO_PROVEDOR   => "A sua exposição foi encaminhada ao Provedor",
            default                      => "Atualização de Status: Protocolo #{$code}"
        };
    }

    /**
     * Retorna a descrição amigável do status
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::APROVADA_CLASSIFICACAO => "A sua exposição foi validada tecnicamente e seguirá para análise interna profunda.",
            self::NEGADA_CLASSIFICACAO   => "Após análise, informamos que a sua exposição não preenche os requisitos para prosseguimento.",
            self::ENCAMINHADO_PROVEDOR   => "Enviamos os detalhes ao provedor responsável para obter esclarecimentos adicionais.",
            self::DEVOLVIDA_RECLAMANTE   => "Identificamos a necessidade de informações complementares para avançar com a análise.",
            self::RESPONDIDA_PROVEDOR    => "Recebemos o feedback do provedor e estamos a consolidar a resposta final.",
            self::RESPONDIDA_RECLAMANTE  => "O seu processo foi concluído. A resposta oficial já se encontra disponível.",
            default                      => "O seu processo registou uma nova movimentação e está a ser processado pela nossa equipa."
        };
    }
}