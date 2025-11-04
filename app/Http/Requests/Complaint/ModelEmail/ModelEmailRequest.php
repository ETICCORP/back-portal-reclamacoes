<?php

namespace App\Http\Requests\Complaint\ModelEmail;

use App\Http\Requests\BaseFormRequest;

class ModelEmailRequest extends BaseFormRequest
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
            'name' => 'required',
            'subject' => 'required',
            'body' => 'required',
            'signature_path' => 'required'
        ];
    }
}