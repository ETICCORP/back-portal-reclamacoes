<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComplaintUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;
    public $statusMessage;
    public $customSubject;

    public function __construct($complaint, $statusMessage, $customSubject)
    {
        $this->complaint = $complaint;
        $this->statusMessage = $statusMessage;
        $this->customSubject = $customSubject;
    }

    public function build()
    {
        return $this->subject($this->customSubject)
            ->view('emails.complaint_updated');
    }
}
