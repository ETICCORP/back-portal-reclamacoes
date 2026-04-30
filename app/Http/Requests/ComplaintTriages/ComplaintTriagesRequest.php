<?php

namespace App\Http\Requests\ComplaintTriages;

use App\Http\Requests\BaseFormRequest;

class ComplaintTriagesRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'complaint_id'     => ['required', 'exists:complaint,id'],
            'assigned_user_id' => ['required', 'exists:users,id'],
            'is_refused'       => ['required', 'boolean'],

            // Obrigatório apenas se NÃO for recusado
            'classification_type' => ['required_if:is_refused,false', 'string'],
            'severity'            => ['required_if:is_refused,false', 'string'],
            'urgency'             => ['required_if:is_refused,false', 'string'],
            'responsible_area'    => ['required_if:is_refused,false', 'string'],

            // Obrigatório apenas se FOR recusado
            'refusal_reason'      => ['required_if:is_refused,true', 'string', 'min:10'],
        ];
    }
}
