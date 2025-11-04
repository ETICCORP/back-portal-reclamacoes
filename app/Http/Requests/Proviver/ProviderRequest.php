<?php

namespace App\Http\Requests\Proviver;

use App\Http\Requests\BaseFormRequest;

class ProviderRequest extends BaseFormRequest
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
            'nif' => 'required',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required'
        ];
    }
}