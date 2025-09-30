<?php

namespace App\Http\Requests\Complaint;

use App\Http\Requests\BaseFormRequest;

class UpdateStatusRequest extends BaseFormRequest
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
        'comment'      => 'required|string|max:255',
        'status'       => 'required|string|max:255',
        'attachments'  => 'nullable|array',
        'attachments.*'=> 'nullable|string',
    ];
}

public function messages(): array
{
    return [
        'comment.required' => 'O comentário é obrigatório.',
        'status.required'  => 'O status é obrigatório.',
    ];
}

}
