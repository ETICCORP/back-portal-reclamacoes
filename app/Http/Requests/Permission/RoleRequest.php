<?php

namespace App\Http\Requests\Permission;

use App\Http\Requests\BaseFormRequest;

class RoleRequest extends BaseFormRequest
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
        $id = $this->route('role') ? $this->route('role') : null;
        return [
            'name' => ['required', "unique:role,name,{$id},id"],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required', 'exists:permission,id'],
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nome do Papel',
            'description' => 'Descrição do Papel',
            'is_active' => 'Status',
            'permissions' => 'Permissões'
        ];
    }
}
