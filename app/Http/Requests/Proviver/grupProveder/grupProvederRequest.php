<?php

namespace App\Http\Requests\Proviver\grupProveder;

use App\Http\Requests\BaseFormRequest;
use App\Models\Proviver\Provider;
use App\Models\User\User;
use Illuminate\Validation\Rule;

class grupProvederRequest extends BaseFormRequest
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
         

               '*.user_id' =>['required', Rule::exists(User::class, 'id')],
            '*.proveder_id' => ['required', Rule::exists(Provider::class, 'id')],
        ];
    }
}