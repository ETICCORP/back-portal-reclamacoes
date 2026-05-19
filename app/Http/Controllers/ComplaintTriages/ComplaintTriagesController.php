<?php

namespace App\Http\Controllers\ComplaintTriages;

use App\Http\Controllers\AbstractController;
use App\Services\ComplaintTriages\ComplaintTriagesService;
use App\Http\Requests\ComplaintTriages\ComplaintTriagesRequest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class ComplaintTriagesController extends AbstractController
{
    public function __construct(ComplaintTriagesService $service)
    {
        $this->service = $service;
    }

    protected function logDefinitions(): array
    {
        return [
            'index'           => 'visualizou todas as triagens de reclamação',
            'show'            => 'visualizou os detalhes da triagem de reclamação #:complaint.code',
            'store'           => 'classificou a reclamação #:complaint.code'
        ];
    }

    /*
     * Regista a triagem da reclamação enviada pelo Front-end.
     */
    public function store(ComplaintTriagesRequest $request)
    {
        try {
            // 1. Regista o log da requisição à entrada (Apenas uma vez)
            $this->logRequest();

            // 2. Obter os dados validados do formulário
            $validatedData = $request->validated();
            $complaintId = data_get($validatedData, 'complaint_id');

            // 3. VALIDAR PRAZO: Verifica se a reclamação ainda tem a deadline ativa
            if ($complaintId) {
                // Encontra a reclamação para avaliar o Accessor dinâmico
                $complaint = \App\Models\Complaint\Complaint::findOrFail($complaintId);

                if (!$complaint->is_deadline_active) {
                    return response()->json([
                        'success' => false,
                        'message' => "Operação recusada. O prazo de resolução (deadline) desta reclamação expirou."
                    ], Response::HTTP_UNPROCESSABLE_ENTITY); // HTTP 422
                }
            }

            // 4. Executa o serviço de triagem (regra de reentrância única)
            $complaintTriages = $this->service->store($validatedData);

            // 5. Regista a ação de sucesso na auditoria
            $this->logAction(params: $complaintTriages);

            return response()->json($complaintTriages, Response::HTTP_CREATED);

        } catch (Exception $e) {
            // CAPTURA INTELIGENTE: Se for uma validação de negócio,
            $isBusinessRule = str_contains($e->getMessage(), 'Ação recusada') || str_contains($e->getMessage(), 'já passou');
            $statusCode = $isBusinessRule ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_INTERNAL_SERVER_ERROR;

            $errorMessage = $isBusinessRule
                ? $e->getMessage()
                : 'Ocorreu um erro interno ao processar a triagem da reclamação.';

            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], $statusCode);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ComplaintTriagesRequest $request, $id)
    {
        return response(null, Response::HTTP_NOT_IMPLEMENTED);
    }
}
