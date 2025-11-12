<?php

namespace App\Http\Controllers\ComplaintTriages;

use App\Http\Controllers\AbstractController;
use App\Services\ComplaintTriages\ComplaintTriagesService;
use App\Http\Requests\ComplaintTriages\ComplaintTriagesRequest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ComplaintTriagesController extends AbstractController
{
    public function __construct(ComplaintTriagesService $service)
    {
        $this->service = $service;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ComplaintTriagesRequest $request)
    {
        try {
            $this->logRequest();
            $data = $request->validated();
            if ($this->logRequest) {
                $this->logRequest();
                $this->logToDatabase(
                    type: $this->logType,
                    level: 'info',
                    complaint_id: $data['complaint_id'],
                    customMessage: "O usuário " . Auth::user()->first_name . "Foi registada uma nova triagem de reclamação",
                );
            }
            $complaintTriages = $this->service->store($request->validated());
            return response()->json($complaintTriages, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
