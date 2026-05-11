<?php

namespace App\Http\Requests\ComplaintTriages;

use App\Http\Requests\BaseFormRequest;

class ComplaintTriagesRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Se is_refused ou is_returned forem true, os campos técnicos NÃO são obrigatórios
        $isTechnicalFieldsRequired = !($this->boolean('is_refused') || $this->boolean('is_returned'));
        $technicalRule = $isTechnicalFieldsRequired ? 'required' : 'nullable';

        return [
            'complaint_id'        => ['required', 'exists:complaint,id'],
            'is_refused'          => ['required', 'boolean'],
            'is_returned'         => ['required', 'boolean'],

            // Motivos obrigatórios condicionalmente
            'refusal_reason'      => ['required_if:is_refused,true', 'nullable', 'string'],
            'return_reason'       => ['required_if:is_returned,true', 'nullable', 'string'],

            // Campos de triagem técnica
            'classification_type' => [$technicalRule, 'string'],
            'severity'            => [$technicalRule, 'string'],
            'urgency'             => [$technicalRule, 'string'],
            'responsible_area'    => [$technicalRule, 'string'],
            'assigned_user_id'    => [$technicalRule, 'exists:users,id'],
        ];
    }

    /**
     * Prepara os dados para o controller após a validação.
     */
    protected function passedValidation()
    {
        $isRefused = $this->boolean('is_refused');
        $isReturned = $this->boolean('is_returned');

        // Se for QUALQUER saída de fluxo excepcional (Recusa ou Devolução)
        if ($isRefused || $isReturned) {
            $this->merge([
                'classification_type' => null,
                'severity'            => null,
                'urgency'             => null,
                'responsible_area'    => null,
                'assigned_user_id'    => null,
            ]);
        }

        // Limpeza mútua de motivos para evitar lixo no banco
        if ($isRefused) {
            $this->merge(['return_reason' => null]);
        }

        if ($isReturned) {
            $this->merge(['refusal_reason' => null]);
        }

        if (!$isRefused && !$isReturned) {
            $this->merge([
                'refusal_reason' => null,
                'return_reason'  => null,
            ]);
        }
    }
}
