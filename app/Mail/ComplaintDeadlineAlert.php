<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Complaint\ComplaintDeadline;

class ComplaintDeadlineAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $deadline;

    public function __construct(ComplaintDeadline $deadline)
    {
        $this->deadline = $deadline;
    }

  public function build()
{
    return $this->subject('Alerta de prazo de reclamação')
                ->view('emails.complaints.deadline_alert', [
                    'deadline' => $this->deadline,
                ]);
}

}
