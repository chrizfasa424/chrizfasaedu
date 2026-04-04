<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $student->full_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px double #333; padding-bottom: 15px; }
        .header h1 { font-size: 20px; margin: 0; color: #1a5632; }
        .header p { margin: 3px 0; color: #555; }
        .student-info { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .info-group { width: 48%; }
        .info-row { display: flex; justify-content: space-between; padding: 3px 0; border-bottom: 1px dotted #ddd; }
        .info-label { font-weight: bold; color: #555; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background: #1a5632; color: white; padding: 8px 5px; text-align: left; font-size: 11px; }
        td { border: 1px solid #ddd; padding: 6px 5px; font-size: 11px; }
        tr:nth-child(even) { background: #f9f9f9; }
        .grade-A1, .grade-B2, .grade-B3 { color: #15803d; font-weight: bold; }
        .grade-C4, .grade-C5, .grade-C6 { color: #1d4ed8; }
        .grade-D7, .grade-E8 { color: #d97706; }
        .grade-F9 { color: #dc2626; font-weight: bold; }
        .summary { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 15px; margin: 15px 0; }
        .remarks { margin-top: 20px; }
        .remark-box { border: 1px solid #ddd; border-radius: 5px; padding: 10px; margin: 8px 0; }
        .remark-label { font-weight: bold; font-size: 11px; color: #555; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 2px solid #333; font-size: 10px; color: #777; }
        .position { font-size: 18px; font-weight: bold; color: #1a5632; }
    </style>
</head>
<body>
    <div class="header">
        @if($school->logo)
        <img src="{{ public_path('storage/' . $school->logo) }}" height="60" style="margin-bottom: 5px;">
        @endif
        <h1>{{ strtoupper($school->name) }}</h1>
        <p>{{ $school->address }}, {{ $school->city }}, {{ $school->state }}</p>
        <p>Tel: {{ $school->phone }} | Email: {{ $school->email }}</p>
        @if($school->motto)<p><em>"{{ $school->motto }}"</em></p>@endif
        <h2 style="margin-top: 10px; font-size: 16px; color: #333;">STUDENT REPORT CARD</h2>
    </div>

    <div style="display: flex; gap: 20px; margin-bottom: 15px;">
        <div style="width: 50%;">
            <div class="info-row"><span class="info-label">Name:</span> <span>{{ $student->full_name }}</span></div>
            <div class="info-row"><span class="info-label">Admission No:</span> <span>{{ $student->admission_number }}</span></div>
            <div class="info-row"><span class="info-label">Class:</span> <span>{{ $student->schoolClass?->name }} {{ $student->arm?->name }}</span></div>
            <div class="info-row"><span class="info-label">Gender:</span> <span>{{ ucfirst($student->gender) }}</span></div>
        </div>
        <div style="width: 50%;">
            <div class="info-row"><span class="info-label">Session:</span> <span>{{ $reportCard->session?->name }}</span></div>
            <div class="info-row"><span class="info-label">Term:</span> <span>{{ $reportCard->term?->name }}</span></div>
            <div class="info-row"><span class="info-label">Position:</span> <span class="position">{{ $reportCard->position_in_class }}<sup>{{ $reportCard->position_in_class == 1 ? 'st' : ($reportCard->position_in_class == 2 ? 'nd' : ($reportCard->position_in_class == 3 ? 'rd' : 'th')) }}</sup></span> of {{ $reportCard->class_size }}</div>
            <div class="info-row"><span class="info-label">Average:</span> <span style="font-size: 14px; font-weight: bold;">{{ $reportCard->average_score }}%</span></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>CA1 (20)</th>
                <th>CA2 (20)</th>
                <th>CA3 (20)</th>
                <th>Exam (60)</th>
                <th>Total (100)</th>
                <th>Grade</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
            @php $gradeInfo = \App\Models\Result::calculateGrade($result->total_score); @endphp
            <tr>
                <td>{{ $result->subject?->name }}</td>
                <td style="text-align: center;">{{ $result->ca1_score }}</td>
                <td style="text-align: center;">{{ $result->ca2_score }}</td>
                <td style="text-align: center;">{{ $result->ca3_score }}</td>
                <td style="text-align: center;">{{ $result->exam_score }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $result->total_score }}</td>
                <td style="text-align: center;" class="grade-{{ $result->grade }}">{{ $result->grade }}</td>
                <td>{{ $gradeInfo['remark'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <strong>Summary:</strong>
        Total Subjects: {{ $reportCard->total_subjects }} |
        Passed: {{ $reportCard->subjects_passed }} |
        Failed: {{ $reportCard->subjects_failed }} |
        Total Score: {{ $reportCard->total_score }} |
        Average: {{ $reportCard->average_score }}%
    </div>

    <div class="remarks">
        <div class="remark-box">
            <span class="remark-label">Class Teacher\'s Remark:</span>
            <p>{{ $reportCard->class_teacher_remark ?? '_______________________________________________' }}</p>
        </div>
        <div class="remark-box">
            <span class="remark-label">Principal\'s Remark:</span>
            <p>{{ $reportCard->principal_remark ?? '_______________________________________________' }}</p>
        </div>
        @if($reportCard->next_term_begins)
        <p style="margin-top: 10px;"><strong>Next Term Begins:</strong> {{ $reportCard->next_term_begins->format('jS F, Y') }}</p>
        @endif
    </div>

    <div class="footer">
        <p>This is a computer-generated report card | Powered by ChrizFasa Academy. | {{ $school->name }} &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
