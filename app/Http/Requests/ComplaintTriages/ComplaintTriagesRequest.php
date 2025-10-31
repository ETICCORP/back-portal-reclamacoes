<?php

namespace App\Http\Requests\ComplaintTriages;

use App\Http\Requests\BaseFormRequest;

class ComplaintTriagesRequest extends BaseFormRequest
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
            'complaint_id' => ['required', 'exists:complaint,id'],
            'classification_type' => 'required',
            'severity' => 'required',
            'urgency' => 'required',
            'responsible_area' => 'required',
            'is_refused' => 'required',
            'refusal_reason' => 'required',
            'assigned_user_id' => ['required', 'exists:users,id'],
        ];
    }
}