<?php $__env->startSection('title', 'Attendance'); ?>
<?php $__env->startSection('header', 'Attendance'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-900">Student Attendance</h1>
        <p class="text-sm text-slate-500 mt-0.5">Record and view daily student attendance.</p>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <form method="GET" action="<?php echo e(route('academic.attendance.index')); ?>" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Class</label>
            <select name="class_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Select class</option>
                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($c->id); ?>" <?php echo e($classId == $c->id ? 'selected' : ''); ?>><?php echo e($c->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Date</label>
            <input type="date" name="date" value="<?php echo e($date); ?>" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Load</button>
    </form>

    <?php if($classId && $students->count()): ?>
    <form method="POST" action="<?php echo e(route('academic.attendance.store')); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="class_id" value="<?php echo e($classId); ?>">
        <input type="hidden" name="date" value="<?php echo e($date); ?>">

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-700">
                    <?php echo e($classes->firstWhere('id', $classId)?->name); ?> — <?php echo e(\Carbon\Carbon::parse($date)->format('d M Y')); ?>

                </h2>
                <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Save Attendance</button>
            </div>
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-left">#</th>
                        <th class="px-5 py-3 text-left">Student</th>
                        <th class="px-5 py-3 text-left">Admission No.</th>
                        <th class="px-5 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 text-slate-500"><?php echo e($i + 1); ?></td>
                        <td class="px-5 py-3 font-medium text-slate-800"><?php echo e($student->full_name); ?></td>
                        <td class="px-5 py-3 text-slate-600"><?php echo e($student->admission_number); ?></td>
                        <td class="px-5 py-3">
                            <input type="hidden" name="attendance[<?php echo e($i); ?>][student_id]" value="<?php echo e($student->id); ?>">
                            <select name="attendance[<?php echo e($i); ?>][status]"
                                class="rounded-lg border border-slate-300 px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <?php $__currentLoopData = ['present' => 'Present', 'absent' => 'Absent', 'late' => 'Late', 'excused' => 'Excused']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>" <?php echo e(($attendances[$student->id] ?? 'present') === $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </form>
    <?php elseif($classId && $students->isEmpty()): ?>
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
            No active students in this class.
        </div>
    <?php else: ?>
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
            Select a class and date to record attendance.
        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views/academic/attendance/index.blade.php ENDPATH**/ ?>