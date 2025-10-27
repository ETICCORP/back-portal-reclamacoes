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
            'description'       => 'required|string|max:2000',
            'incidentDateTime'  => 'required|date|before_or_equal:today',
            'location'          => 'required|string|max:255',
            'suggestionAttempt' => 'nullable|string|max:255',
            'relationship'      => 'nullable|string|max:255',
            'status'            => 'required|string|max:255',
            'isAnonymous'       => 'required|boolean',
            'type' => ['required', 'numeric', 'exists:type_complaints,id'],


            'due_date' => 'nullable|string|max:255',
            'responsible_area' => 'nullable|string|max:255',
            'justification' => 'nullable|string|max:255',
            'urgency' => 'nullable|string|max:255',
            'gravity' => 'nullable|string|max:255',
            'entity' => 'required|string|max:255',
            'responsible_analyst' => 'nullable|string|max:255',

            // validação dos envolvidos
            'involveColleagues'            => 'nullable|array',
            'involveColleagues.*.name'     => 'required_with:involveColleagues|string|max:255',
            'involveColleagues.*.role'     => 'required_with:involveColleagues|string|max:255',


            // validação do reporter
            'reporter'               => 'nullable|array',
            'reporter.fullName'      => 'required_with:reporter|string|max:255',
            'reporter.email'         => 'required_with:reporter|email',
            'reporter.department'    => 'nullable|string|max:255',
            'reporter.phone'         => 'nullable|string|max:20',
            'reporter.quality'       => 'required_with:reporter|string|max:255',
            // 📎 Validação dos anexos
            'attachments'            => 'nullable|array',
            'attachments.*'          => 'nullable|string', // cada item deve ser uma string base64
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'A descrição é obrigatória.',
            'incidentDateTime.required' => 'A data/hora do incidente é obrigatória.',
            'incidentDateTime.date' => 'A data/hora deve ser uma data válida.',
            'incidentDateTime.before_or_equal' => 'A data do incidente não pode estar no futuro.',
            'location.required' => 'A localização é obrigatória.',
            'status.required' => 'O status é obrigatório.',
            'type.required' => 'O tipo de ocorrência é obrigatório.',

            'due_date.date' => 'A data de vencimento deve ser uma data válida.',            

            'involveColleagues.name.required_with' => 'O nome do colega envolvido é obrigatório.',
            'involveColleagues.role.required_with' => 'O papel/função do colega envolvido é obrigatório.',

            'reporter.fullName.required_with' => 'O nome do repórter é obrigatório.',
            'reporter.email.required_with' => 'O email do repórter é obrigatório e deve ser válido.',
        ];
    }
}
