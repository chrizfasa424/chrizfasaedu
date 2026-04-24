<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(public Payment $payment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $student = $this->payment->student;
        $mail = (new MailMessage)
            ->subject('Payment Update: Submission Rejected')
            ->greeting('Hello ' . ($student?->full_name ?: 'Student') . ',')
            ->line('Your payment submission was reviewed and could not be approved yet.')
            ->line('Student ID: ' . (string) ($student?->admission_number ?: $student?->registration_number ?: $student?->id))
            ->line('Payment Reference: ' . $this->payment->payment_reference)
            ->action('Review Payment Status', route('portal.payments.index'))
            ->line('You can submit updated proof from your portal if needed.');

        $reason = trim((string) $this->payment->rejection_reason);
        if ($reason !== '') {
            $mail->line('Reason: ' . $reason);
        }

        return $mail;
    }
}