<?php

namespace App\Http\Controllers\Complaint\Proviver;

use App\Http\Controllers\AbstractController;
use App\Services\Complaint\Proviver\ComplaintProviderService;
use App\Http\Requests\Complaint\Proviver\ComplaintProviderRequest;
use App\Services\Proviver\grupProveder\grupProvederService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ComplaintProviderController extends AbstractController
{
    protected grupProvederService $grupProvederService;
    public function __construct(ComplaintProviderService $service, grupProvederService $grupProvederService)
    {
        $this->service = $service;
        $this->grupProvederService = $grupProvederService;
    }

    /**
     * Store a newly created resource in storage.
     */

    public function index(Request $request)
    {
        try {
            $userId = Auth::user()->id ?? null;
            $providerID =    $this->grupProvederService->getUserProviderIdByUser($userId);
            if (is_callable([$this, 'logRequest'])) {
                $this->logRequest();
                $this->logToDatabase(
                    type: $this->logType,
                    level: 'info',
                    customMessage: "O usuário " . Auth::user()->first_name . " visualizou todos os registros de reclamações"
                );
            }

            // Valor default para filtersV2
            $defaultFiltersV2 = [
                [
                    "field" => "provider_id",
                    "filterType" => "EQUALS",
                    "filterValue" =>  $providerID 
                ]
            ];

            // Define filters com prioridade: filters -> filtersV2 -> defaultFiltersV2
            $filters = $request['filters'] ?? $request['filtersV2'] ?? $defaultFiltersV2;

            $service = $this->service->index(
                $request['paginate'] ?? null,
                $filters,
                $request->input('orderBy', ['id' => 'desc']),
                $request->input('relationships', [])
            );

            return response()->json($service);
        } catch (Exception $e) {
            if (is_callable([$this, 'logRequest'])) {
                $this->logRequest($e);
            }
            return response()->json([
                'message' => $e->getMessage(),
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    public function store(ComplaintProviderRequest $request)
    {
        try {
            $this->logRequest();
            $complaintProvider = $this->service->store($request->validated());
            return response()->json($complaintProvider, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function forwardComplaint(ComplaintProviderRequest $request)
    {
        try {

            $this->logRequest();
            $complaintProvider = $this->service->forwardComplaint($request->validated());
            return response()->json($complaintProvider, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ComplaintProviderRequest $request, $id)
    {
        try {
            $this->logRequest();
            $complaintProvider = $this->service->update($request->validated(), $id);
            return response()->json($complaintProvider, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $this->logRequest($e);
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function forward()
    {

        try {
            $this->logRequest();
            $complaintProviderResponse = $this->service->forward();
            return response()->json($complaintProviderResponse, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $this->logRequest($e);
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function providersManth()
    {

        try {
            $this->logRequest();
            $complaintProviderResponse = $this->service->providersManth();
            return response()->json($complaintProviderResponse, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $this->logRequest($e);
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
