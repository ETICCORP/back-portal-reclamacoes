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
            'refusal_reason'      => ['required_if:is_refused,true', 'nullable', 'string', 'max:1000'],
            'return_reason'       => ['required_if:is_returned,true', 'nullable', 'string', 'max:1000'],

            // Campos de triagem técnica
            'classification_type' => [$technicalRule, 'string'],
            'severity'            => [$technicalRule, 'string'],
            'urgency'             => [$technicalRule, 'string'],
            'responsible_area'    => [$technicalRule, 'string'],
        ];
    }

    /**
     * Sobrescrevemos o método validated para garantir que o array retornado 
     * contenha os campos nulos conforme a regra de negócio.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        $isRefused = $this->boolean('is_refused');
        $isReturned = $this->boolean('is_returned');

        // Se for Recusa ou Devolução, força NULL nos campos técnicos
        if ($isRefused || $isReturned) {
            $validated['classification_type'] = null;
            $validated['severity']            = null;
            $validated['urgency']             = null;
            $validated['responsible_area']    = null;
            $validated['assigned_user_id']    = null;
        }

        // Limpeza cruzada de motivos
        if ($isRefused) {
            $validated['return_reason'] = null;
        } elseif ($isReturned) {
            $validated['refusal_reason'] = null;
        } else {
            // Se for triagem normal, limpa ambos os motivos
            $validated['refusal_reason'] = null;
            $validated['return_reason']  = null;
        }

        return $validated;
    }
}
