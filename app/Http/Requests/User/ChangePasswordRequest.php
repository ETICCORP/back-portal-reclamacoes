<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class ChangePasswordRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        // Autoriza todos usuários (ajuste se quiser lógica diferente)
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required',
            'new_password' => [
                'required',
                'regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+])[A-Za-z\d!@#$%^&*()_+]{8,}$/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'A senha antiga é obrigatória.',
            'new_password.required' => 'A nova senha é obrigatória.',
            'new_password.regex' => 'A senha deve ter pelo menos 8 caracteres, incluir um número, uma letra maiúscula, uma minúscula e um caractere especial.',
        ];
    }
}
