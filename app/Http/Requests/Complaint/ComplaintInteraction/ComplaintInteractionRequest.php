<?php

namespace App\Http\Requests\Complaint\ComplaintInteraction;

use App\Http\Requests\BaseFormRequest;

class ComplaintInteractionRequest extends BaseFormRequest
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
            'complaint_id' =>  ['required', 'exists:complaint,id'],
            'user_id'      => 'nullable|max:255',
            'type_contact' => 'nullable|string|max:255',
            'contact'      => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ];
    }
}
