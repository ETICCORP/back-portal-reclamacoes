<?php

namespace App\Http\Requests\Complaint;

use App\Http\Requests\BaseFormRequest;

class ComplaintUpdateRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Como a rota é pública mas usa 'signed' middleware, o 'true' aqui está correto.
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
            'full_name'        => ['required', 'string', 'max:255'],
            'complainant_role' => ['required', 'string', 'max:100'],
            'source'           => ['nullable', 'string', 'max:50'],
            'location'         => ['nullable', 'string', 'max:255'],
            'type'             => ['nullable', 'string'],
            'policy_number'    => ['nullable', 'string', 'max:100'],
            'entity'           => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'incidentDateTime' => ['nullable', 'date'], // Ajustado para validar formato de data/hora
            'representative'   => ['nullable', 'string'],

            // 📎 Validação dos anexos que o reclamante pode carregar
            'attachments'      => ['nullable', 'array'],
            'attachments.*'    => ['file', 'max:10240'], // Opcional: limita cada arquivo a 10MB
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'full_name.required'        => 'O campo Nome completo é obrigatório.',
            'complainant_role.required' => 'O campo Qualidade do reclamante é obrigatório.',
            'policy_number.string'      => 'O número da apólice deve ser um texto válido.',
            'entity.required'           => 'O campo Entidade reclamada é obrigatório.',
            'incidentDateTime.date'     => 'A data e hora do incidente devem ser válidas.',
            'status.required'           => 'O novo estado do processo deve ser informado.',
            'attachments.array'         => 'Os anexos devem ser enviados em formato de lista.',
            'attachments.*.max'         => 'Cada anexo não pode exceder o tamanho de 10MB.',
        ];
    }
}