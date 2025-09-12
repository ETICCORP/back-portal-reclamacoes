<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        $domain=request()->getSchemeAndHttpHost();
        $frontendUrl = config('app.frontend_url',  $domain);

        $resetUrl = $frontendUrl . '/reset-password?token=' . $this->token . '&email=' . urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
            ->subject('Redefinição de Senha')
            ->markdown('emails.auth.reset-password', [
                'resetUrl' => $resetUrl,
                'notifiable' => $notifiable,
            ]);
    }
}
