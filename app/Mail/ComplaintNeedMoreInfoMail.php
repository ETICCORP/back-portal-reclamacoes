<?php

namespace App\Mail;

use App\Models\Complaint\Complaint;
use App\Models\ComplaintTriages\ComplaintTriages;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComplaintNeedMoreInfoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ?string $frontendUrl;

    /**
     * Criar uma nova instância da mensagem.
     * @param string $type Pode ser 'return' ou 'refusal'
     */
    public function __construct(
        public Complaint $complaint,
        public ?ComplaintTriages $triage,
        public string $type = 'return',
        ?string $frontendUrl = null
    ) {
        $this->frontendUrl = $frontendUrl ?? '';
    }

    /**
     * Define o assunto dinamicamente com base no tipo
     */
    public function envelope(): Envelope
    {
        $subject = $this->type === 'refusal'
            ? "Processo Recusado: ID #{$this->complaint->code}"
            : "Ação Necessária (Devolução): ID #{$this->complaint->code}";

        return new Envelope(subject: $subject);
    }

    /**
     * Define a view dinamicamente com base no tipo
     */
    public function content(): Content
    {
        // Define caminhos de views diferentes usando a mesma Mailable
        $viewTemplate = $this->type === 'refusal'
            ? 'emails.complaints.refused'      // Sua view criada para recusas (sem resumo)
            : 'emails.complaints.need_more_info'; // Sua view atual para devoluções

        return new Content(
            view: $viewTemplate,
            with: [
                'triage' => $this->triage,
                'complaint' => $this->complaint,
                'frontendUrl' => $this->frontendUrl
            ],
        );
    }
}
