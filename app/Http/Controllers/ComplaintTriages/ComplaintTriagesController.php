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

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Regista a triagem da reclamação enviada pelo Front-end.
     */
    public function store(ComplaintTriagesRequest $request)
    {
        try {
            // 1. Regista o log da requisição à entrada (Apenas uma vez)
            $this->logRequest();

            // 2. Executa o serviço de triagem (onde reside a nossa regra de reentrância única)
            $complaintTriages = $this->service->store($request->validated());

            // 3. Regista a ação de sucesso na auditoria
            $this->logAction(params: $complaintTriages);

            return response()->json($complaintTriages, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Regista o rasto do erro nos logs da VPS para debug posterior
            $this->logRequest($e);

            // 💡 CAPTURA INTELIGENTE: Se for uma validação de negócio (como a nossa trava),
            // devolvemos o código 422 (Unprocessable Entity) com a mensagem real.
            // Caso contrário, mantemos o 500 para erros fatais de SQL/Sistema.
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
        try {
            $this->logRequest();
            $complaintTriages = $this->service->update($request->validated(), $id);
            return response()->json($complaintTriages, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $this->logRequest($e);
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
