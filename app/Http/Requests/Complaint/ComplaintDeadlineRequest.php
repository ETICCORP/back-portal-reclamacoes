<?php

namespace App\Http\Requests\Complaint;

use App\Http\Requests\BaseFormRequest;

class ComplaintDeadlineRequest extends BaseFormRequest
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
          'complaint_id' => ['required', 'exists:complaint,id'],
            'days' => ['required', 'integer', 'min:1', 'max:90'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'in:em_andamento,expirado,cumprido'],
            'notified_at' => ['nullable', 'date'],
        ];
    }

    /**
     * Mensagens personalizadas de erro.
     */
    public function messages(): array
    {
        return [
            'complaint_id.required' => 'O campo reclamação é obrigatório.',
            'complaint_id.exists' => 'A reclamação selecionada não existe.',
            'days.required' => 'O número de dias é obrigatório.',
            'days.integer' => 'O número de dias deve ser um valor numérico.',
            'days.min' => 'O prazo mínimo é de 1 dia.',
            'days.max' => 'O prazo máximo permitido é de 90 dias.',
            'start_date.required' => 'A data de início é obrigatória.',
            'end_date.required' => 'A data de fim é obrigatória.',
            'end_date.after_or_equal' => 'A data de fim deve ser igual ou posterior à data de início.',
            'status.in' => 'O estado deve ser um dos seguintes: em_andamento, expirado ou cumprido.',
            'notified_at.date' => 'A data de notificação deve ser uma data válida.',
        ];
    }
}