<?php

namespace App\Http\Controllers\Comment;

use App\Http\Controllers\AbstractController;
use App\Services\Comment\CommentService;
use App\Http\Requests\Comment\CommentRequest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CommentController extends AbstractController
{
    public function __construct(CommentService $service)
    {
        $this->service = $service;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentRequest $request)
    {
        try {
            $this->logRequest();
            $data = $request->validated();
            $data['fk_user'] = Auth::id();
            if ($this->logRequest) {
                $this->logRequest();
                $this->logToDatabase(
                    type: $this->logType,
                    level: 'info',
                    complaint_id: $data['report_id'],
                    customMessage: "O usuário " . Auth::user()->first_name . "Registou um comentário.",
                );
            }

            $comment = $this->service->store($data);
            return response()->json($comment, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommentRequest $request, $id)
    {
        try {
            $this->logRequest();
            $comment = $this->service->update($request->validated(), $id);
            return response()->json($comment, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $this->logRequest($e);
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
