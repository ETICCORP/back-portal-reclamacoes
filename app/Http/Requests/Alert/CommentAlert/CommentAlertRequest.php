<?php

namespace App\Http\Requests\Alert\CommentAlert;

use App\Http\Requests\BaseFormRequest;

class CommentAlertRequest extends BaseFormRequest
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
            'alert_id' => ['required'],
            'comment'  => ['required'],
        ];;
    }
}