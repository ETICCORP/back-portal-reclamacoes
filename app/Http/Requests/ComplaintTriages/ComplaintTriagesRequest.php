<?php

namespace App\Http\Requests\ComplaintTriages;

use Illuminate\Foundation\Http\FormRequest;

class ComplaintTriagesRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepara os dados para validação (Sanitização).
     * Útil para garantir que strings vazias ou nulas do front-end 
     * não quebrem a regra 'exists'.
     */
    protected function prepareForValidation()
    {
        if ($this->is_refused === true || $this->is_refused === 'true') {
            $this->merge([
                'assigned_user_id' => null,
                'classification_type' => null,
                'severity' => null,
                'urgency' => null,
                'responsible_area' => null,
            ]);
        }
    }

    /**
     * Regras de validação.
     */
    public function rules(): array
    {
        return [
            // Campos Identificadores
            'complaint_id' => ['required', 'exists:complaint,id'],
            'is_refused'   => ['required', 'boolean'],

            // Fluxo de Aceite (is_refused = false)
            // Usamos required_if para obrigar o preenchimento apenas se não for recusado.
            'assigned_user_id'    => ['required_if:is_refused,false', 'nullable', 'exists:users,id'],
            'classification_type' => ['required_if:is_refused,false', 'nullable', 'string'],
            'severity'            => ['required_if:is_refused,false', 'nullable', 'string'],
            'urgency'             => ['required_if:is_refused,false', 'nullable', 'string'],
            'responsible_area'    => ['required_if:is_refused,false', 'nullable', 'string'],

            // Fluxo de Recusa (is_refused = true)
            'refusal_reason'      => ['required_if:is_refused,true', 'nullable', 'string', 'min:10'],
        ];
    }
}
