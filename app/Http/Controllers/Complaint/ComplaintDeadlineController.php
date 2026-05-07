<?php

namespace App\Http\Controllers\Complaint;

use App\Http\Controllers\AbstractController;
use App\Services\Complaint\ComplaintDeadlineService;
use App\Http\Requests\Complaint\ComplaintDeadlineRequest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ComplaintDeadlineController extends AbstractController
{
    protected function logDefinitions(): array
    {
        return [
            'index'           => 'visualizou os prazos das reclamações',
            'show'            => 'visualizou os detalhes do prazo da reclamação #:complaint.code',
            'store'           => 'registou um novo prazo para a reclamação #:complaint.code',
            'update'          => 'atualizou o prazo da reclamação #:complaint.code'
        ];
    }

    public function __construct(ComplaintDeadlineService $service)
    {
        $this->service = $service;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ComplaintDeadlineRequest $request)
    {
        try {
            $this->logRequest();
            $this->logAction();

            $complaintDeadline = $this->service->store($request->validated());

            return response()->json($complaintDeadline, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ComplaintDeadlineRequest $request, $id)
    {
        try {
            $this->logRequest();
            $complaintDeadline = $this->service->update($request->validated(), $id);
            $this->logAction(params: $complaintDeadline);
            return response()->json($complaintDeadline, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $this->logRequest($e);
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function percentageServicedWithinDeadline()
    {
        $this->logRequest();
        $complaint = $this->service->percentageServicedWithinDeadline();
        return response()->json($complaint, Response::HTTP_OK);
    }
}
