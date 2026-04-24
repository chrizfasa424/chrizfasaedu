<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payments\Gateway\FlutterwaveGatewayService;
use App\Services\Payments\Gateway\PaystackGatewayService;
use App\Services\Payments\PaymentWorkflowService;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function paystack(Request $request, PaystackGatewayService $paystack, PaymentWorkflowService $workflow)
    {
        $payload = (string) $request->getContent();
        $signature = (string) $request->header('x-paystack-signature');
        $reference = (string) $request->input('data.reference', '');
        $schoolId = $reference !== ''
            ? Payment::query()->where('payment_reference', $reference)->value('school_id')
            : null;

        if (!$paystack->validWebhookSignature($payload, $signature, $schoolId ? (int) $schoolId : null)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $event = (string) $request->input('event');
        if ($event !== 'charge.success') {
            return response()->json(['status' => 'ignored']);
        }

        if ($reference === '') {
            return response()->json(['status' => 'ignored']);
        }

        $payment = Payment::query()->where('payment_reference', $reference)->first();
        if (!$payment) {
            return response()->json(['status' => 'not_found']);
        }

        if ($payment->isSuccessful()) {
            return response()->json(['status' => 'already_processed']);
        }

        $verification = $paystack->verify($reference, (int) $payment->school_id);
        if (!$verification['ok']) {
            return response()->json(['status' => 'failed_verification']);
        }

        $verifiedReference = (string) ($verification['data']['reference'] ?? '');
        if ($verifiedReference !== '' && $verifiedReference !== $reference) {
            return response()->json(['status' => 'reference_mismatch'], 422);
        }

        $verifiedAmountKobo = (int) ($verification['data']['amount'] ?? 0);
        $expectedAmountKobo = (int) round((float) $payment->amount * 100);
        if ($verifiedAmountKobo > 0 && $verifiedAmountKobo !== $expectedAmountKobo) {
            return response()->json(['status' => 'amount_mismatch'], 422);
        }

        $payment->update([
            'transaction_id' => (string) ($verification['data']['id'] ?? $request->input('data.id', '')),
            'gateway_reference' => (string) ($verification['data']['reference'] ?? $request->input('data.reference', $reference)),
            'gateway_response' => $verification['raw'],
            'payment_date' => now()->toDateString(),
        ]);

        $workflow->approve($payment, null, 'Paystack webhook verification', ['gateway' => 'paystack-webhook'], notifyStudent: true);

        return response()->json(['status' => 'ok']);
    }

    public function flutterwave(Request $request, FlutterwaveGatewayService $flutterwave, PaymentWorkflowService $workflow)
    {
        $txRef = (string) $request->input('data.tx_ref', '');
        $schoolId = $txRef !== ''
            ? Payment::query()->where('payment_reference', $txRef)->value('school_id')
            : null;

        $signature = $request->header('verif-hash');
        if (!$flutterwave->validWebhookSignature($signature, $schoolId ? (int) $schoolId : null)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $status = strtolower((string) $request->input('status', ''));
        if (!in_array($status, ['successful', 'completed'], true)) {
            return response()->json(['status' => 'ignored']);
        }

        $transactionId = (string) $request->input('data.id', '');

        if ($txRef === '') {
            return response()->json(['status' => 'ignored']);
        }

        $payment = Payment::query()->where('payment_reference', $txRef)->first();
        if (!$payment) {
            return response()->json(['status' => 'not_found']);
        }

        if ($payment->isSuccessful()) {
            return response()->json(['status' => 'already_processed']);
        }

        $verification = $transactionId !== ''
            ? $flutterwave->verifyTransaction($transactionId, (int) $payment->school_id)
            : ['ok' => false, 'raw' => ['message' => 'Missing transaction id'], 'data' => []];

        if (!$verification['ok']) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'gateway_response' => $verification['raw'],
            ]);

            return response()->json(['status' => 'failed_verification']);
        }

        if ((string) ($verification['data']['tx_ref'] ?? '') !== $txRef) {
            return response()->json(['status' => 'reference_mismatch'], 422);
        }

        $verifiedAmount = (float) ($verification['data']['amount'] ?? 0);
        $expectedAmount = (float) $payment->amount;
        if ($verifiedAmount > 0 && abs($verifiedAmount - $expectedAmount) > 0.01) {
            return response()->json(['status' => 'amount_mismatch'], 422);
        }

        $payment->update([
            'transaction_id' => (string) ($verification['data']['id'] ?? $transactionId),
            'gateway_reference' => (string) ($verification['data']['flw_ref'] ?? $txRef),
            'gateway_response' => $verification['raw'],
            'payment_date' => now()->toDateString(),
        ]);

        $workflow->approve($payment, null, 'Flutterwave webhook verification', ['gateway' => 'flutterwave-webhook'], notifyStudent: true);

        return response()->json(['status' => 'ok']);
    }
}
