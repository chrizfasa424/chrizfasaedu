<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\InitiateOnlinePaymentRequest;
use App\Http\Requests\Portal\StoreOfflinePaymentSubmissionRequest;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Receipt;
use App\Models\SchoolBankAccount;
use App\Services\Payments\Gateway\FlutterwaveGatewayService;
use App\Services\Payments\Gateway\PaystackGatewayService;
use App\Services\Payments\StudentFeeSyncService;
use App\Services\Payments\PaymentWorkflowService;
use App\Services\Payments\ReceiptService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class StudentPaymentController extends Controller
{
    protected function portalUser()
    {
        return auth('portal')->user() ?? auth()->user();
    }

    protected function studentContext(): array
    {
        $user = $this->portalUser();
        $student = $user?->student;
        $school = $user?->school;

        if (!$user || !$student || !$school || (string) ($user->role?->value ?? '') !== 'student') {
            abort(403, 'Student profile is not available.');
        }

        $student->loadMissing(['schoolClass', 'arm']);

        return [$user, $student, $school];
    }

    public function index(Request $request, StudentFeeSyncService $studentFeeSyncService)
    {
        [, $student, $school] = $this->studentContext();
        $studentFeeSyncService->syncCurrentForStudent($student, $school);
        $paymentSyncNotice = $this->buildPaymentSyncNotice($student, $school);

        $invoices = Invoice::query()
            ->with(['session', 'term'])
            ->where('student_id', (int) $student->id)
            ->where('school_id', (int) $school->id)
            ->latest('id')
            ->get();

        $outstandingInvoices = $invoices->filter(fn (Invoice $invoice) => (float) $invoice->balance > 0)->values();

        $selectedInvoiceId = (int) $request->integer('invoice_id');
        $selectedInvoice = $selectedInvoiceId > 0
            ? $invoices->firstWhere('id', $selectedInvoiceId)
            : $outstandingInvoices->first();

        $payments = Payment::query()
            ->with(['invoice.session', 'invoice.term', 'receipt'])
            ->where('student_id', (int) $student->id)
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $bankAccounts = SchoolBankAccount::query()
            ->where('school_id', (int) $school->id)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->latest('id')
            ->get();

        $activeMethods = PaymentMethod::query()
            ->where(function ($query) use ($school) {
                $query->whereNull('school_id')
                    ->orWhere('school_id', (int) $school->id);
            })
            ->orderByRaw('case when school_id is null then 1 else 0 end')
            ->orderBy('name')
            ->get()
            ->groupBy('code')
            ->map(fn ($items) => $items->first())
            ->filter(fn ($method) => (bool) $method->is_active);

        $offlineMethods = $activeMethods
            ->filter(fn ($method, $code) => in_array((string) $code, ['bank_transfer', 'pos'], true))
            ->mapWithKeys(fn ($method) => [$method->code => $method->name])
            ->all();

        $onlineMethods = $activeMethods
            ->filter(fn ($method, $code) => in_array((string) $code, ['paystack', 'flutterwave'], true))
            ->mapWithKeys(fn ($method) => [$method->code => $method->name])
            ->all();

        return view('portal.student.payments.index', compact(
            'student',
            'school',
            'invoices',
            'outstandingInvoices',
            'selectedInvoice',
            'payments',
            'bankAccounts',
            'offlineMethods',
            'onlineMethods',
            'paymentSyncNotice'
        ));
    }

    public function invoices(Request $request, StudentFeeSyncService $studentFeeSyncService)
    {
        [, $student, $school] = $this->studentContext();
        $studentFeeSyncService->syncCurrentForStudent($student, $school);
        $paymentSyncNotice = $this->buildPaymentSyncNotice($student, $school);

        $baseQuery = Invoice::query()
            ->where('student_id', (int) $student->id)
            ->where('school_id', (int) $school->id);

        $statusFilter = strtolower((string) $request->string('status')->toString());
        $sessionFilter = (int) $request->integer('session_id');
        $termFilter = (int) $request->integer('term_id');
        $search = trim((string) $request->string('q')->toString());

        $query = (clone $baseQuery)->with(['session', 'term'])->withCount('items');

        if ($statusFilter !== '' && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($sessionFilter > 0) {
            $query->where('session_id', $sessionFilter);
        }

        if ($termFilter > 0) {
            $query->where('term_id', $termFilter);
        }

        if ($search !== '') {
            $query->where('invoice_number', 'like', '%' . $search . '%');
        }

        $invoices = $query
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $allInvoices = (clone $baseQuery)
            ->get(['id', 'status', 'net_amount', 'amount_paid', 'balance']);

        $totalInvoices = $allInvoices->count();
        $outstandingCount = $allInvoices->filter(fn (Invoice $invoice) => (float) $invoice->balance > 0)->count();
        $paidCount = $allInvoices->filter(fn (Invoice $invoice) => (float) $invoice->balance <= 0)->count();
        $totalNetAmount = (float) $allInvoices->sum(fn (Invoice $invoice) => (float) $invoice->net_amount);
        $totalPaidAmount = (float) $allInvoices->sum(fn (Invoice $invoice) => (float) $invoice->amount_paid);
        $totalOutstandingAmount = (float) $allInvoices->sum(fn (Invoice $invoice) => max(0, (float) $invoice->balance));

        $sessions = (clone $baseQuery)
            ->with('session:id,name')
            ->whereNotNull('session_id')
            ->get()
            ->pluck('session')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $terms = (clone $baseQuery)
            ->with('term:id,name,session_id')
            ->whereNotNull('term_id')
            ->get()
            ->pluck('term')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        return view('portal.student.payments.invoices', compact(
            'student',
            'school',
            'invoices',
            'sessions',
            'terms',
            'statusFilter',
            'sessionFilter',
            'termFilter',
            'search',
            'totalInvoices',
            'outstandingCount',
            'paidCount',
            'totalNetAmount',
            'totalPaidAmount',
            'totalOutstandingAmount',
            'paymentSyncNotice'
        ));
    }

    public function invoiceShow(Invoice $invoice)
    {
        [, $student, $school] = $this->studentContext();

        $invoice = $this->resolveStudentInvoice(
            (int) $invoice->id,
            (int) $student->id,
            (int) $school->id
        );

        return view('portal.student.payments.invoice-show', compact('invoice', 'student', 'school'));
    }

    public function invoicePrint(Invoice $invoice)
    {
        [, $student, $school] = $this->studentContext();

        $invoice = $this->resolveStudentInvoice(
            (int) $invoice->id,
            (int) $student->id,
            (int) $school->id
        );

        return view('portal.student.payments.invoice-print', [
            'invoice' => $invoice,
            'asPdf' => false,
        ]);
    }

    public function invoicePdf(Invoice $invoice)
    {
        [, $student, $school] = $this->studentContext();

        $invoice = $this->resolveStudentInvoice(
            (int) $invoice->id,
            (int) $student->id,
            (int) $school->id
        );

        $pdf = Pdf::loadView('portal.student.payments.invoice-print', [
            'invoice' => $invoice,
            'asPdf' => true,
        ]);

        return $pdf->download($this->safeInvoiceFilename((string) $invoice->invoice_number));
    }

    public function submitOffline(StoreOfflinePaymentSubmissionRequest $request, PaymentWorkflowService $workflow)
    {
        [$user, $student, $school] = $this->studentContext();

        $invoice = $this->resolveStudentInvoice(
            (int) $request->integer('invoice_id'),
            (int) $student->id,
            (int) $school->id
        );
        $amount = (float) $request->input('amount');

        if ($amount <= 0 || $amount > ((float) $invoice->balance + 0.01)) {
            return back()->withErrors([
                'amount' => 'Amount should not exceed the outstanding invoice balance.',
            ])->withInput();
        }

        $methodCode = (string) $request->input('payment_method');
        if (!$workflow->resolvePaymentMethod($methodCode, (int) $student->school_id)) {
            return back()->withErrors([
                'payment_method' => 'This payment method is currently unavailable. Please contact the bursary.',
            ])->withInput();
        }

        $payment = $workflow->submitOfflinePayment(
            $student,
            $invoice,
            $request->validated(),
            $request->file('proof_file'),
            $user
        );

        return redirect()
            ->route('portal.payments.index', ['invoice_id' => $invoice->id])
            ->with('success', 'Payment proof submitted successfully. Reference: ' . $payment->payment_reference);
    }

    public function initiateOnline(
        InitiateOnlinePaymentRequest $request,
        PaymentWorkflowService $workflow,
        PaystackGatewayService $paystack,
        FlutterwaveGatewayService $flutterwave
    ) {
        [$user, $student, $school] = $this->studentContext();

        $invoice = $this->resolveStudentInvoice(
            (int) $request->integer('invoice_id'),
            (int) $student->id,
            (int) $school->id
        );
        $method = (string) $request->string('payment_method');
        $amount = (float) ($request->input('amount') ?: $invoice->balance);

        if ($amount <= 0 || $amount > ((float) $invoice->balance + 0.01)) {
            return back()->withErrors([
                'amount' => 'Amount should not exceed the outstanding invoice balance.',
            ])->withInput();
        }

        if (!$workflow->resolvePaymentMethod($method, (int) $school->id)) {
            return back()->withErrors([
                'payment_method' => 'Selected gateway is currently unavailable. Please contact the bursary.',
            ])->withInput();
        }

        $payment = $workflow->createOnlinePendingPayment(
            $student,
            $invoice,
            $method,
            $amount,
            ['initiated_from' => 'student_portal'],
            $user
        );

        if ($method === 'paystack') {
            $response = $paystack->initialize([
                'email' => (string) $user->email,
                'amount' => (int) round($amount * 100),
                'reference' => $payment->payment_reference,
                'callback_url' => route('portal.payments.paystack.callback'),
                'metadata' => [
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'student_id' => $student->id,
                ],
            ], (int) $school->id);

            if (!$response['ok']) {
                $payment->update([
                    'status' => Payment::STATUS_FAILED,
                    'gateway_response' => $response['raw'],
                ]);

                return back()->with('error', $this->gatewayFailureMessage($response, 'paystack', 'initialize'));
            }

            return redirect((string) ($response['data']['authorization_url'] ?? route('portal.payments.index')));
        }

        $response = $flutterwave->initialize([
            'tx_ref' => $payment->payment_reference,
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => 'NGN',
            'redirect_url' => route('portal.payments.flutterwave.callback'),
            'customer' => [
                'email' => (string) $user->email,
                'name' => (string) $student->full_name,
            ],
            'customizations' => [
                'title' => (string) ($school->name ?? config('app.name')),
                'description' => 'Invoice payment: ' . $invoice->invoice_number,
            ],
            'meta' => [
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'student_id' => $student->id,
            ],
        ], (int) $school->id);

        if (!$response['ok']) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'gateway_response' => $response['raw'],
            ]);

            return back()->with('error', $this->gatewayFailureMessage($response, 'flutterwave', 'initialize'));
        }

        return redirect((string) ($response['data']['link'] ?? route('portal.payments.index')));
    }

    public function paystackCallback(Request $request, PaystackGatewayService $paystack, PaymentWorkflowService $workflow)
    {
        [, $student] = $this->studentContext();

        $reference = (string) $request->query('reference', '');
        abort_if($reference === '', 422, 'Missing payment reference.');

        $payment = Payment::query()
            ->where('payment_reference', $reference)
            ->where('student_id', (int) $student->id)
            ->firstOrFail();

        $verification = $paystack->verify($reference, (int) $payment->school_id);

        if (!$verification['ok']) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'gateway_response' => $verification['raw'],
            ]);

            return redirect()->route('portal.payments.index')->with('error', $this->gatewayFailureMessage($verification, 'paystack', 'verify'));
        }

        $verifiedReference = (string) ($verification['data']['reference'] ?? '');
        if ($verifiedReference !== '' && $verifiedReference !== $reference) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'gateway_response' => $verification['raw'],
            ]);

            return redirect()->route('portal.payments.index')->with('error', 'Payment reference mismatch detected.');
        }

        $verifiedAmountKobo = (int) ($verification['data']['amount'] ?? 0);
        $expectedAmountKobo = (int) round((float) $payment->amount * 100);
        if ($verifiedAmountKobo > 0 && $verifiedAmountKobo !== $expectedAmountKobo) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'gateway_response' => $verification['raw'],
            ]);

            return redirect()->route('portal.payments.index')->with('error', 'Payment amount mismatch detected.');
        }

        $payment->update([
            'transaction_id' => (string) ($verification['data']['id'] ?? ''),
            'gateway_reference' => (string) ($verification['data']['reference'] ?? $reference),
            'gateway_response' => $verification['raw'],
            'payment_date' => now()->toDateString(),
        ]);

        $workflow->approve($payment, null, 'Paystack server-side verification', ['gateway' => 'paystack'], notifyStudent: true);

        return redirect()->route('portal.payments.index')->with('success', 'Payment successful. Receipt is now available.');
    }

    public function flutterwaveCallback(Request $request, FlutterwaveGatewayService $flutterwave, PaymentWorkflowService $workflow)
    {
        [, $student] = $this->studentContext();

        $txRef = (string) $request->query('tx_ref', '');
        $transactionId = (string) $request->query('transaction_id', '');

        abort_if($txRef === '', 422, 'Missing payment reference.');

        $payment = Payment::query()
            ->where('payment_reference', $txRef)
            ->where('student_id', (int) $student->id)
            ->firstOrFail();

        $verification = $transactionId !== ''
            ? $flutterwave->verifyTransaction($transactionId, (int) $payment->school_id)
            : ['ok' => false, 'raw' => ['message' => 'Missing transaction id'], 'data' => []];

        if (!$verification['ok']) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'gateway_response' => $verification['raw'],
            ]);

            return redirect()->route('portal.payments.index')->with('error', $this->gatewayFailureMessage($verification, 'flutterwave', 'verify'));
        }

        if ((string) ($verification['data']['tx_ref'] ?? '') !== $txRef) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'gateway_response' => $verification['raw'],
            ]);

            return redirect()->route('portal.payments.index')->with('error', 'Payment reference mismatch detected.');
        }

        $verifiedAmount = (float) ($verification['data']['amount'] ?? 0);
        $expectedAmount = (float) $payment->amount;
        if ($verifiedAmount > 0 && abs($verifiedAmount - $expectedAmount) > 0.01) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'gateway_response' => $verification['raw'],
            ]);

            return redirect()->route('portal.payments.index')->with('error', 'Payment amount mismatch detected.');
        }

        $payment->update([
            'transaction_id' => (string) ($verification['data']['id'] ?? $transactionId),
            'gateway_reference' => (string) ($verification['data']['flw_ref'] ?? $txRef),
            'gateway_response' => $verification['raw'],
            'payment_date' => now()->toDateString(),
        ]);

        $workflow->approve($payment, null, 'Flutterwave server-side verification', ['gateway' => 'flutterwave'], notifyStudent: true);

        return redirect()->route('portal.payments.index')->with('success', 'Payment successful. Receipt is now available.');
    }

    public function receipt(Receipt $receipt)
    {
        [, $student] = $this->studentContext();

        $receipt->loadMissing(['payment.invoice.session', 'payment.invoice.term', 'payment.student.schoolClass', 'payment.student.arm', 'payment.school']);

        abort_unless((int) $receipt->payment?->student_id === (int) $student->id, 403, 'Unauthorized receipt access.');
        abort_unless($receipt->payment?->isSuccessful(), 422, 'Receipt is only available for approved payments.');

        return view('portal.student.payments.receipt', [
            'receipt' => $receipt,
            'payment' => $receipt->payment,
            'student' => $student,
            'school' => $receipt->payment?->school,
        ]);
    }

    public function receiptPdf(Receipt $receipt, ReceiptService $receiptService)
    {
        [, $student] = $this->studentContext();

        $receipt->loadMissing(['payment.student']);
        abort_unless((int) $receipt->payment?->student_id === (int) $student->id, 403, 'Unauthorized receipt access.');
        abort_unless($receipt->payment?->isSuccessful(), 422, 'Receipt is only available for approved payments.');

        $pdf = $receiptService->receiptPdf($receipt->payment);

        return $pdf->download($receiptService->safeReceiptFilename($receipt->payment));
    }

    protected function buildPaymentSyncNotice($student, $school): ?array
    {
        $student->refresh()->loadMissing('schoolClass');

        if (!(int) $student->class_id) {
            return [
                'title' => 'Class assignment is missing',
                'message' => 'Your profile has no class yet, so tuition invoices cannot be generated. Please contact admin to assign your class.',
            ];
        }

        $session = $school->currentSession()
            ?: $school->sessions()->orderByDesc('is_current')->orderByDesc('id')->first();

        if (!$session) {
            return [
                'title' => 'Session setup is missing',
                'message' => 'No academic session is currently active. Your invoices will appear after the school sets a current session.',
            ];
        }

        $currentTerm = $session->terms()->where('is_current', true)->first();
        $classId = (int) $student->class_id;

        $baseFeeQuery = FeeStructure::query()
            ->where('school_id', (int) $student->school_id)
            ->where('is_active', true)
            ->where('session_id', (int) $session->id)
            ->where(function ($query) use ($classId) {
                $query->whereNull('class_id')
                    ->orWhere('class_id', $classId);
            });

        if (!(clone $baseFeeQuery)->exists()) {
            $className = (string) ($student->schoolClass?->name ?: 'your class');

            return [
                'title' => 'No fee structure found for your class',
                'message' => "No active fee structure is mapped to {$className} for session {$session->name}. Please contact admin.",
            ];
        }

        if ($currentTerm) {
            $hasCurrentTermFee = (clone $baseFeeQuery)
                ->where(function ($query) use ($currentTerm) {
                    $query->whereNull('term_id')
                        ->orWhere('term_id', (int) $currentTerm->id);
                })
                ->exists();

            if (!$hasCurrentTermFee) {
                $availableTerms = (clone $baseFeeQuery)
                    ->whereNotNull('term_id')
                    ->with('term:id,name')
                    ->get()
                    ->pluck('term.name')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                $hint = empty($availableTerms)
                    ? ''
                    : ' Available term fees: ' . implode(', ', $availableTerms) . '.';

                return [
                    'title' => 'No current-term fee is mapped',
                    'message' => "No active fee is mapped to {$currentTerm->name} for your class in session {$session->name}.{$hint}",
                ];
            }
        }

        return null;
    }

    protected function resolveStudentInvoice(int $invoiceId, int $studentId, int $schoolId): Invoice
    {
        return Invoice::query()
            ->with([
                'student.schoolClass',
                'student.arm',
                'session',
                'term',
                'items.feeStructure',
                'payments.receipt',
                'school',
            ])
            ->where('id', $invoiceId)
            ->where('student_id', $studentId)
            ->where('school_id', $schoolId)
            ->firstOrFail();
    }

    protected function safeInvoiceFilename(string $invoiceNumber): string
    {
        $token = preg_replace('/[^A-Za-z0-9._-]/', '-', $invoiceNumber) ?: 'invoice';

        return 'invoice-' . $token . '.pdf';
    }

    protected function gatewayFailureMessage(array $response, string $gateway, string $action): string
    {
        $raw = $response['raw'] ?? [];
        $message = is_array($raw) ? (string) ($raw['message'] ?? $raw['error'] ?? '') : '';
        $gatewayLabel = ucfirst($gateway);
        $actionLabel = $action === 'verify' ? 'verify payment' : 'start payment';

        if (str_contains(strtolower($message), 'ssl certificate problem')) {
            return "{$gatewayLabel} could not {$actionLabel} due to local SSL certificate configuration. Please contact admin to configure CA bundle or disable SSL verify for local development.";
        }

        if ($message !== '') {
            return "{$gatewayLabel} could not {$actionLabel}: {$message}";
        }

        return "{$gatewayLabel} could not {$actionLabel}. Please try again.";
    }
}
