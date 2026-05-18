<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends BaseResetPassword
{
    use Queueable;

    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('Reset Kata Laluan - Smart Mosque System')
            ->greeting('Assalamu Alaikum')
            ->line('Anda menerima e-mel ini kerana kami menerima permintaan reset kata laluan untuk akaun anda.')
            ->action('Reset Kata Laluan', $url)
            ->line('Pautan ini akan tamat dalam 60 minit.')
            ->line('Jika anda tidak meminta reset kata laluan, anda boleh abaikan e-mel ini.');
    }
}
