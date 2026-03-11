<?php

namespace App\Http\Controllers\Complaint;

use App\Http\Controllers\AbstractController;
use App\Services\Complaint\ComplaintResponsesService;
use App\Http\Requests\Complaint\ComplaintResponsesRequest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use App\Http\Requests\Complaint\SendEmailResponseRequest;
use App\Models\Complaint\ComplaintDeadline;
use App\Services\Complaint\ComplaintDeadlineService;
use Carbon\Carbon;

class ComplaintResponsesController extends AbstractController
{
    private  $complaintDeadlineService;

    public function __construct(ComplaintResponsesService $service, ComplaintDeadlineService $complaintDeadlineService)
    {
        $this->service = $service;
        $this->complaintDeadlineService = $complaintDeadlineService;
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(ComplaintResponsesRequest $request)
{
    try {

        $this->logRequest();

        $data = $request->validated();

        $complaintResponses = $this->service->store($data);

        ComplaintDeadline::where('complaint_id', $request->input('complaint_id'))
            ->update([
                'end_date' => null
            ]);

        return response()->json($complaintResponses, Response::HTTP_CREATED);

    } catch (\Throwable $e) {

        $this->logRequest($e);

        return response()->json([
            'message' => 'Erro interno',
            'error' => $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);

    }
}

    public function complaintResponse(ComplaintResponsesRequest $request)
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

    public function sendEmailResponse($id)
    {
        try {

            $this->logRequest();
            $complaintResponses = $this->service->sendEmailResponse($id);
            return response()->json($complaintResponses, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ComplaintResponsesRequest $request, $id)
    {
        try {
            $this->logRequest();
            $complaintResponses = $this->service->update($request->validated(), $id);
            return response()->json($complaintResponses, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $this->logRequest($e);
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
