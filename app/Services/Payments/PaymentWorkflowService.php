<?php

namespace App\Services\Payments;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Student;
use App\Models\User;
use App\Notifications\PaymentApprovedNotification;
use App\Notifications\PaymentRejectedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWorkflowService
{
    public function __construct(private readonly ReceiptService $receiptService)
    {
    }

    public function resolvePaymentMethod(string $code, int $schoolId): ?PaymentMethod
    {
        $schoolSpecific = PaymentMethod::query()
            ->where('school_id', $schoolId)
            ->where('code', $code)
            ->first();

        if ($schoolSpecific) {
            return $schoolSpecific->is_active ? $schoolSpecific : null;
        }

        return PaymentMethod::query()
            ->whereNull('school_id')
            ->where('code', $code)
            ->where('is_active', true)
            ->first();
    }

    public function submitOfflinePayment(Student $student, Invoice $invoice, array $data, UploadedFile $proof, ?User $submittedBy = null): Payment
    {
        $this->assertInvoiceBelongsToStudent($invoice, $student);

        $methodCode = (string) $data['payment_method'];
        $method = $this->resolvePaymentMethod($methodCode, (int) $student->school_id);

        return DB::transaction(function () use ($student, $invoice, $data, $proof, $submittedBy, $methodCode, $method) {
            $path = $proof->store('payment-proofs/' . $student->school_id . '/' . now()->format('Y/m'));

            return Payment::query()->create([
                'school_id' => (int) $student->school_id,
                'invoice_id' => (int) $invoice->id,
                'student_id' => (int) $student->id,
                'payment_method_id' => $method?->id,
                'payment_reference' => Payment::generateReference(),
                'amount' => (float) $data['amount'],
                'amount_expected' => (float) $invoice->balance,
                'payment_method' => $methodCode,
                'status' => Payment::STATUS_PENDING,
                'payment_date' => $data['payment_date'],
                'paid_at' => null,
                'gateway_name' => null,
                'gateway_reference' => null,
                'bank_name' => $data['sender_bank'] ?? null,
                'account_name' => $data['sender_account_name'] ?? null,
                'receipt_number' => (string) ($data['payment_reference'] ?? ''),
                'notes' => $data['notes'] ?? null,
                'proof_file_path' => $path,
                'proof_original_name' => $proof->getClientOriginalName(),
                'submitted_by' => $submittedBy?->id,
                'meta_json' => [
                    'manual_reference' => (string) ($data['payment_reference'] ?? ''),
                    'submitted_from' => 'student_portal',
                ],
            ]);
        });
    }

    public function createOnlinePendingPayment(Student $student, Invoice $invoice, string $methodCode, float $amount, array $meta = [], ?User $submittedBy = null): Payment
    {
        $this->assertInvoiceBelongsToStudent($invoice, $student);

        $method = $this->resolvePaymentMethod($methodCode, (int) $student->school_id);

        return Payment::query()->create([
            'school_id' => (int) $student->school_id,
            'invoice_id' => (int) $invoice->id,
            'student_id' => (int) $student->id,
            'payment_method_id' => $method?->id,
            'payment_reference' => Payment::generateReference(),
            'amount' => $amount,
            'amount_expected' => (float) $invoice->balance,
            'payment_method' => $methodCode,
            'payment_gateway' => $methodCode,
            'gateway_name' => $methodCode,
            'status' => Payment::STATUS_PENDING,
            'submitted_by' => $submittedBy?->id,
            'meta_json' => $meta,
        ]);
    }

    public function recordCashPayment(User $actor, Invoice $invoice, array $data): Payment
    {
        $method = $this->resolvePaymentMethod('cash', (int) $invoice->school_id);
        $markApproved = (bool) ($data['mark_approved'] ?? true);

        $payment = Payment::query()->create([
            'school_id' => (int) $invoice->school_id,
            'invoice_id' => (int) $invoice->id,
            'student_id' => (int) $invoice->student_id,
            'payment_method_id' => $method?->id,
            'payment_reference' => (string) ($data['payment_reference'] ?? Payment::generateReference()),
            'amount' => (float) $data['amount'],
            'amount_expected' => (float) $invoice->balance,
            'payment_method' => 'cash',
            'status' => $markApproved ? Payment::STATUS_APPROVED : Payment::STATUS_PENDING,
            'payment_date' => $data['payment_date'] ?? now()->toDateString(),
            'paid_at' => $markApproved ? now() : null,
            'approved_at' => $markApproved ? now() : null,
            'confirmed_by' => $markApproved ? (int) $actor->id : null,
            'verified_by' => $markApproved ? (int) $actor->id : null,
            'verified_at' => $markApproved ? now() : null,
            'verification_note' => $data['notes'] ?? null,
            'submitted_by' => (int) $actor->id,
            'notes' => $data['notes'] ?? null,
            'gateway_name' => null,
            'gateway_reference' => null,
        ]);

        if ($markApproved) {
            $this->approve($payment, $actor, (string) ($data['notes'] ?? 'Cash payment confirmed by admin.'), notifyStudent: true);
        }

        return $payment->refresh();
    }

    public function approve(Payment $payment, ?User $verifier = null, ?string $note = null, array $meta = [], bool $notifyStudent = true): Payment
    {
        return DB::transaction(function () use ($payment, $verifier, $note, $meta, $notifyStudent) {
            $payment->refresh();

            if (!$payment->isSuccessful()) {
                $existingMeta = is_array($payment->meta_json) ? $payment->meta_json : [];
                $verifierId = $verifier?->id;

                $payment->update([
                    'status' => Payment::STATUS_APPROVED,
                    'verified_by' => $verifierId,
                    'verified_at' => now(),
                    'verification_note' => $note,
                    'approved_at' => now(),
                    'confirmed_by' => $verifierId,
                    'paid_at' => $payment->paid_at ?: now(),
                    'rejection_reason' => null,
                    'meta_json' => array_merge($existingMeta, $meta),
                ]);
            }

            $payment->loadMissing(['invoice', 'student.user']);
            $payment->invoice?->updateBalance();
            $receipt = $this->receiptService->createOrGetReceipt($payment, $verifier?->id);

            $studentUser = $payment->student?->user;
            if ($notifyStudent && $studentUser && $studentUser->email) {
                try {
                    $studentUser->notify(new PaymentApprovedNotification($payment->fresh(['invoice', 'student']), $receipt));
                } catch (\Throwable $exception) {
                    Log::warning('Payment approved but notification delivery failed.', [
                        'payment_id' => (int) $payment->id,
                        'student_id' => (int) ($payment->student_id ?? 0),
                        'student_user_id' => (int) ($studentUser->id ?? 0),
                        'student_email' => (string) $studentUser->email,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }

            return $payment->fresh(['invoice', 'student', 'receipt']);
        });
    }

    public function reject(Payment $payment, User $verifier, string $reason, ?string $note = null): Payment
    {
        return DB::transaction(function () use ($payment, $verifier, $reason, $note) {
            $payment->update([
                'status' => Payment::STATUS_REJECTED,
                'verified_by' => (int) $verifier->id,
                'verified_at' => now(),
                'verification_note' => $note,
                'rejection_reason' => $reason,
                'approved_at' => null,
            ]);

            $payment->loadMissing(['invoice', 'student.user']);
            $payment->invoice?->updateBalance();

            $studentUser = $payment->student?->user;
            if ($studentUser && $studentUser->email) {
                try {
                    $studentUser->notify(new PaymentRejectedNotification($payment->fresh(['invoice', 'student'])));
                } catch (\Throwable $exception) {
                    Log::warning('Payment rejected but notification delivery failed.', [
                        'payment_id' => (int) $payment->id,
                        'student_id' => (int) ($payment->student_id ?? 0),
                        'student_user_id' => (int) ($studentUser->id ?? 0),
                        'student_email' => (string) $studentUser->email,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }

            return $payment->fresh(['invoice', 'student']);
        });
    }

    public function markUnderReview(Payment $payment, User $verifier, ?string $note = null): Payment
    {
        $payment->update([
            'status' => Payment::STATUS_UNDER_REVIEW,
            'verified_by' => (int) $verifier->id,
            'verified_at' => now(),
            'verification_note' => $note,
        ]);

        return $payment->fresh(['invoice', 'student']);
    }

    protected function assertInvoiceBelongsToStudent(Invoice $invoice, Student $student): void
    {
        if ((int) $invoice->student_id !== (int) $student->id || (int) $invoice->school_id !== (int) $student->school_id) {
            abort(422, 'Selected invoice is not assigned to the authenticated student.');
        }
    }
}
