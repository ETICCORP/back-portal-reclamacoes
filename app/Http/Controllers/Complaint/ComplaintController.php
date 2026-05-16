<?php

namespace App\Http\Controllers\Complaint;

use App\Http\Controllers\AbstractController;
use App\Http\Requests\Complaint\UpdateRequest;
use App\Services\Complaint\ComplaintService;
use App\Http\Requests\Complaint\ComplaintRequest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Actions\StatusAction;
use App\Http\Requests\Complaint\UpdateStatusRequest;

class ComplaintController extends AbstractController
{
    protected string $resourceName = "reclamações";

    public function __construct(ComplaintService $service)
    {
        $this->service = $service;
    }

    protected function logDefinitions(): array
    {
        return [
            'index'           => 'visualizou todas as reclamações',
            'show'            => 'visualizou os detalhes da reclamação #:code',
            'update'          => 'atualizou os dados da reclamação #:code',
            'delete'          => 'excluiu a reclamação #:code',
            'updateStatus'    => 'atualizou o status da reclamação #:code',
        ];
    }

    public function index(Request $request)
    {
        try {
            if ($this->logRequest) {
                $this->logRequest();
            }

            $this->logAction();

            $filters = $request['filters'] ?? $request['filtersV2'];

            $service = $this->service->index(
                $request['paginate'],
                $filters,
                $request->input('orderBy', ['id' => 'desc']),
                $request->input('relationships', []),
            );

            // 🔹 Se for um paginator (quando existe "paginate")
            if ($service instanceof \Illuminate\Pagination\AbstractPaginator) {
                $service->getCollection()->transform(function ($item) {
                    if (is_array($item)) {
                        $item['nextStatus'] = StatusAction::getNextStatuses($item['status'] ?? null);
                    } else {
                        $item->nextStatus = StatusAction::getNextStatuses($item->status ?? null);
                    }
                    return $item;
                });
            } else {
                // 🔹 Se for apenas collection/array
                $service = collect($service)->map(function ($item) {
                    if (is_array($item)) {
                        $item['nextStatus'] = StatusAction::getNextStatuses($item['status'] ?? null);
                    } else {
                        $item->nextStatus = StatusAction::getNextStatuses($item->status ?? null);
                    }
                    return $item;
                });
            }

            return response()->json($service);
        } catch (Exception $e) {
            if ($this->logRequest) {
                $this->logRequest($e);
            }
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(ComplaintRequest $request)
    {
        try {
            // Concatena os anexos ao array de dados
            $attachments = $request->file('attachments');
            if ($attachments && !is_array($attachments)) {
                $attachments = [$attachments]; // transforma 1 arquivo em array
            }
            $data['attachments'] = $attachments;
            $data = $request->validated();
            // Envia tudo para o service
            $complaint = $this->service->storeData($data);

            //SendReportCopy::dispatch($complaint->id);
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
    public function update(UpdateRequest $request, $id)
    {
        try {
            $this->logRequest();
            $complaint = $this->service->update($request->validated(), $id);

            $this->logAction(params: $complaint);

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
    //========================================
    public function timeResponse()
    {
        try {
            $this->logRequest();
            $complaint = $this->service->timeResponse();
            return response()->json($complaint, Response::HTTP_OK);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //========================================

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
            $this->logAction();
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

    public function GetBycode($code)
    {
        try {
            $this->logRequest();
            $complaint = $this->service->getBycode($code);
            return response()->json($complaint, Response::HTTP_OK);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateStatus(UpdateStatusRequest $request, $id)
    {
        return response()->json('operation not allowed', Response::HTTP_FORBIDDEN);
    }

    public function byManth()
    {
        $this->logRequest();
        $complaint = $this->service->byManth();
        return response()->json($complaint, Response::HTTP_OK);
    }


    public function repeatOffenders()
    {

        $this->logRequest();
        $complaint = $this->service->repeatOffenders();
        return response()->json($complaint, Response::HTTP_OK);
    }
}
