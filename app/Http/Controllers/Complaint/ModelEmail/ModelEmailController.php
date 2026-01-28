<?php

namespace App\Http\Controllers\Complaint\ModelEmail;

use App\Http\Controllers\AbstractController;
use App\Services\Complaint\ModelEmail\ModelEmailService;
use App\Http\Requests\Complaint\ModelEmail\ModelEmailRequest;
use Exception;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class ModelEmailController extends AbstractController
{
    public function __construct(ModelEmailService $service)
    {
        $this->service = $service;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ModelEmailRequest $request)
    {
        try {
            $this->logRequest();

            $data = $request->validated();

            // 🔑 INJETAR O FICHEIRO MANUALMENTE
            $data['signature_path'] = $request->file('signature_path');
            $data['user_id'] = Auth::user()->id;

            $modelEmail = $this->service->store($data);

            return response()->json($modelEmail, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function complaintResponse(ModelEmailRequest $request)
    {
        try {

            $this->logRequest();
            $data = $request->validated();
            // Atribui automaticamente o ID do utilizador autenticado
            $data['user_id'] = auth()->id();
            $complaintResponses = $this->service->complaintResponse($data);
            return response()->json($complaintResponses, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ModelEmailRequest $request, $id)
    {
        try {

        
            $this->logRequest();
            $modelEmail = $this->service->update($request->validated(), $id);
            return response()->json($modelEmail, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $this->logRequest($e);
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function files($alertID)
    {
        try {
            $this->logRequest();
            $alertAttachment = $this->service->files($alertID);
            return response()->json($alertAttachment, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showFile($id)
    {

        $filePath = $this->service->showFile($id); // Retorna caminho absoluto

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Arquivo não encontrado.'], 404);
        }

        $mimeType = \Illuminate\Support\Facades\File::mimeType($filePath);
        $fileName = basename($filePath);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
        try {
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Falha ao abrir o arquivo.",
                "error" => $th->getMessage()
            ], 400);
        }
    }
}
