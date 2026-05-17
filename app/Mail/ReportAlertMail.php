<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $dados;

    public function __construct($dados)
    {
        $this->dados  = $dados;
    }

    public function build()
    {
        return $this->subject('Resumo da Reclamação')
            ->view('emails.report_resume')
            ->with([
                'complaint' => $this->dados,
            ]);
    }
}
