<?php

namespace App\Jobs;

use App\Mail\ReportAlertMail;
use App\Models\Alert\Alert;
use App\Models\Complaint\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReportCopy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $complaintId;

    /**
     * Cria uma instância do job.
     */
    public function __construct(int $complaintId)
    {
        $this->complaintId = $complaintId;
    }

    /**
     * Executa o job.
     */
    public function handle(): void
    {
        $complaint = Complaint::find($this->complaintId)->with('reporter', 'description', 'involveColleagues',);

        if (!$complaint) {
            Log::error("Complaint ID {$this->complaintId} not found.");
            return;
        }

        try {
            $alert = Alert::create([
                'complit_id' => $complaint->id,
                'is_active' => true,
            ]);

            Log::info("Alert created successfully for Complaint ID {$complaint->id}, Alert ID: {$alert->id}");

            //Se quiser enviar e-mail, descomente abaixo
            //SendGrupoAlertEmailJob::dispatch($alert->id);
            
            Mail::to($complaint->reporter->email)->send(new ReportAlertMail($complaint));



        } catch (\Exception $e) {
            Log::error("Error creating alert for Complaint ID {$complaint->id}: " . $e->getMessage());
        }
    }
}
