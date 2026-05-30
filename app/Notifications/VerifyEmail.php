<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends BaseVerifyEmail implements ShouldQueue
{
    use Queueable;

    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Sila Sahkan E-mel Anda - Smart Mosque System')
            ->greeting('Assalamu Alaikum ' . ($notifiable->name ?: ''))
            ->line('Terima kasih kerana mendaftar ke Smart Mosque System. Sila klik butang di bawah untuk mengesahkan alamat e-mel anda.')
            ->action('Sahkan E-mel', $verificationUrl)
            ->line('Jika anda tidak mendaftar akaun ini, anda boleh abaikan e-mel ini.');
    }
}
