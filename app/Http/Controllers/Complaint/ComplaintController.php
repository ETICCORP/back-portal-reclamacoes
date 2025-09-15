<?php

namespace App\Http\Controllers\Complaint;

use App\Http\Controllers\AbstractController;
use App\Services\Complaint\ComplaintService;
use App\Http\Requests\Complaint\ComplaintRequest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ComplaintController extends AbstractController
{
    public function __construct(ComplaintService $service)
    {
        $this->service = $service;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ComplaintRequest $request)
    {
        try {
            $this->logRequest();
            $complaint = $this->service->storeData($request->validated());
            return response()->json($complaint, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function showFile($id)
    {

        try {
            $this->logRequest();
            $complaint = $this->service->showFile($id);
            return response()->json($complaint, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ComplaintRequest $request, $id)
    {
        try {
            $this->logRequest();
            $complaint = $this->service->update($request->validated(), $id);
            return response()->json($complaint, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $this->logRequest($e);
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    public function total()
    {
        try {
            $this->logRequest();
            $complaint = $this->service->total();
            return response()->json($complaint, Response::HTTP_OK);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function totalForCurrentWeek()
    {
        try {
            $this->logRequest();
            $complaint = $this->service->totalForCurrentWeek();
            return response()->json($complaint, Response::HTTP_OK);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function totalForLastWeek()
    {
        try {
            $this->logRequest();
            $complaint = $this->service->totalForLastWeek();
            return response()->json($complaint, Response::HTTP_OK);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTopTypes()
    {
        try {
            $this->logRequest();
            $complaint = $this->service->getTopTypes();
            return response()->json($complaint, Response::HTTP_OK);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function countByDate(Request $request)
    {
        try {
            $this->logRequest();

            $startDate = $request->input('from');
            $endDate   = $request->input('to');

            $complaint = $this->service->countByDate($startDate, $endDate);

            return response()->json($complaint, Response::HTTP_OK);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
