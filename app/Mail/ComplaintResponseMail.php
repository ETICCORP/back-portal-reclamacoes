<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Complaint\ComplaintResponse;

class ComplaintResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function build()
    {
        $mail = $this->subject($this->response->subject ?? 'Resposta à Reclamação')
                    ->view('emails.complaints.response')
                    ->with(['response' => $this->response]);

        if ($this->response->signature_path && file_exists(storage_path("app/public/{$this->response->signature_path}"))) {
            $mail->attach(storage_path("app/public/{$this->response->signature_path}"));
        }

        return $mail;
    }
}
