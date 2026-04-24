<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\RecordCashPaymentRequest;
use App\Http\Requests\Financial\VerifyPaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Payments\Gateway\PaystackGatewayService;
use App\Services\Payments\PaymentWorkflowService;
use App\Services\Payments\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    protected function authorizeFinanceUser(): void
    {
        $user = auth()->user();

        abort_unless($user && in_array((string) ($user->role?->value ?? ''), [
            'super_admin',
            'school_admin',
            'principal',
            'vice_principal',
            'accountant',
        ], true), 403, 'Unauthorized access.');
    }

    public function index(Request $request)
    {
        $this->authorizeFinanceUser();

        $query = Payment::query()
            ->with([
                'student.schoolClass',
                'invoice.session',
                'invoice.term',
                'paymentMethod',
                'verifier',
                'receipt',
            ])
            ->latest('id');

        if ($request->filled('status')) {
            $query->where('status', (string) $request->string('status'));
        }

        if ($request->filled('method')) {
            $query->where('payment_method', (string) $request->string('method'));
        }

        if ($request->filled('student')) {
            $student = trim((string) $request->string('student'));
            $query->whereHas('student', function ($scope) use ($student) {
                $scope->where('admission_number', 'like', '%' . $student . '%')
                    ->orWhere('registration_number', 'like', '%' . $student . '%')
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ['%' . $student . '%']);
            });
        }

        $payments = $query->paginate(20)->withQueryString();

        $statuses = [
            Payment::STATUS_PENDING,
            Payment::STATUS_UNDER_REVIEW,
            Payment::STATUS_APPROVED,
            Payment::STATUS_REJECTED,
            Payment::STATUS_FAILED,
            Payment::STATUS_CANCELLED,
            Payment::STATUS_CONFIRMED_LEGACY,
        ];

        $methods = ['bank_transfer', 'pos', 'cash', 'flutterwave', 'paystack'];

        $pendingCount = Payment::query()
            ->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_UNDER_REVIEW])
            ->count();

        return view('financial.payments.index', compact('payments', 'statuses', 'methods', 'pendingCount'));
    }

    public function review(Payment $payment)
    {
        $this->authorizeFinanceUser();

        $payment->load([
            'student.schoolClass',
            'student.arm',
            'invoice.items',
            'invoice.session',
            'invoice.term',
            'receipt',
            'verifier',
            'submitter',
        ]);

        return view('financial.payments.review', compact('payment'));
    }

    public function verify(VerifyPaymentRequest $request, Payment $payment, PaymentWorkflowService $workflow)
    {
        $this->authorizeFinanceUser();

        $action = (string) $request->string('action');

        if ($action === 'approve') {
            $workflow->approve($payment, auth()->user(), $request->input('verification_note'));
            return back()->with('success', 'Payment approved successfully.');
        }

        if ($action === 'reject') {
            $workflow->reject(
                $payment,
                auth()->user(),
                (string) $request->input('rejection_reason'),
                $request->input('verification_note')
            );

            return back()->with('success', 'Payment rejected and student notified.');
        }

        $workflow->markUnderReview($payment, auth()->user(), $request->input('verification_note'));

        return back()->with('success', 'Payment moved to under review.');
    }

    public function recordManual(RecordCashPaymentRequest $request, PaymentWorkflowService $workflow)
    {
        $this->authorizeFinanceUser();

        $invoice = Invoice::query()->findOrFail((int) $request->integer('invoice_id'));

        if ((float) $request->input('amount') > ((float) $invoice->balance + 0.01)) {
            return back()->withErrors([
                'amount' => 'Amount should not exceed invoice balance.',
            ])->withInput();
        }

        if ((string) $request->input('payment_method') !== 'cash') {
            $payment = Payment::query()->create([
                'school_id' => (int) $invoice->school_id,
                'invoice_id' => (int) $invoice->id,
                'student_id' => (int) $invoice->student_id,
                'payment_reference' => (string) ($request->input('payment_reference') ?: Payment::generateReference()),
                'amount' => (float) $request->input('amount'),
                'amount_expected' => (float) $invoice->balance,
                'payment_method' => (string) $request->input('payment_method'),
                'status' => Payment::STATUS_PENDING,
                'payment_date' => $request->input('payment_date'),
                'notes' => (string) $request->input('notes', ''),
                'submitted_by' => auth()->id(),
            ]);

            return redirect()
                ->route('financial.payments.review', $payment)
                ->with('success', 'Payment logged as pending for verification.');
        }

        $payment = $workflow->recordCashPayment(auth()->user(), $invoice, $request->validated());

        return redirect()
            ->route('financial.payments.review', $payment)
            ->with('success', 'Cash payment recorded successfully.');
    }

    public function initiatePaystack(Request $request, PaystackGatewayService $paystack, PaymentWorkflowService $workflow)
    {
        $this->authorizeFinanceUser();

        $data = $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:100'],
            'email' => ['required', 'email'],
        ]);

        $invoice = Invoice::query()->findOrFail((int) $data['invoice_id']);
        $student = $invoice->student;
        abort_unless($student, 422, 'Invoice student not found.');
        abort_unless($workflow->resolvePaymentMethod('paystack', (int) $invoice->school_id), 422, 'Paystack is currently disabled for this school.');

        $payment = $workflow->createOnlinePendingPayment(
            $student,
            $invoice,
            'paystack',
            (float) $data['amount'],
            ['initiated_from' => 'admin_finance'],
            auth()->user()
        );

        $response = $paystack->initialize([
            'email' => (string) $data['email'],
            'amount' => (int) round(((float) $data['amount']) * 100),
            'reference' => $payment->payment_reference,
            'callback_url' => route('financial.payments.paystack.callback'),
            'metadata' => [
                'invoice_id' => $invoice->id,
                'student_id' => $student->id,
                'payment_id' => $payment->id,
            ],
        ], (int) $invoice->school_id);

        if (!$response['ok']) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'gateway_response' => $response['raw'],
            ]);

            return back()->with('error', 'Unable to initialize Paystack payment.');
        }

        return redirect((string) ($response['data']['authorization_url'] ?? route('financial.invoices.show', $invoice)));
    }

    public function paystackCallback(Request $request, PaystackGatewayService $paystack, PaymentWorkflowService $workflow)
    {
        $this->authorizeFinanceUser();

        $reference = (string) $request->query('reference', '');
        abort_if($reference === '', 422, 'Missing payment reference.');

        $payment = Payment::query()->where('payment_reference', $reference)->firstOrFail();
        $verification = $paystack->verify($reference, (int) $payment->school_id);

        if (!$verification['ok']) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'gateway_response' => $verification['raw'],
            ]);

            return redirect()
                ->route('financial.invoices.show', $payment->invoice_id)
                ->with('error', 'Payment verification failed.');
        }

        $verifiedReference = (string) ($verification['data']['reference'] ?? '');
        if ($verifiedReference !== '' && $verifiedReference !== $reference) {
            return redirect()
                ->route('financial.invoices.show', $payment->invoice_id)
                ->with('error', 'Payment verification reference mismatch.');
        }

        $verifiedAmountKobo = (int) ($verification['data']['amount'] ?? 0);
        $expectedAmountKobo = (int) round((float) $payment->amount * 100);
        if ($verifiedAmountKobo > 0 && $verifiedAmountKobo !== $expectedAmountKobo) {
            return redirect()
                ->route('financial.invoices.show', $payment->invoice_id)
                ->with('error', 'Payment amount does not match expected value.');
        }

        $payment->update([
            'transaction_id' => (string) ($verification['data']['id'] ?? ''),
            'gateway_reference' => (string) ($verification['data']['reference'] ?? $reference),
            'gateway_response' => $verification['raw'],
            'payment_date' => now()->toDateString(),
        ]);

        $workflow->approve($payment, auth()->user(), 'Paystack callback verification', [
            'gateway' => 'paystack',
        ], notifyStudent: true);

        return redirect()
            ->route('financial.payments.review', $payment)
            ->with('success', 'Payment verified and approved.');
    }

    public function proofFile(Payment $payment)
    {
        $this->authorizeFinanceUser();

        $path = ltrim((string) $payment->proof_file_path, '/');
        abort_if($path === '', 404, 'No proof file uploaded.');
        abort_if(!Storage::disk('local')->exists($path), 404, 'Proof file not found.');

        $requestedName = basename((string) ($payment->proof_original_name ?: ''));
        $safeName = preg_replace('/[^A-Za-z0-9._-]+/', '-', $requestedName ?? '') ?: '';
        $downloadName = $safeName !== '' ? $safeName : basename($path);

        return Storage::disk('local')->download($path, $downloadName);
    }

    public function receipt(Payment $payment, ReceiptService $receiptService)
    {
        $this->authorizeFinanceUser();

        abort_unless($payment->isSuccessful(), 422, 'Receipt can only be generated for approved payments.');

        $pdf = $receiptService->receiptPdf($payment);

        return $pdf->download($receiptService->safeReceiptFilename($payment));
    }
}
