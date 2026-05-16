<?php

namespace App\Mail;

use App\Models\Complaint\Complaint;
use App\Models\Complaint\ComplaintOpinions;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComplaintOpinionAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Complaint $complaint,
        public ComplaintOpinions $opinion
    ) {}

    public function build()
    {
        return $this->subject('Nova Opinião Técnica - Protocolo #' . $this->complaint->code)
            ->view('emails.complaint_opinion_notification');
    }
}
