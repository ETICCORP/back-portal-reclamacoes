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

    /**
     * Create a new message instance.
     */
    public function __construct($complaintProvider)
    {
        $this->complaintProvider = $complaintProvider;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $email = $this->subject("Nova Reclamação Encaminhada: ID #{$this->complaintProvider->complaint->code}")
            ->view('emails.providers.complaint_forwarded')
            ->with([
                'complaintProvider' => $this->complaintProvider,
            ]);

        return $email;
    }
}
