<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminal Result Sheet - {{ $sheet->student?->full_name }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #0f172a; margin: 0; padding: 20px; background: #fff; }
        .top-actions { margin-bottom: 12px; }
        .btn { display: inline-block; margin-right: 8px; padding: 7px 12px; border-radius: 6px; text-decoration: none; border: 1px solid #cbd5e1; color: #334155; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #334155; padding-bottom: 10px; margin-bottom: 14px; }
        .logo { height: 60px; margin-bottom: 6px; }
        .school-name { font-size: 20px; font-weight: 700; margin-bottom: 3px; }
        .school-address { font-size: 11px; color: #475569; }
        .meta { margin-top: 8px; font-size: 12px; font-weight: 600; }
        .info-grid { width: 100%; margin-bottom: 14px; border-collapse: collapse; }
        .info-grid td { width: 50%; border: 1px solid #cbd5e1; padding: 8px; vertical-align: top; }
        .info-row { margin: 3px 0; }
        .label { font-size: 10px; color: #64748b; display: inline-block; min-width: 110px; }
        .value { font-weight: 600; }
        .results-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .results-table th, .results-table td { border: 1px solid #cbd5e1; padding: 6px; }
        .results-table th { background: #e2e8f0; font-size: 11px; text-transform: uppercase; }
        .results-table td { font-size: 11px; }
        .results-table td.num { text-align: center; }
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .summary-table td { border: 1px solid #cbd5e1; padding: 6px; font-size: 11px; }
        .summary-table td:first-child { width: 230px; font-weight: 700; background: #f8fafc; }
        .remarks { margin-top: 10px; }
        .remarks-box { border: 1px solid #cbd5e1; padding: 8px; min-height: 42px; margin-bottom: 8px; }
        .grade-legend { margin-top: 8px; border-top: 1px dashed #cbd5e1; padding-top: 8px; font-size: 10px; color: #475569; }
    </style>
</head>
<body>
@if(!($asPdf ?? false))
    <div class="top-actions">
        <a class="btn" href="{{ route('examination.result-sheets.class-sheet') }}">Back to Class Sheet</a>
        <a class="btn" href="{{ route('examination.result-sheets.student.pdf', $sheet) }}">Download PDF</a>
    </div>
@endif

<div class="header">
    @if($school?->logo)
        @php
            $logo = ($asPdf ?? false)
                ? public_path('storage/' . ltrim($school->logo, '/'))
                : asset('storage/' . ltrim($school->logo, '/'));
        @endphp
        <img src="{{ $logo }}" alt="School Logo" class="logo">
    @endif
    <div class="school-name">{{ $school?->name }}</div>
    <div class="school-address">
        {{ implode(', ', array_filter([$school?->address, $school?->city, $school?->state])) }}
        @if($school?->phone) | {{ $school->phone }} @endif
        @if($school?->email) | {{ $school->email }} @endif
    </div>
    <div class="meta">
        TERMINAL RESULT SHEET - {{ strtoupper($sheet->examType?->name ?? 'EXAM') }}
    </div>
</div>

@php
    $position = (int) ($sheet->class_position ?? 0);
    $suffix = 'th';
    if ($position % 100 < 11 || $position % 100 > 13) {
        $suffix = match($position % 10) {1 => 'st', 2 => 'nd', 3 => 'rd', default => 'th'};
    }
@endphp

<table class="info-grid">
    <tr>
        <td>
            <div class="info-row"><span class="label">Student Name:</span> <span class="value">{{ $sheet->student?->full_name }}</span></div>
            <div class="info-row"><span class="label">Registration No:</span> <span class="value">{{ $sheet->student?->registration_number ?? $sheet->student?->admission_number }}</span></div>
            <div class="info-row"><span class="label">Class:</span> <span class="value">{{ $sheet->schoolClass?->name }}{{ $sheet->arm ? ' '.$sheet->arm->name : '' }}</span></div>
            <div class="info-row"><span class="label">Section:</span> <span class="value">{{ $sheet->section ?: ($sheet->schoolClass?->section ?? '—') }}</span></div>
            <div class="info-row"><span class="label">Gender:</span> <span class="value">{{ ucfirst($sheet->student?->gender ?? '—') }}</span></div>
        </td>
        <td>
            <div class="info-row"><span class="label">Academic Year:</span> <span class="value">{{ $sheet->session?->name }}</span></div>
            <div class="info-row"><span class="label">Exam Term:</span> <span class="value">{{ $sheet->term?->name }}</span></div>
            <div class="info-row"><span class="label">Position in Class:</span> <span class="value">{{ $position > 0 ? $position.$suffix : '—' }}</span></div>
            <div class="info-row"><span class="label">Attendance:</span> <span class="value">{{ $sheet->attendance_summary ?? '—' }}</span></div>
            <div class="info-row"><span class="label">Promoted To:</span> <span class="value">{{ $sheet->promotedToClass?->name ?? '—' }}</span></div>
        </td>
    </tr>
</table>

<table class="results-table">
    <thead>
        <tr>
            <th style="text-align: left;">Subjects</th>
            <th>Exam</th>
            <th>First Test</th>
            <th>Second Test</th>
            <th>Total</th>
            <th>Grade</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
    @foreach($sheet->items->sortBy('subject.name') as $item)
        <tr>
            <td>{{ $item->subject?->name }}</td>
            <td class="num">{{ number_format((float)$item->exam_score, 2) }}</td>
            <td class="num">{{ number_format((float)$item->first_test_score, 2) }}</td>
            <td class="num">{{ number_format((float)$item->second_test_score, 2) }}</td>
            <td class="num">{{ number_format((float)$item->total_score, 2) }}</td>
            <td class="num">{{ $item->grade }}</td>
            <td>{{ $item->remark }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table class="summary-table">
    <tr><td>Total Score</td><td>{{ number_format((float)$sheet->total_score, 2) }}</td></tr>
    <tr><td>Mark Average</td><td>{{ number_format((float)$sheet->average_score, 2) }}</td></tr>
    <tr><td>Class Average</td><td>{{ $sheet->class_average !== null ? number_format((float)$sheet->class_average, 2) : '—' }}</td></tr>
    <tr><td>Promoted To</td><td>{{ $sheet->promotedToClass?->name ?? '—' }}</td></tr>
    <tr><td>Attendance</td><td>{{ $sheet->attendance_summary ?? '—' }}</td></tr>
</table>

<div class="remarks">
    @php
        $respectVisibility = (bool) ($respectVisibility ?? false);
        $remarkBlocks = [
            [
                'label' => 'Class Teacher Remarks',
                'active' => (bool) ($sheet->class_teacher_remark_active ?? true),
                'value' => $sheet->class_teacher_remark,
            ],
            [
                'label' => 'Principal Remarks',
                'active' => (bool) ($sheet->principal_remark_active ?? true),
                'value' => $sheet->principal_remark,
            ],
            [
                'label' => 'Vice Principal Remarks',
                'active' => (bool) ($sheet->vice_principal_remark_active ?? true),
                'value' => $sheet->vice_principal_remark,
            ],
        ];
        $visibleRemarkBlocks = array_values(array_filter($remarkBlocks, function ($block) use ($respectVisibility) {
            if ($respectVisibility) {
                return (bool) $block['active'];
            }

            return trim((string) ($block['value'] ?? '')) !== '';
        }));
    @endphp

    @if(count($visibleRemarkBlocks) > 0)
        @foreach($visibleRemarkBlocks as $block)
            <div><strong>{{ $block['label'] }}</strong></div>
            <div class="remarks-box">{{ $block['value'] ?: '—' }}</div>
        @endforeach
    @else
        <div><strong>Comments</strong></div>
        <div class="remarks-box">Comments are currently unavailable.</div>
    @endif
    <div class="info-row"><span class="label">Principal Signature:</span> <span class="value">{{ $sheet->principal_signature ?: '—' }}</span></div>
    <div class="info-row"><span class="label">Date:</span> <span class="value">{{ $sheet->signed_at?->format('d M Y') ?? '—' }}</span></div>
</div>

<div class="grade-legend">
    <strong>Grade Interpretation:</strong>
    @foreach($interpretation as $index => $grade)
        {{ $grade['min'] }}-{{ $grade['max'] }} = {{ $grade['grade'] }}{{ $grade['remark'] ? ' ('.$grade['remark'].')' : '' }}@if($index < count($interpretation)-1) | @endif
    @endforeach
</div>

</body>
</html>
