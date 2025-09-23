<?php

namespace App\Http\Requests\Alert\UserGrupoAlert;

use App\Http\Requests\BaseFormRequest;
use App\Models\Alert\GrupoAlertEmails\GrupoAlertEmails;
use App\Models\User\User;
use Illuminate\Validation\Rule;
class UserGrupoAlertRequest extends BaseFormRequest
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
            '*.grup_alert_id' =>['required', Rule::exists(GrupoAlertEmails::class, 'id')],
            '*.user_id' => ['required', Rule::exists(User::class, 'id')],
        ];
    }
    public function messages(): array
    {
        return [
            '*.grup_alert_id.required' => 'O campo grup_alert_id é obrigatório.',
            '*.grup_alert_id.exists'   => 'O grupo informado não existe.',
            '*.user_id.required'  => 'O campo user_id é obrigatório.',
            '*.user_id.exists'    => 'O usuário informado não existe.',
        ];
    }
}