<?php

namespace App\Mail;

use App\Models\Complaint\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComplaintUpdatedReclamanteMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Complaint $complaint;

    /**
     * Create a new message instance.
     */
    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject("Exposição Atualizada com Sucesso - ID #{$this->complaint->code}")
            ->view('emails.complaint_updated_reclamante')
            ->with([
                'complaint' => $this->complaint,
            ]);
    }
}