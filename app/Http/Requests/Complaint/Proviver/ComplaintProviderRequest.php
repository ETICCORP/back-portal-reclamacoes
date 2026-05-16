<?php

namespace App\Http\Requests\Complaint\Proviver;

use App\Http\Requests\BaseFormRequest;

class ComplaintProviderRequest extends BaseFormRequest
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
            'complaint_id' => 'required|integer|exists:complaint,id',
            'provider_id' => 'required|integer|exists:provider,id',
            'summary' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            // 📎 Validação dos anexos
            'attachments'            => 'nullable|array',
            'attachments.*'          => 'nullable|string', // cada item deve ser uma string base64
            'deadline' => 'nullable|date'
        ];
    }
}
