<?php

namespace App\Mail;

use App\Models\Complaint\Complaint;
use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComplaintUpdatedAnalistaMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Complaint $complaint;
    public User $analista;

    /**
     * Create a new message instance.
     */
    public function __construct(Complaint $complaint, User $analista)
    {
        $this->complaint = $complaint;
        $this->analista  = $analista;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject("Notificação: Retificação de Dados no Protocolo #{$this->complaint->code}")
            ->view('emails.complaint_updated_analista')
            ->with([
                'complaint' => $this->complaint,
                'analista'  => $this->analista,
            ]);
    }
}