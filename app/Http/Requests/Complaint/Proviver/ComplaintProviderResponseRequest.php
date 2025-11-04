<?php

namespace App\Http\Requests\Complaint\Proviver;

use App\Http\Requests\BaseFormRequest;

class ComplaintProviderResponseRequest extends BaseFormRequest
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
            'complaint_id' => 'required',
            'provider_id' => 'required',
            'status' => 'required',
            'response' => 'required'
        ];
    }
}