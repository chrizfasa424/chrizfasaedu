<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px; font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #0f172a; }
        .wrap { max-width: 900px; margin: 0 auto; }
        .actions { margin-bottom: 12px; text-align: right; }
        .btn { display: inline-block; margin-left: 6px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 7px 12px; font-size: 12px; font-weight: 600; text-decoration: none; color: #0f172a; }
        .btn-primary { border-color: #4f46e5; background: #4f46e5; color: #fff; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #1e293b; }
        .brand h1 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .muted { color: #64748b; font-size: 11px; }
        .logo { max-height: 64px; max-width: 64px; object-fit: contain; }
        .pill { display: inline-block; border-radius: 999px; padding: 4px 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .pill-ok { background: #dcfce7; color: #166534; }
        .pill-pending { background: #fef3c7; color: #92400e; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
        .card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; }
        .card h3 { margin: 0 0 8px; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: #64748b; }
        .row { display: flex; justify-content: space-between; gap: 10px; padding: 4px 0; border-bottom: 1px dashed #e2e8f0; }
        .row:last-child { border-bottom: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px; text-align: left; }
        th { background: #f8fafc; font-size: 11px; text-transform: uppercase; color: #475569; }
        td.num, th.num { text-align: right; }
        tfoot td { font-weight: 700; background: #f8fafc; }
        .footer { margin-top: 14px; color: #64748b; font-size: 11px; }
        @media print {
            .actions { display: none; }
            body { padding: 0; }
            .wrap { max-width: 100%; }
        }
    </style>
</head>
<body>
@php
    $school = $invoice->school;
    $student = $invoice->student;
    $status = strtolower((string) ($invoice->status?->value ?? $invoice->status));
    $logoPath = $school?->logo ? public_path('storage/' . ltrim($school->logo, '/')) : null;
@endphp
<div class="wrap">
    <div class="actions">
        @if(!$asPdf)
            <a class="btn" href="{{ route('portal.invoices.show', $invoice) }}">Back</a>
            <a class="btn" href="{{ route('portal.invoices.pdf', $invoice) }}">Download PDF</a>
            <button type="button" class="btn btn-primary" onclick="window.print()">Print</button>
        @endif
    </div>

    <div class="header">
        <div class="brand">
            <h1>{{ $school?->name ?: config('app.name') }}</h1>
            <div class="muted">{{ implode(', ', array_filter([$school?->address, $school?->city, $school?->state, $school?->country])) }}</div>
            <div class="muted">
                @if($school?->phone) {{ $school->phone }} @endif
                @if($school?->email) | {{ $school->email }} @endif
            </div>
        </div>
        <div>
            @if($logoPath && file_exists($logoPath))
                <img class="logo" src="{{ $logoPath }}" alt="School Logo">
            @endif
            <div style="margin-top:8px;">
                <span class="pill {{ in_array($status, ['paid']) ? 'pill-ok' : 'pill-pending' }}">{{ strtoupper($status) }}</span>
            </div>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <h3>Invoice Details</h3>
            <div class="row"><span>Invoice Number</span><strong>{{ $invoice->invoice_number }}</strong></div>
            <div class="row"><span>Session / Term</span><strong>{{ $invoice->session?->name ?: 'N/A' }} / {{ $invoice->term?->name ?: 'N/A' }}</strong></div>
            <div class="row"><span>Due Date</span><strong>{{ $invoice->due_date?->format('d M Y') ?: 'N/A' }}</strong></div>
            <div class="row"><span>Generated Date</span><strong>{{ $invoice->created_at?->format('d M Y') ?: 'N/A' }}</strong></div>
        </div>
        <div class="card">
            <h3>Student Details</h3>
            <div class="row"><span>Name</span><strong>{{ $student?->full_name ?: 'N/A' }}</strong></div>
            <div class="row"><span>Student ID</span><strong>{{ $student?->admission_number ?: $student?->registration_number ?: ('STD-' . $invoice->student_id) }}</strong></div>
            <div class="row"><span>Class</span><strong>{{ $student?->schoolClass?->name ?: 'N/A' }} {{ $student?->arm?->name ?: '' }}</strong></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="num">Amount</th>
                <th class="num">Discount</th>
                <th class="num">Net</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="num">NGN {{ number_format((float) $item->amount, 2) }}</td>
                    <td class="num">NGN {{ number_format((float) $item->discount, 2) }}</td>
                    <td class="num">NGN {{ number_format((float) $item->net_amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No invoice items available.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td>Total Amount</td>
                <td class="num" colspan="3">NGN {{ number_format((float) $invoice->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Amount Paid</td>
                <td class="num" colspan="3">NGN {{ number_format((float) $invoice->amount_paid, 2) }}</td>
            </tr>
            <tr>
                <td>Balance</td>
                <td class="num" colspan="3">NGN {{ number_format((float) $invoice->balance, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">Generated on {{ now()->format('d M Y, h:i A') }}.</div>
</div>
</body>
</html>
