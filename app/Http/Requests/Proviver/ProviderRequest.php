<?php

namespace App\Http\Requests\Proviver;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ProviderRequest extends BaseFormRequest
{
    /**
     * Determina se o utilizador tem autorização para esta operação.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação aplicadas ao pedido.
     */
    public function rules(): array
    {
        // Tenta capturar o ID da rota (suporta 'id' ou 'provider')
        $providerId = $this->route('provider') ?? $this->route('id');

        // Verifica se é uma criação ou atualização
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'name' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:255'
            ],
            'nif' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                Rule::unique('providers', 'nif')->ignore($providerId)
            ],
            'phone' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:20'
            ],
            'email' => [
                $isUpdate ? 'sometimes' : 'required',
                'email',
                'max:255',
                // Garante unicidade na tabela users, ignorando o próprio registro no update
                Rule::unique('users', 'email')->ignore($providerId)
            ]
        ];
    }

    /**
     * Customização das mensagens (opcional, mas pragmático para UX)
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Este endereço de e-mail já está registado no sistema.',
            'nif.unique'   => 'Este NIF já se encontra associado a outro fornecedor.',
        ];
    }
}
