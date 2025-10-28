<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReportAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $dados;

    public function __construct($dados)
    {
        $this->dados  = $dados;
    }

    public function build()
    {

        return $this->subject('Resumo da Reclamnação')
            ->view('emails.report_resume')
            ->with([
                'complaint' => $this->dados,
            ]);
    }
}
