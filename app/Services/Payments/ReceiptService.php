<?php

namespace App\Services\Payments;

use App\Models\BursarySignature;
use App\Models\Payment;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class ReceiptService
{
    public function createOrGetReceipt(Payment $payment, ?int $generatedBy = null): Receipt
    {
        $payment->loadMissing(['invoice.session', 'invoice.term', 'student.schoolClass', 'student.arm', 'school', 'receipt']);

        if ($payment->receipt) {
            return $payment->receipt;
        }

        return Receipt::query()->create([
            'school_id' => (int) $payment->school_id,
            'payment_id' => (int) $payment->id,
            'receipt_number' => $this->generateReceiptNumber((int) $payment->school_id),
            'generated_by' => $generatedBy,
        ]);
    }

    public function generateReceiptNumber(int $schoolId): string
    {
        $count = Receipt::query()->where('school_id', $schoolId)->count() + 1;

        return sprintf('RCP-%s-%06d', date('Y'), $count);
    }

    public function receiptPdf(Payment $payment)
    {
        $receipt = $this->createOrGetReceipt($payment, auth()->id());
        $payment->loadMissing(['invoice.items', 'invoice.session', 'invoice.term', 'student.schoolClass', 'student.arm', 'school']);

        $signatureQuery = BursarySignature::query()
            ->where('school_id', (int) $payment->school_id)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->latest('id');

        if (Schema::hasColumn('bursary_signatures', 'signature_role')) {
            $signature = (clone $signatureQuery)
                ->where('signature_role', BursarySignature::ROLE_BURSAR)
                ->first();
        } else {
            $signature = null;
        }

        if (!$signature) {
            $signature = $signatureQuery->first();
        }

        return Pdf::loadView('financial.receipts.pdf', [
            'payment' => $payment,
            'receipt' => $receipt,
            'signature' => $signature,
            'amountInWords' => $this->amountInWords((float) $payment->amount),
        ]);
    }

    public function safeReceiptFilename(Payment $payment): string
    {
        $payment->loadMissing('student');
        $identity = (string) ($payment->student?->admission_number ?: $payment->student_id);
        $identity = Str::of($identity)
            ->replace(['\\', '/', ' '], '-')
            ->replaceMatches('/[^A-Za-z0-9_.-]/', '')
            ->trim('-_.')
            ->value();

        if ($identity === '') {
            $identity = (string) $payment->student_id;
        }

        return 'receipt-' . $identity . '-' . $payment->payment_reference . '.pdf';
    }

    protected function amountInWords(float $amount): string
    {
        $whole = (int) floor($amount);
        $fraction = (int) round(($amount - $whole) * 100);

        if (class_exists(\NumberFormatter::class)) {
            $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
            $words = ucfirst((string) $formatter->format($whole)) . ' naira';
            if ($fraction > 0) {
                $words .= ' and ' . $formatter->format($fraction) . ' kobo';
            }

            return $words;
        }

        return number_format($amount, 2) . ' naira';
    }
}
