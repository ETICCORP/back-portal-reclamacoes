<?php

namespace App\Http\Controllers\Complaint;

use App\Http\Controllers\AbstractController;
use App\Services\Complaint\TypeComplaintsService;
use App\Http\Requests\Complaint\TypeComplaintsRequest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class TypeComplaintsController extends AbstractController
{
    public function __construct(TypeComplaintsService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            if ($this->logRequest) {
                $this->logRequest();
            }

            $filters = $request['filters'] ?? $request['filtersV2'];

            $service = $this->service->index(
                $request['paginate'],
                $filters,
                $request->input('orderBy', ['id' => 'desc']),
                $request->input('relationships', []),
                $filters
            );

            return response()->json($service);
        } catch (Exception $e) {
            if ($this->logRequest) {
                $this->logRequest($e);
            }
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(TypeComplaintsRequest $request)
    {
        try {
            $this->logRequest();
            $typeComplaints = $this->service->store($request->validated());
            return response()->json($typeComplaints, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(TypeComplaintsRequest $request, $id)
    {
        try {
            $this->logRequest();
            $typeComplaints = $this->service->update($request->validated(), $id);
            return response()->json($typeComplaints, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $this->logRequest($e);
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
