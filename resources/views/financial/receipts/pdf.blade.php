<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $receipt->receipt_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #0f172a; padding: 24px; }
        .header { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 14px; }
        .header img { max-height: 60px; margin-bottom: 8px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .muted { color: #64748b; font-size: 10px; }
        .grid { width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden; margin-bottom: 12px; }
        .row { display: table; width: 100%; border-bottom: 1px solid #e2e8f0; }
        .row:last-child { border-bottom: none; }
        .label, .value { display: table-cell; padding: 8px 10px; vertical-align: top; }
        .label { width: 30%; background: #f8fafc; font-weight: 700; font-size: 10px; color: #334155; }
        .value { width: 70%; font-size: 11px; }
        .amount { font-size: 20px; font-weight: 800; color: #16a34a; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 10px; font-weight: 700; }
        .badge-ok { background: #dcfce7; color: #166534; }
        .footer { margin-top: 18px; display: table; width: 100%; }
        .left, .right { display: table-cell; width: 50%; vertical-align: bottom; }
        .right { text-align: right; }
        .sign { max-height: 60px; margin-bottom: 4px; }
        .line { margin-top: 26px; border-top: 1px solid #334155; width: 180px; display: inline-block; }
    </style>
</head>
<body>
    @php
        $school = $payment->school;
        $student = $payment->student;
        $invoice = $payment->invoice;
        $logoPath = $school?->logo ? public_path('storage/' . ltrim($school->logo, '/')) : null;
        $signaturePath = $signature?->signature_path ? storage_path('app/' . ltrim($signature->signature_path, '/')) : null;
        $signatureRoleLabel = $signature?->signature_role_label ?? 'Bursar';
    @endphp

    <div class="header">
        @if($logoPath && file_exists($logoPath))
            <img src="{{ $logoPath }}" alt="School Logo">
        @endif
        <h1>{{ $school?->name }}</h1>
        <div class="muted">
            {{ implode(', ', array_filter([$school?->address, $school?->city, $school?->state, $school?->country])) }}
            @if($school?->phone) | {{ $school->phone }} @endif
            @if($school?->email) | {{ $school->email }} @endif
        </div>
        <div style="margin-top:8px;font-size:12px;font-weight:700;">Official Payment Receipt</div>
    </div>

    <div class="grid">
        <div class="row"><div class="label">Receipt Number</div><div class="value">{{ $receipt->receipt_number }}</div></div>
        <div class="row"><div class="label">Invoice Number</div><div class="value">{{ $invoice?->invoice_number ?: 'N/A' }}</div></div>
        <div class="row"><div class="label">Student Name</div><div class="value">{{ $student?->full_name }}</div></div>
        <div class="row"><div class="label">Student ID</div><div class="value">{{ $student?->admission_number ?: $student?->registration_number ?: $student?->id }}</div></div>
        <div class="row"><div class="label">Class</div><div class="value">{{ $student?->schoolClass?->name }} {{ $student?->arm?->name }}</div></div>
        <div class="row"><div class="label">Session / Term</div><div class="value">{{ $invoice?->session?->name }} / {{ $invoice?->term?->name }}</div></div>
        <div class="row"><div class="label">Payment Method</div><div class="value">{{ ucwords(str_replace('_', ' ', (string) $payment->payment_method)) }}</div></div>
        <div class="row"><div class="label">Transaction Reference</div><div class="value">{{ $payment->gateway_reference ?: $payment->receipt_number ?: $payment->payment_reference }}</div></div>
        <div class="row"><div class="label">Date Paid</div><div class="value">{{ optional($payment->payment_date ?: $payment->paid_at)->format('d M Y') ?: '-' }}</div></div>
        <div class="row"><div class="label">Amount Paid</div><div class="value"><span class="amount">NGN {{ number_format((float) $payment->amount, 2) }}</span></div></div>
        <div class="row"><div class="label">Amount In Words</div><div class="value">{{ $amountInWords }}</div></div>
        <div class="row"><div class="label">Status</div><div class="value"><span class="badge badge-ok">{{ strtoupper((string) $payment->status) }}</span></div></div>
    </div>

    <div class="footer">
        <div class="left">
            <div class="muted">Generated: {{ now()->format('d M Y, h:i A') }}</div>
            <div class="line"></div>
            <div class="muted">{{ $signatureRoleLabel }} Office</div>
        </div>
        <div class="right">
            @if($signaturePath && file_exists($signaturePath))
                <img src="{{ $signaturePath }}" class="sign" alt="Signature">
            @endif
            <div class="line"></div>
            <div class="muted">{{ $signature?->name ?: ($signatureRoleLabel . ' Officer') }}{{ $signature?->title ? ' - ' . $signature->title : '' }}</div>
        </div>
    </div>
</body>
</html>
