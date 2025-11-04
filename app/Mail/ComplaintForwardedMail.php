<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Complaint\ComplaintProvider;

class ComplaintForwardedMail extends Mailable
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
        $email = $this->subject("Nova Reclamação Encaminhada: ID {$this->complaintProvider->complaint_id}")
                      ->view('emails.providers.complaint_forwarded')
                      ->with([
                          'complaintProvider' => $this->complaintProvider,
                      ]);

        
        return $email;
    }
}
