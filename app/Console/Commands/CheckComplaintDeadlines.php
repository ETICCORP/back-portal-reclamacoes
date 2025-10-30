<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Complaint\ComplaintDeadline;
use App\Models\Alert\Alert as AlertAlert;
use App\Models\Alert\AlertUser\AlertUser as AlertUserAlertUser;
use App\Mail\ComplaintDeadlineAlert;
use Illuminate\Support\Facades\Mail;

class CheckComplaintDeadlines extends Command
{
    protected $signature = 'complaints:check-deadlines';
    protected $description = 'Verifica prazos legais e envia alertas automáticos';

    public function handle()
    {
        $deadlines = ComplaintDeadline::where('status', 'em_andamento')->get();

        $this->info("Total de deadlines em andamento: " . $deadlines->count());

        foreach ($deadlines as $deadline) {
            $remaining = $deadline->remainingDays();
            $this->info("Deadline ID {$deadline->id} - Complaint ID {$deadline->complaint_id} - Remaining Days: {$remaining} - Notified At: " . ($deadline->notified_at ?? 'null'));

            // Envio de alertas a 3 dias ou menos
            if ($remaining <= 3 && !$deadline->notified_at) {

                $alert = AlertAlert::where('complit_id', $deadline->complaint_id)
                                   ->where('is_active', 1)
                                   ->first();

                if ($alert) {
                    $this->info("Alerta ativo encontrado: Alert ID {$alert->id}");

                    $users = AlertUserAlertUser::where('alert_id', $alert->id)->get();
                    $this->info("Total de usuários a notificar: " . $users->count());

                    foreach ($users as $userAlert) {
                        $email = $userAlert->user->email ?? null;
                        if ($email) {
                            $this->info("Enviando email para: {$email}");
                            Mail::to($email)->send(new ComplaintDeadlineAlert($deadline));
                        } else {
                            $this->warn("Usuário ID {$userAlert->user_id} não possui email.");
                        }
                    }
                } else {
                    $this->warn("Nenhum alerta ativo encontrado para Complaint ID {$deadline->complaint_id}");
                }

                $deadline->update(['notified_at' => now()]);
                $this->info("Deadline ID {$deadline->id} marcado como notificado.");
            }

            // Atualiza status se expirado
            if ($deadline->isExpired()) {
                $deadline->update(['status' => 'expirado']);
                $this->info("Deadline ID {$deadline->id} expirado. Status atualizado.");
            }
        }

        $this->info('Verificação de prazos e envio de alertas concluída.');
    }
}
