<?php

namespace App\Http\Requests\Alert\AlertUser;

use App\Http\Requests\BaseFormRequest;
use App\Models\Alert\Alert;
use App\Models\User\User;
use Illuminate\Validation\Rule;
class AlertUserRequest extends BaseFormRequest
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
            '*.alert_id' => ['required', Rule::exists('alert', 'id')],
            '*.user_id'  => ['required', Rule::exists('users', 'id')],
        ];
    }
    
    public function messages(): array
    {
        return [
            '*.alert_id.required' => 'O campo alert_id é obrigatório.',
            '*.alert_id.exists'   => 'O alerta informado não existe.',
            '*.user_id.required'  => 'O campo user_id é obrigatório.',
            '*.user_id.exists'    => 'O usuário informado não existe.',
        ];
    }
    
}