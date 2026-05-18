<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends BaseVerifyEmail
{
    use Queueable;

    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('Sila Sahkan E-mel Anda - Smart Mosque System')
            ->greeting('Assalamu Alaikum ' . ($this->user->name ?: ''))
            ->line('Terima kasih kerana mendaftar ke Smart Mosque System. Sila klik butang di bawah untuk mengesahkan alamat e-mel anda.')
            ->action('Sahkan E-mel', $url)
            ->line('Jika anda tidak mendaftar akaun ini, anda boleh abaikan e-mel ini.');
    }
}
