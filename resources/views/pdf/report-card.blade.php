<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card – {{ $student->full_name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1e293b; background: #fff; padding: 24px; }

        /* ── Header ── */
        .header { text-align: center; border-bottom: 3px double #334155; padding-bottom: 12px; margin-bottom: 14px; }
        .header img { height: 54px; margin-bottom: 6px; }
        .header h1 { font-size: 18px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #1e293b; }
        .header .address { font-size: 10px; color: #64748b; margin-top: 2px; }
        .header .badge { display: inline-block; margin-top: 8px; border: 2px solid #4f46e5; border-radius: 999px; padding: 3px 16px; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #4f46e5; }

        /* ── Student info grid ── */
        .info-grid { display: table; width: 100%; margin-bottom: 14px; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; }
        .info-col { display: table-cell; width: 50%; padding: 10px 14px; vertical-align: top; }
        .info-col + .info-col { border-left: 1px solid #e2e8f0; }
        .info-row { display: flex; justify-content: space-between; padding: 3px 0; border-bottom: 1px dotted #e2e8f0; font-size: 10.5px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 700; color: #64748b; min-width: 90px; }
        .info-value { font-weight: 600; color: #1e293b; text-align: right; }

        /* ── Summary box ── */
        .summary-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center; }
        .summary-stat { text-align: center; }
        .summary-stat .val { font-size: 18px; font-weight: 800; color: #4f46e5; }
        .summary-stat .lbl { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 1px; }
        .summary-stat.green .val { color: #15803d; }
        .summary-stat.red .val { color: #dc2626; }

        /* ── Results table ── */
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        thead tr { background: #4f46e5; color: #fff; }
        thead th { padding: 7px 6px; text-align: center; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        thead th:first-child { text-align: left; padding-left: 10px; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 6px 6px; font-size: 10.5px; border-bottom: 1px solid #e2e8f0; text-align: center; }
        tbody td:first-child { text-align: left; padding-left: 10px; font-weight: 600; }
        tfoot td { padding: 6px 6px; font-size: 10.5px; font-weight: 700; text-align: center; background: #f1f5f9; border-top: 2px solid #cbd5e1; }
        tfoot td:first-child { text-align: left; padding-left: 10px; }

        /* ── Grade badges ── */
        .grade { display: inline-block; padding: 1px 7px; border-radius: 999px; font-weight: 800; font-size: 10px; }
        .grade-A { background: #d1fae5; color: #065f46; }
        .grade-B { background: #dbeafe; color: #1e40af; }
        .grade-C { background: #e0e7ff; color: #3730a3; }
        .grade-D { background: #fef3c7; color: #92400e; }
        .grade-E { background: #fee2e2; color: #991b1b; }

        /* ── Remarks ── */
        .remarks-grid { display: table; width: 100%; margin-bottom: 14px; gap: 10px; }
        .remark-box { display: table-cell; width: 50%; border: 1px solid #e2e8f0; border-radius: 6px; padding: 9px 12px; vertical-align: top; }
        .remark-box + .remark-box { border-left: none; }
        .remark-label { font-size: 9px; font-weight: 700; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px; margin-bottom: 5px; }
        .remark-text { font-size: 10.5px; color: #1e293b; min-height: 28px; }

        /* ── Footer ── */
        .footer-row { display: table; width: 100%; margin-top: 14px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .footer-cell { display: table-cell; width: 50%; vertical-align: top; }
        .sig-label { font-size: 9px; color: #94a3b8; text-transform: uppercase; margin-bottom: 20px; }
        .sig-line { border-top: 1px solid #334155; width: 140px; padding-top: 3px; font-size: 9px; color: #64748b; }

        /* ── Grade key ── */
        .grade-key { font-size: 9px; color: #64748b; text-align: center; margin-top: 10px; padding-top: 8px; border-top: 1px dashed #e2e8f0; }

        /* ── Promoted ── */
        .promoted-row { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 7px 12px; margin-bottom: 14px; font-size: 10.5px; color: #166534; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        @if($school->logo)
        <img src="{{ public_path('storage/' . ltrim($school->logo, '/')) }}" alt="">
        @endif
        <h1>{{ $school->name }}</h1>
        <div class="address">
            {{ implode(', ', array_filter([$school->address ?? null, $school->city ?? null, $school->state ?? null])) }}
            @if($school->phone) &nbsp;·&nbsp; Phone: {{ $school->phone }} @endif
            @if($school->email) &nbsp;·&nbsp; {{ $school->email }} @endif
        </div>
        <div class="badge">Terminal Report Card</div>
    </div>

    {{-- Student Info --}}
    <div class="info-grid">
        <div class="info-col">
            <div class="info-row"><span class="info-label">Full Name</span><span class="info-value">{{ $student->full_name }}</span></div>
            <div class="info-row"><span class="info-label">Reg No</span><span class="info-value">{{ $student->registration_number ?? $student->admission_number }}</span></div>
            <div class="info-row"><span class="info-label">Class</span><span class="info-value">{{ $student->schoolClass?->name }}{{ $student->arm ? ' '.$student->arm->name : '' }}</span></div>
            <div class="info-row"><span class="info-label">Gender</span><span class="info-value">{{ ucfirst($student->gender) }}</span></div>
        </div>
        <div class="info-col">
            <div class="info-row"><span class="info-label">Academic Year</span><span class="info-value">{{ $term?->session?->name ?? '—' }}</span></div>
            <div class="info-row"><span class="info-label">Exam</span><span class="info-value">{{ $term?->name ?? '—' }}</span></div>
            <div class="info-row">
                <span class="info-label">Position in Class</span>
                <span class="info-value">
                    @if($reportCard->position_in_class)
                        @php
                            $pos = $reportCard->position_in_class;
                            $suffix = match(true) {
                                $pos % 100 >= 11 && $pos % 100 <= 13 => 'th',
                                $pos % 10 === 1 => 'st', $pos % 10 === 2 => 'nd', $pos % 10 === 3 => 'rd', default => 'th',
                            };
                        @endphp
                        {{ $pos }}{{ $suffix }}
                    @else —
                    @endif
                </span>
            </div>
            <div class="info-row"><span class="info-label">Attendance</span><span class="info-value">{{ $reportCard->attendance_summary ?? '—' }}</span></div>
        </div>
    </div>

    {{-- Summary --}}
    <div class="summary-box">
        <div class="summary-stat">
            <div class="val">{{ number_format($reportCard->average_score, 2) }}</div>
            <div class="lbl">Mark Average</div>
        </div>
        <div class="summary-stat">
            <div class="val">{{ $reportCard->position_in_class ?? '—' }}/{{ $reportCard->class_size }}</div>
            <div class="lbl">Class Position</div>
        </div>
        <div class="summary-stat">
            <div class="val">{{ $reportCard->total_subjects }}</div>
            <div class="lbl">Subjects</div>
        </div>
        <div class="summary-stat green">
            <div class="val">{{ $reportCard->subjects_passed }}</div>
            <div class="lbl">Passed</div>
        </div>
        <div class="summary-stat red">
            <div class="val">{{ $reportCard->subjects_failed }}</div>
            <div class="lbl">Failed</div>
        </div>
        <div class="summary-stat">
            <div class="val">{{ $reportCard->total_score }}</div>
            <div class="lbl">Total Score</div>
        </div>
    </div>

    {{-- Results Table --}}
    <table>
        <thead>
            <tr>
                <th style="text-align:left; padding-left:10px;">Subjects</th>
                <th>Exam</th>
                <th>First Test</th>
                <th>Second Test</th>
                <th>Total</th>
                <th>Grade</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results->sortBy('subject.name') as $result)
            @php
                $gradeInfo = \App\Models\Result::calculateGrade((float)$result->total_score);
            @endphp
            <tr>
                <td>{{ $result->subject?->name }}</td>
                <td>{{ $result->exam_score ?? '—' }}</td>
                <td>{{ $result->ca1_score ?? '—' }}</td>
                <td>{{ $result->ca2_score ?? '—' }}</td>
                <td><strong>{{ $result->total_score }}</strong></td>
                <td><span class="grade grade-{{ $result->grade }}">{{ $result->grade ?? '—' }}</span></td>
                <td>{{ $gradeInfo['remark'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total</td>
                <td>{{ $reportCard->total_score }}</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="4" style="font-weight:400; font-size:9.5px; color:#64748b;">Mark Average</td>
                <td style="color:#4f46e5;">{{ number_format($reportCard->average_score, 2) }}</td>
                <td colspan="2" style="font-size:9.5px; color:#94a3b8; text-align:right;">Class Average: {{ number_format($reportCard->average_score, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Promoted To --}}
    @if($reportCard->promoted_to ?? null)
    <div class="promoted-row">Promoted To: <strong>{{ $reportCard->promoted_to }}</strong></div>
    @endif

    {{-- Remarks --}}
    <div class="remarks-grid">
        <div class="remark-box">
            <div class="remark-label">Class Teacher Remarks</div>
            <div class="remark-text">{{ $reportCard->class_teacher_remark ?: '________________________________' }}</div>
        </div>
        <div class="remark-box" style="border-left:1px solid #e2e8f0;">
            <div class="remark-label">Principal Remarks</div>
            <div class="remark-text">{{ $reportCard->principal_remark ?: 'N/A' }}</div>
        </div>
    </div>

    {{-- Signature + Next Term --}}
    <div class="footer-row">
        <div class="footer-cell">
            <div class="sig-label">Principal Signature</div>
            <div class="sig-line">Date: {{ now()->format('d/m/Y') }}</div>
        </div>
        <div class="footer-cell" style="text-align:right;">
            @if($reportCard->next_term_begins)
            <div style="font-size:10px; color:#475569;">
                <strong>Next Term Begins:</strong> {{ \Carbon\Carbon::parse($reportCard->next_term_begins)->format('d M Y') }}
            </div>
            @endif
            @if($reportCard->next_term_fees)
            <div style="font-size:10px; color:#475569; margin-top:4px;">
                <strong>Next Term Fees:</strong> ₦{{ number_format($reportCard->next_term_fees, 0) }}
            </div>
            @endif
        </div>
    </div>

    {{-- Grade Key --}}
    <div class="grade-key">
        Interpretation of Grades: &nbsp;
        70–100 = 5.0 [A] Excellent &nbsp;|&nbsp;
        60–69 = 4.5 [B] Very Good &nbsp;|&nbsp;
        50–59 = 4.0 [C] Good &nbsp;|&nbsp;
        40–49 = 3.5 [D] Pass &nbsp;|&nbsp;
        0–39 = 3.0 [E] Fail
    </div>

</body>
</html>
