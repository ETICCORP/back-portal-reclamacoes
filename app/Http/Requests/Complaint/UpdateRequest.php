<?php

namespace App\Http\Requests\Complaint;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
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
        'due_date' => 'nullable|string|max:255',
        'responsible_area' => 'nullable|string|max:255',
        'justification' => 'nullable|string|max:255',
        'urgency' => 'nullable|string|max:255',
        'gravity'=> 'nullable|string|max:255',
        'responsible_analyst' => 'nullable|string|max:255',

    ];
}


}
