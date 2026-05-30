<?php

namespace App\Notifications;

use App\Models\WithdrawalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WithdrawalRequestNotification extends Notification
{
    use Queueable;

    protected $withdrawalRequest;
    protected $action;

    public function __construct(WithdrawalRequest $withdrawalRequest, string $action = 'created')
    {
        $this->withdrawalRequest = $withdrawalRequest;
        $this->action = $action;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $title = '';
        $message = '';
        $icon = '';
        $iconColor = '';

        switch ($this->action) {
            case 'created':
                $title = 'New Withdrawal Request';
                $message = 'A new withdrawal request of <strong>RM ' . number_format($this->withdrawalRequest->amount, 2) . '</strong> has been submitted by ' . $this->withdrawalRequest->requester->name . ' for <strong>' . e($this->withdrawalRequest->purpose) . '</strong>.';
                $icon = 'fa-money-bill-wave';
                $iconColor = 'text-yellow-500';
                break;

            case 'approved':
                $title = 'Withdrawal Approved';
                $message = 'Your withdrawal request of <strong>RM ' . number_format($this->withdrawalRequest->amount, 2) . '</strong> for <strong>' . e($this->withdrawalRequest->purpose) . '</strong> has been <span class="text-green-600 font-semibold">APPROVED</span> by ' . ($this->withdrawalRequest->approver->name ?? 'a treasurer') . '.';
                $icon = 'fa-check-circle';
                $iconColor = 'text-green-500';
                break;

            case 'maker_checked':
                $title = 'Withdrawal Needs Second Approval';
                $message = 'A withdrawal request of <strong>RM ' . number_format($this->withdrawalRequest->amount, 2) . '</strong> for <strong>' . e($this->withdrawalRequest->purpose) . '</strong> has been checked by ' . ($this->withdrawalRequest->makerChecker->name ?? 'a treasurer') . ' and needs a second treasurer to approve.';
                $icon = 'fa-user-check';
                $iconColor = 'text-orange-500';
                break;

            case 'rejected':
                $title = 'Withdrawal Rejected';
                $message = 'Your withdrawal request of <strong>RM ' . number_format($this->withdrawalRequest->amount, 2) . '</strong> for <strong>' . e($this->withdrawalRequest->purpose) . '</strong> has been <span class="text-red-600 font-semibold">REJECTED</span>.';
                if ($this->withdrawalRequest->rejection_reason) {
                    $message .= '<br><em>Reason: ' . e($this->withdrawalRequest->rejection_reason) . '</em>';
                }
                $icon = 'fa-times-circle';
                $iconColor = 'text-red-500';
                break;
        }

        return [
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'icon_color' => $iconColor,
            'withdrawal_id' => $this->withdrawalRequest->id,
            'action' => $this->action,
            'url' => route('withdrawals.index'),
        ];
    }

    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }
}
