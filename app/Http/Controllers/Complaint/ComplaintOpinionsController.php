<?php

namespace App\Http\Controllers\Complaint;

use App\Http\Controllers\AbstractController;
use App\Services\Complaint\ComplaintOpinionsService;
use App\Http\Requests\Complaint\ComplaintOpinionsRequest;
use App\Models\Alert\UserGrupoAlert\UserGrupoAlert;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\ComplaintOpinionAlert;
use App\Models\Log\Log;
use Illuminate\Support\Facades\DB;

class ComplaintOpinionsController extends AbstractController
{
    protected ?string $logType = 'complaint_opinion';

    public function __construct(ComplaintOpinionsService $service)
    {
        $this->service = $service;
    }

    protected function logDefinitions(): array
    {
        return [
            'index'           => 'visualizou todos os pareceres de reclamacão',
            'show'            => 'visualizou os detalhes do parecer de reclamação #:code',
            'store'           => 'registrou um novo parecer de reclamação #:complaint.code',
            'update'          => 'atualizou o parecer de reclamação #:complaint.code',
            'delete'          => 'excluiu o parecer de reclamação #:complaint.code'
        ];
    }


    /**
     * Notifica o grupo de alerta. Lança exceção se o utilizador específico for inválido.
     * * @throws \InvalidArgumentException
     */
    private function notifyAlertGroup($opinion, ?int $specificUserId = null): void
    {
        $query = UserGrupoAlert::where('grup_alert_id', $opinion->department_id)
            ->with('user');

        if ($specificUserId) {
            $query->where('user_id', $specificUserId);
        }

        $recipients = $query->get()->pluck('user.email')->filter();

        // Se foi solicitado um user específico e ele não está no grupo, lançamos erro.
        if ($specificUserId && $recipients->isEmpty()) {
            throw new \InvalidArgumentException(
                "O utilizador selecionado (ID: {$specificUserId}) não pertence ao grupo de alerta do departamento {$opinion->department_id}."
            );
        }

        logs()->info("Notificando grupo de alerta do departamento {$opinion->department_id} para o parecer de reclamação #{$opinion->complaint_id}. Destinatários: " . $recipients->implode(', '));

        if ($recipients->isNotEmpty()) {
            try {
                Mail::to($recipients)->queue(new ComplaintOpinionAlert($opinion->complaint, $opinion));
            } catch (\Throwable $e) {
                // Se o disparo da fila falhar, lançamos uma exception para o store capturar
                throw new \RuntimeException("Falha técnica ao enfileirar e-mail de notificação.");
            }
        }
    }


    public function store(ComplaintOpinionsRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = array_merge($request->validated(), [
                'user_id'      => auth()->id(),
                'submitted_at' => now(),
            ]);

            $opinion = $this->service->store($data);
            $opinion->load(['complaint', 'user']);

            // Se notifyAlertGroup lançar Exception, o catch abaixo captura
            $this->notifyAlertGroup($opinion, $request->get('user_id'));

            $this->logAction(params: $opinion->complaint);

            DB::commit();

            return response()->json(['message' => 'Parecer registado e equipa notificada.'], 201);
        } catch (\InvalidArgumentException $e) {
            // Erro de regra de negócio (Ex: user não pertence ao grupo)
            DB::rollBack();

            logs()->warning("Validação de Notificação: " . $e->getMessage());

            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        } catch (\Throwable $th) {
            // Erros críticos de sistema
            DB::rollBack();

            logs()->error("Erro Crítico ComplaintOpinion@store: " . $th->getMessage(), [
                'exception' => $th,
                'payload'   => $request->all()
            ]);

            return response()->json([
                'message' => 'Não foi possível processar o parecer. ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ComplaintOpinionsRequest $request, $id)
    {
        try {
            $this->logRequest();
            $complaintOpinions = $this->service->update($request->validated(), $id);
            $this->logAction(params: $complaintOpinions);
            return response()->json($complaintOpinions, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $this->logRequest($e);
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $this->logRequest($e);
            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
