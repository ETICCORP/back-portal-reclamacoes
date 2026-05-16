<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class ComplaintResponseMail extends Mailable implements ShouldQueue
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

    // 🔹 Adiciona assinatura inline, se existir
    if ($this->response->signature_path && Storage::disk('public')->exists($this->response->signature_path)) {
        $mail->attachFromStorageDisk(
            'public',
            $this->response->signature_path,
            'assinatura.png',
            [
                'as' => 'assinatura.png',
                'mime' => 'image/png',
            ]
        );
    }

    return $mail;
}

}
