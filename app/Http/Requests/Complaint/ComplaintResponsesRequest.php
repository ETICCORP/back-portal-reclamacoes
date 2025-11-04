<?php

namespace App\Http\Requests\Complaint;

use App\Http\Requests\BaseFormRequest;

class ComplaintResponsesRequest extends BaseFormRequest
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
        
            'complaint_id' => 'required|exists:complaint,id',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string|min:5',
            'signature_path' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'O campo utilizador é obrigatório.',
            'user_id.exists' => 'O utilizador selecionado não é válido.',
            'complaint_id.required' => 'É necessário associar a resposta a uma reclamação.',
            'complaint_id.exists' => 'A reclamação indicada não existe.',
            'subject.string' => 'O assunto deve ser um texto.',
            'body.required' => 'O corpo da mensagem é obrigatório.',
            'body.min' => 'O corpo da mensagem deve conter pelo menos :min caracteres.',
            'signature_path.file' => 'A assinatura deve ser um ficheiro válido.',
            'signature_path.mimes' => 'A assinatura deve estar em formato PNG, JPG, JPEG ou PDF.',
            'signature_path.max' => 'A assinatura não pode exceder 2MB.',
        ];
    }
    
    
}