<?php

namespace App\Http\Requests\Complaint\ModelEmail;

use App\Http\Requests\BaseFormRequest;

class ModelEmailRequest extends BaseFormRequest
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
     */
    public function rules(): array
    {
        // Define se é uma atualização (PUT ou PATCH)
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        // Se for update, usamos 'sometimes'. Se for post, 'required'.
        $requiredCondition = $isUpdate ? 'sometimes' : 'required';

        return [
            'subject'        => "$requiredCondition|string|max:255",
            'name'           => "$requiredCondition|string|max:255",
            'body'           => "$requiredCondition|string",
            'signature_path' => "$requiredCondition|string",
            // 'user_id'     => "$requiredCondition|exists:users,id",
        ];
    }

    /**
     * Opcional: Customizar mensagens de erro
     */
    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório para criar um modelo.',
            'sometimes' => 'O campo :attribute foi enviado mas contém um formato inválido.',
        ];
    }
}
