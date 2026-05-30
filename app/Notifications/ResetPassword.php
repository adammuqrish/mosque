<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends BaseResetPassword implements ShouldQueue
{
    use Queueable;

    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            ->subject('Reset Kata Laluan - Smart Mosque System')
            ->greeting('Assalamu Alaikum')
            ->line('Anda menerima e-mel ini kerana kami menerima permintaan reset kata laluan untuk akaun anda.')
            ->action('Reset Kata Laluan', url(route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false)))
            ->line('Pautan ini akan tamat dalam 60 minit.')
            ->line('Jika anda tidak meminta reset kata laluan, anda boleh abaikan e-mel ini.');
    }
}
