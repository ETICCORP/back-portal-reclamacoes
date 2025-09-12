<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GrupoAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $alert;

    public function __construct($user, $alert)
    {
        $this->user  = $user;
        $this->alert = $alert;
    }

    public function build()
    {
        Log::info('GrupoAlertMail build', [
            'alert_id' => $this->alert->id ?? null,
            'alert_class' => get_class($this->alert),
        ]);

        return $this->subject('Notificação de Grupo de Alerta')
            ->view('emails.grupo.alert')
            ->with([
                'user' => $this->user,
                'alert' => $this->alert,
            ]);
    }
}
