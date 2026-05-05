<?php

namespace App\Http\Controllers\Alert\UserGrupoAlert;

use App\Http\Controllers\AbstractController;
use App\Services\Alert\UserGrupoAlert\UserGrupoAlertService;
use App\Http\Requests\Alert\UserGrupoAlert\UserGrupoAlertRequest;
use Exception;
use Illuminate\Http\Response;

class UserGrupoAlertController extends AbstractController
{
    public function __construct(UserGrupoAlertService $service)
    {
        $this->service = $service;
    }

    public function store(UserGrupoAlertRequest $request)
    {
        try {
            $this->logRequest();
            // Chama o método unificado do Service
            $result = $this->service->syncGroupUsers($request->validated());
            return response()->json($result, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}