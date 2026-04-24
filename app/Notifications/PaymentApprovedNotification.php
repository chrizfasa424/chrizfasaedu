<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Models\Receipt;
use App\Services\Payments\ReceiptService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Throwable;

class PaymentApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Payment $payment,
        public Receipt $receipt,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $student = $this->payment->student;
        $invoice = $this->payment->invoice;
        $receiptUrl = route('portal.payments.receipt', $this->receipt);
        $mail = (new MailMessage)
            ->subject('Payment Approved: ' . ($invoice?->invoice_number ?: $this->receipt->receipt_number))
            ->greeting('Hello ' . ($student?->full_name ?: 'Student') . ',')
            ->line('Your payment has been approved successfully.')
            ->line('Student ID: ' . (string) ($student?->admission_number ?: $student?->registration_number ?: $student?->id))
            ->line('Fee / Invoice: ' . (string) ($invoice?->invoice_number ?: 'N/A'))
            ->line('Amount Paid: NGN ' . number_format((float) $this->payment->amount, 2))
            ->line('Payment Method: ' . ucwords(str_replace('_', ' ', (string) $this->payment->payment_method)))
            ->line('Receipt Number: ' . $this->receipt->receipt_number)
            ->action('View Receipt', $receiptUrl)
            ->line('Thank you.');

        try {
            /** @var ReceiptService $receiptService */
            $receiptService = app(ReceiptService::class);
            $pdfBinary = $receiptService->receiptPdf($this->payment)->output();
            $mail->attachData($pdfBinary, $receiptService->safeReceiptFilename($this->payment), [
                'mime' => 'application/pdf',
            ]);
        } catch (Throwable $e) {
            // Keep notification delivery resilient if PDF attach fails.
        }

        return $mail;
    }
}
