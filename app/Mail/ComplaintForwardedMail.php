<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComplaintForwardedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $complaintProvider;
    public string $target;

    /**
     * Create a new message instance.
     * * @param mixed $complaintProvider Registo da tabela complaint_provider
     * @param string $target Destinatário do e-mail: 'provider' ou 'reclamante'
     */
    public function __construct($complaintProvider, string $target = 'provider')
    {
        $this->complaintProvider = $complaintProvider;
        $this->target = $target;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $code = $this->complaintProvider->complaint->code ?? 'N/D';

        // 1. Define dinamicamente o Assunto e a View com base no Target
        if ($this->target === 'reclamante') {
            $subject = "Atualização do Processo: ID #{$code}";
            $view = 'complaint_forwarded_reclamante';
        } else {
            $subject = "Nova Reclamação Encaminhada: ID #{$code}";
            $view = 'complaint_forwarded';
        }
        
        return $this->subject($subject)
            ->view('emails.providers.' . $view)
            ->with([
                // Injeta as duas variáveis para garantir que nenhuma das views falhe por falta de mapeamento
                'complaintProvider' => $this->complaintProvider,
                'latestForward'     => $this->complaintProvider,
            ]);
    }
}
