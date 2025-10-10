<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class changePasswordUser extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'new_password' => 'required|string|min:8|confirmed',
        ];
    }
}
