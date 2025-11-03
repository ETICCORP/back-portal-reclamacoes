<?php

namespace App\Http\Controllers\Complaint\ComplaintInteraction;

use App\Http\Controllers\AbstractController;
use App\Services\Complaint\ComplaintInteraction\ComplaintInteractionService;
use App\Http\Requests\Complaint\ComplaintInteraction\ComplaintInteractionRequest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ComplaintInteractionController extends AbstractController
{
    public function __construct(ComplaintInteractionService $service)
    {
        $this->service = $service;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ComplaintInteractionRequest $request)
    {
        try {
            $this->logRequest();
            // Pega os dados validados
            $data = $request->validated();
            // Atribui automaticamente o ID do utilizador autenticado
            $data['user_id'] = auth()->id();

            if ($this->logRequest) {
                $this->logRequest();
                $this->logToDatabase(
                    type: $this->logType,
                    level: 'info',
                    complaint_id: $data['complaint_id'],
                    customMessage: "O usuário " . Auth::user()->first_name . "Registrou uma nova interação na reclamação",
                );
            }


            $complaintInteraction = $this->service->store($data);
            return response()->json($complaintInteraction, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ComplaintInteractionRequest $request, $id)
    {
        try {
            $this->logRequest();
            $complaintInteraction = $this->service->update($request->validated(), $id);
            return response()->json($complaintInteraction, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $this->logRequest($e);
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
