<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bulk Result Sheets</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #0f172a; margin: 0; padding: 16px; }
        .sheet { page-break-after: always; }
        .sheet:last-child { page-break-after: auto; }
        .header { text-align: center; border-bottom: 1px solid #334155; padding-bottom: 8px; margin-bottom: 10px; }
        .logo { height: 42px; margin-bottom: 4px; }
        .school { font-size: 16px; font-weight: 700; }
        .meta { font-size: 10px; color: #475569; margin-top: 3px; }
        .student-line { margin: 8px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #cbd5e1; padding: 4px; }
        th { background: #e2e8f0; font-size: 9px; text-transform: uppercase; }
        td { font-size: 9px; }
        .num { text-align: center; }
        .summary { margin-top: 6px; font-size: 9px; }
        .remarks { margin-top: 6px; }
        .remark-row { margin-top: 3px; }
        .remark-label { font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase; }
        .remark-box { border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px; font-size: 9px; min-height: 22px; }
        .legend { margin-top: 8px; font-size: 8px; border-top: 1px dashed #cbd5e1; padding-top: 5px; color: #475569; }
    </style>
</head>
<body>
@foreach($sheets as $sheet)
    <div class="sheet">
        <div class="header">
            @if($school?->logo)
                <img src="{{ public_path('storage/' . ltrim($school->logo, '/')) }}" class="logo" alt="Logo">
            @endif
            <div class="school">{{ $school?->name }}</div>
            <div class="meta">{{ implode(', ', array_filter([$school?->address, $school?->city, $school?->state])) }}</div>
        </div>

        <div class="student-line">
            <strong>{{ $sheet->student?->full_name }}</strong>
            | Reg: {{ $sheet->student?->registration_number ?? $sheet->student?->admission_number }}
            | Class: {{ $sheet->schoolClass?->name }}{{ $sheet->arm ? ' '.$sheet->arm->name : '' }}
            | Session: {{ $sheet->session?->name }}
            | Term: {{ $sheet->term?->name }}
            | Exam: {{ $sheet->examType?->name }}
        </div>

        <table>
            <thead>
            <tr>
                <th style="text-align:left;">Subject</th>
                <th>Exam</th>
                <th>First Test</th>
                <th>Second Test</th>
                <th>Total</th>
                <th>Grade</th>
                <th>Remark</th>
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

        <div class="summary">
            Total: {{ number_format((float)$sheet->total_score, 2) }} |
            Average: {{ number_format((float)$sheet->average_score, 2) }} |
            Class Average: {{ $sheet->class_average !== null ? number_format((float)$sheet->class_average, 2) : '—' }} |
            Position: {{ $sheet->class_position ?? '—' }} |
            Attendance: {{ $sheet->attendance_summary ?? '—' }} |
            Promoted To: {{ $sheet->promotedToClass?->name ?? '—' }}
        </div>

        @php
            $remarkBlocks = [
                ['label' => 'Class Teacher Remarks', 'value' => $sheet->class_teacher_remark],
                ['label' => 'Principal Remarks', 'value' => $sheet->principal_remark],
                ['label' => 'Vice Principal Remarks', 'value' => $sheet->vice_principal_remark],
            ];
            $visibleRemarkBlocks = array_values(array_filter($remarkBlocks, fn ($block) => trim((string) ($block['value'] ?? '')) !== ''));
        @endphp

        @if(count($visibleRemarkBlocks) > 0)
            <div class="remarks">
                @foreach($visibleRemarkBlocks as $block)
                    <div class="remark-row">
                        <div class="remark-label">{{ $block['label'] }}</div>
                        <div class="remark-box">{{ $block['value'] }}</div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="legend">
            @foreach($interpretation as $index => $grade)
                {{ $grade['min'] }}-{{ $grade['max'] }} = {{ $grade['grade'] }}{{ $grade['remark'] ? ' ('.$grade['remark'].')' : '' }}@if($index < count($interpretation)-1) | @endif
            @endforeach
        </div>
    </div>
@endforeach
</body>
</html>
