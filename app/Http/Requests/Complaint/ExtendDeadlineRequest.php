<?php

namespace App\Http\Requests\Complaint;

use App\Http\Requests\BaseFormRequest;

class ExtendDeadlineRequest extends BaseFormRequest
{
    /**
     * Determina se o utilizador está autorizado a fazer este pedido.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para o prolongamento.
     */
    public function rules(): array
    {
        return [
            'days'   => ['required', 'integer', 'min:1', 'max:5'], // Evita abusos (máximo 5 dias de uma vez)
            'reason' => ['required', 'string', 'min:10', 'max:1000'], // Garante uma justificação auditável
        ];
    }

    /**
     * Mensagens de erro personalizadas em Português.
     */
    public function messages(): array
    {
        return [
            'days.required'   => 'A quantidade de dias adicionais é obrigatória.',
            'days.integer'    => 'Os dias adicionais devem ser um número inteiro.',
            'days.min'        => 'O prazo deve ser prolongado em pelo menos 1 dia útil.',
            'days.max'        => 'Não é permitido prolongar mais de 5 dias úteis numa única operação.',
            'reason.required' => 'A razão do prolongamento é obrigatória para efeitos de auditoria.',
            'reason.min'      => 'A justificação deve ser descritiva e ter pelo menos 10 caracteres.',
        ];
    }
}