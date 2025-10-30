<?php

namespace App\Http\Requests\Complaint;

use App\Http\Requests\BaseFormRequest;

class ComplaintRequest extends BaseFormRequest
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

            'full_name'         => ['required', 'string', 'max:255'],
            'complainant_role'  => ['required', 'string', 'max:100'],
            'contact'           => ['nullable',  'max:50'],
            'incidentDateTime'           => ['nullable',  'max:50'],
            'location'           => ['nullable',  'max:50'],
            'status'           => ['nullable',  'max:50'],
            'type'           => ['nullable'],
            'email'             => ['nullable', 'email', 'max:255'],
            'policy_number'     => ['nullable',  'max:100'],
            'entity'            => ['required', 'string', 'max:255'],
            'description'       => ['required', 'string', 'min:10'],

            // 📎 Validação dos anexos
            'attachments'            => 'nullable|array',
            'attachments.*'          => 'nullable|string', // cada item deve ser uma string base64
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'        => 'O campo Nome completo é obrigatório.',
            'complainant_role.required' => 'O campo Qualidade do reclamante é obrigatório.',
            'contact.string'            => 'O contacto deve ser um texto válido.',
            'email.email'               => 'Insira um endereço de e-mail válido.',
            'policy_number.string'      => 'O número da apólice deve ser um texto válido.',
            'entity.required'           => 'O campo Entidade reclamada é obrigatório.',
            'description.required'      => 'A descrição da reclamação é obrigatória.',
            'description.min'           => 'A descrição deve ter pelo menos 10 caracteres.',
        ];
    }
}
