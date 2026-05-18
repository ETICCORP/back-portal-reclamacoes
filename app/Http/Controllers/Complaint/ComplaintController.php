<?php

namespace App\Http\Controllers\Complaint;

use App\Http\Controllers\AbstractController;
use App\Services\Complaint\ComplaintService;
use App\Http\Requests\Complaint\ComplaintRequest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\Complaint\UpdateStatusRequest;
use App\Http\Requests\Complaint\ComplaintUpdateRequest;
use Illuminate\Pagination\AbstractPaginator;

class ComplaintController extends AbstractController
{
    protected string $resourceName = "reclamações";
    protected ?string $logType = 'complaint';

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

            // 1. Isolamos a função de transformação para não duplicar código (DRY)
            $transformer = function ($item) {
                // Captura o status atual (seja de um array ou de um objeto)
                $enumStatus = is_array($item) ? ($item['status'] ?? null) : ($item->status ?? null);
                $nextStatuses = $enumStatus ? $enumStatus->getNextStatuses() : [];

                // Injeta o resultado de volta no item respeitando o seu tipo
                if (is_array($item)) {
                    $item['nextStatus'] = $nextStatuses;
                } else {
                    $item->nextStatus = $nextStatuses;
                }

                return $item;
            };

            // 2. Aplica a lógica dependendo do tipo de retorno do Service
            if ($service instanceof AbstractPaginator) {
                // Modifica a collection interna do paginador diretamente na memória
                $service->getCollection()->transform($transformer);
            } else {
                // Se for array ou collection comum, mapeia e substitui a variável
                $service = collect($service)->map($transformer);
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

    public function update(ComplaintUpdateRequest $request, $id)
    {
        try {
            $this->logRequest();

            $complaint = $this->service->update($request->validated(), $id);

            $this->logAction(params: $complaint);

            return response()->json($complaint);
        } catch (ModelNotFoundException $e) {
            $this->logRequest($e);
            return response()->json(['error' => 'Resource not found.'], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Operação inválida',
                'messages' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), 500);
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
