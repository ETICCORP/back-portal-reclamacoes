<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class EnabledRequest extends BaseFormRequest
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
        $id = $this->route('id') ?? null;
        return [
            'is_active' => ['nullable', 'boolean'],
          
        ];
    }

    public function attributes()
    {
        return [
            
            'is_active' => 'É necessário preencher o campo is_active.' ,
        ];
    }
}
