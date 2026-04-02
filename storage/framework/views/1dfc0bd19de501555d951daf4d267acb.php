<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - <?php echo e($student->full_name); ?></title>
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
        <?php if($school->logo): ?>
        <img src="<?php echo e(public_path('storage/' . $school->logo)); ?>" height="60" style="margin-bottom: 5px;">
        <?php endif; ?>
        <h1><?php echo e(strtoupper($school->name)); ?></h1>
        <p><?php echo e($school->address); ?>, <?php echo e($school->city); ?>, <?php echo e($school->state); ?></p>
        <p>Tel: <?php echo e($school->phone); ?> | Email: <?php echo e($school->email); ?></p>
        <?php if($school->motto): ?><p><em>"<?php echo e($school->motto); ?>"</em></p><?php endif; ?>
        <h2 style="margin-top: 10px; font-size: 16px; color: #333;">STUDENT REPORT CARD</h2>
    </div>

    <div style="display: flex; gap: 20px; margin-bottom: 15px;">
        <div style="width: 50%;">
            <div class="info-row"><span class="info-label">Name:</span> <span><?php echo e($student->full_name); ?></span></div>
            <div class="info-row"><span class="info-label">Admission No:</span> <span><?php echo e($student->admission_number); ?></span></div>
            <div class="info-row"><span class="info-label">Class:</span> <span><?php echo e($student->schoolClass?->name); ?> <?php echo e($student->arm?->name); ?></span></div>
            <div class="info-row"><span class="info-label">Gender:</span> <span><?php echo e(ucfirst($student->gender)); ?></span></div>
        </div>
        <div style="width: 50%;">
            <div class="info-row"><span class="info-label">Session:</span> <span><?php echo e($reportCard->session?->name); ?></span></div>
            <div class="info-row"><span class="info-label">Term:</span> <span><?php echo e($reportCard->term?->name); ?></span></div>
            <div class="info-row"><span class="info-label">Position:</span> <span class="position"><?php echo e($reportCard->position_in_class); ?><sup><?php echo e($reportCard->position_in_class == 1 ? 'st' : ($reportCard->position_in_class == 2 ? 'nd' : ($reportCard->position_in_class == 3 ? 'rd' : 'th'))); ?></sup></span> of <?php echo e($reportCard->class_size); ?></div>
            <div class="info-row"><span class="info-label">Average:</span> <span style="font-size: 14px; font-weight: bold;"><?php echo e($reportCard->average_score); ?>%</span></div>
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
            <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $gradeInfo = \App\Models\Result::calculateGrade($result->total_score); ?>
            <tr>
                <td><?php echo e($result->subject?->name); ?></td>
                <td style="text-align: center;"><?php echo e($result->ca1_score); ?></td>
                <td style="text-align: center;"><?php echo e($result->ca2_score); ?></td>
                <td style="text-align: center;"><?php echo e($result->ca3_score); ?></td>
                <td style="text-align: center;"><?php echo e($result->exam_score); ?></td>
                <td style="text-align: center; font-weight: bold;"><?php echo e($result->total_score); ?></td>
                <td style="text-align: center;" class="grade-<?php echo e($result->grade); ?>"><?php echo e($result->grade); ?></td>
                <td><?php echo e($gradeInfo['remark']); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="summary">
        <strong>Summary:</strong>
        Total Subjects: <?php echo e($reportCard->total_subjects); ?> |
        Passed: <?php echo e($reportCard->subjects_passed); ?> |
        Failed: <?php echo e($reportCard->subjects_failed); ?> |
        Total Score: <?php echo e($reportCard->total_score); ?> |
        Average: <?php echo e($reportCard->average_score); ?>%
    </div>

    <div class="remarks">
        <div class="remark-box">
            <span class="remark-label">Class Teacher\'s Remark:</span>
            <p><?php echo e($reportCard->class_teacher_remark ?? '_______________________________________________'); ?></p>
        </div>
        <div class="remark-box">
            <span class="remark-label">Principal\'s Remark:</span>
            <p><?php echo e($reportCard->principal_remark ?? '_______________________________________________'); ?></p>
        </div>
        <?php if($reportCard->next_term_begins): ?>
        <p style="margin-top: 10px;"><strong>Next Term Begins:</strong> <?php echo e($reportCard->next_term_begins->format('jS F, Y')); ?></p>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>This is a computer-generated report card | Powered by ChrizFasa EMS. | <?php echo e($school->name); ?> &copy; <?php echo e(date('Y')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views\pdf\report-card.blade.php ENDPATH**/ ?>