<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['student', 'invoice'])->latest()->paginate(25);
        return view('financial.payments.index', compact('payments'));
    }

    public function recordManual(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,bank_transfer,pos',
            'bank_name' => 'nullable|string',
            'account_name' => 'nullable|string',
            'receipt_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        $payment = Payment::create([
            'school_id' => auth()->user()->school_id,
            'invoice_id' => $invoice->id,
            'student_id' => $invoice->student_id,
            'payment_reference' => Payment::generateReference(),
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'status' => 'confirmed',
            'paid_at' => now(),
            'confirmed_by' => auth()->id(),
            'bank_name' => $validated['bank_name'] ?? null,
            'account_name' => $validated['account_name'] ?? null,
            'receipt_number' => $validated['receipt_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $invoice->updateBalance();

        return back()->with('success', 'Payment recorded. Balance: ' . number_format($invoice->balance, 2));
    }

    // ── Paystack Integration ───────────────────────────────
    public function initiatePaystack(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:100',
            'email' => 'required|email',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);
        $reference = Payment::generateReference();

        $response = Http::withToken(config('services.paystack.secret'))
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => $request->email,
                'amount' => $request->amount * 100, // Paystack uses kobo
                'reference' => $reference,
                'callback_url' => route('financial.payments.paystack.callback'),
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'student_id' => $invoice->student_id,
                ],
            ]);

        if ($response->successful()) {
            Payment::create([
                'school_id' => auth()->user()->school_id,
                'invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'payment_reference' => $reference,
                'amount' => $request->amount,
                'payment_method' => 'paystack',
                'payment_gateway' => 'paystack',
                'status' => 'pending',
            ]);

            return redirect($response->json('data.authorization_url'));
        }

        return back()->with('error', 'Payment initialization failed.');
    }

    public function paystackCallback(Request $request)
    {
        $reference = $request->get('reference');
        $response = Http::withToken(config('services.paystack.secret'))
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        if ($response->successful() && $response->json('data.status') === 'success') {
            $payment = Payment::where('payment_reference', $reference)->first();
            if ($payment) {
                $payment->update([
                    'status' => 'confirmed',
                    'transaction_id' => $response->json('data.id'),
                    'gateway_response' => $response->json('data'),
                    'paid_at' => now(),
                ]);
                $payment->invoice->updateBalance();
            }
            return redirect()->route('financial.invoices.show', $payment->invoice_id)
                ->with('success', 'Payment successful!');
        }

        return redirect()->route('financial.invoices.index')->with('error', 'Payment verification failed.');
    }
}
