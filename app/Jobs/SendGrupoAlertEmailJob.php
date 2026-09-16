<?php

namespace App\Jobs;

use App\Mail\GrupoAlertMail;
use App\Models\Alert\Alert;
use App\Models\Alert\AlertUser\AlertUser;
use App\Models\Alert\GrupoType\GrupoType;
use App\Models\Alert\UserGrupoAlert\UserGrupoAlert;
use App\Models\Complaint\Complaint;
use App\Models\Complaint\TypeComplaints;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendGrupoAlertEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $alertID;
    public function __construct(int $alertID)
    {
        $this->alertID = $alertID;
    }
    public $tries = 5;
    public $timeout = 36000;
    public function handle(): void
    {
        $alertId = $this->alertID;

        $alert = Alert::find($alertId);

        if (!$alert) {
            Log::error("Alert com ID {$alertId} não encontrado.");
            return;
        }

        $complaint = Complaint::find($alert->complit_id);
        if (!$complaint) {
            Log::error("Complaint com ID {$alert->complit_id} não encontrado.");
            return;
        }

        Log::info("Processando Alert ID {$alert->id} | Tipo: {$complaint->type}");

        // Buscar todos os grupos que correspondem ao tipo da reclamação.

        $typeComplaint = is_numeric($complaint->type)
            ? TypeComplaints::find((int) $complaint->type)
            : TypeComplaints::where('name', $complaint->type)->first();

        if (!$typeComplaint) {
            Log::warning("Tipo de reclamação '{$complaint->type}' não encontrado.");
            return;
        }

        $grupoTypes = GrupoType::where('type_complaints_id', $typeComplaint->id)->get();

        if ($grupoTypes->isEmpty()) {
            Log::warning("Nenhum grupo de alertas configurado para o tipo de reclamação '{$typeComplaint->name}' (ID {$typeComplaint->id}).");
            return;
        }

        foreach ($grupoTypes as $grupo) {
            Log::info("Processando GrupoType ID {$grupo->id} | Name: {$grupo->name} | grup_alert_id: {$grupo->grup_alert_id}");

            // Buscar usuários vinculados ao grupo
            $userGrupoAlerts = UserGrupoAlert::where('grup_alert_id', $grupo->grup_alert_id)
                ->with('user')
                ->get();

            if ($userGrupoAlerts->isEmpty()) {
                Log::warning("Nenhum usuário encontrado para GrupoType ID {$grupo->id}");
                continue;
            }

            foreach ($userGrupoAlerts as $uga) {
                if (!$uga->user) {
                    Log::warning("UserGrupoAlert ID {$uga->id} não possui usuário relacionado (user_id: {$uga->user_id})");
                    continue;
                }

                $user = $uga->user;

                try {
                    $alreadyLinked = AlertUser::where('alert_id', $alert->id)
                        ->where('user_id', $user->id)
                        ->exists();
                    if ($alreadyLinked) {
                        Log::info("Usuário ID {$user->id} já vinculado ao alert ID {$alert->id}");
                        continue;
                    }

                    AlertUser::create([
                        'alert_id' => $alert->id,
                        'user_id'  => $user->id,
                    ]);
                    // Envia email
                    Mail::to($user->email)->send(new GrupoAlertMail($user, $complaint));

                    Log::info("Usuário ID {$user->id} vinculado ao alert ID {$alert->id}");
                } catch (\Throwable $e) {
                    Log::error("Erro ao criar AlertUser para user ID {$user->id}: {$e->getMessage()}");
                }
            }
        }
    }
}
