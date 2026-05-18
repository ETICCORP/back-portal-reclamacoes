<?php

namespace App\Http\Controllers\Complaint;

use App\Http\Controllers\AbstractController;
use App\Http\Requests\Complaint\ComplaintDeadlineRequest;
use App\Http\Requests\Complaint\ExtendDeadlineRequest;
use App\Services\Complaint\ComplaintDeadlineService;
use Exception;
use Illuminate\Http\Response;

class ComplaintDeadlineController extends AbstractController
{
    protected ?string $logType = 'complaint_deadline';

    protected function logDefinitions(): array
    {
        return [
            'index'           => 'visualizou os prazos das reclamações',
            'show'            => 'visualizou os detalhes do prazo da reclamação #:complaint.code',
            'extendDeadline'  => 'prolongou o prazo da reclamação #:complaint.code'
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
        return response(null, Response::HTTP_NOT_IMPLEMENTED);
    }

    public function extendDeadline(int $id, ExtendDeadlineRequest $request)
    {
        try {
            // Passamos os dados primitivos estritos do request para o Service
            $deadline = $this->service->extendDeadline(
                $id,
                (int) $request->validated('days'),
                $request->validated('reason')
            );

            $this->logAction(params: $deadline);

            return response()->json([
                'message' => 'O prazo de resposta da reclamação foi prolongado com sucesso.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(ComplaintDeadlineRequest $request, $id)
    {
        return response(null, Response::HTTP_NOT_IMPLEMENTED);
    }

    public function percentageServicedWithinDeadline()
    {
        $this->logRequest();
        $complaint = $this->service->percentageServicedWithinDeadline();
        return response()->json($complaint, Response::HTTP_OK);
    }
}
