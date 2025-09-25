<?php

namespace App\Http\Requests\Alert\GrupoType;

use App\Http\Requests\BaseFormRequest;
use App\Models\Alert\GrupoAlertEmails\GrupoAlertEmails;
use App\Models\Alert\GrupoType\GrupoType;
use App\Models\Complaint\Complaint;

class GrupoTypeRequest extends BaseFormRequest
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
            'type_complaints_id' => ['required', 'exists:type_complaints,id'],
            'grup_alert_id'    => ['required', 'exists:user_grupo_alert,id'],
        ];
    }
}
