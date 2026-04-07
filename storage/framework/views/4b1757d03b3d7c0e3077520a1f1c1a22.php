<?php $__env->startSection('title', $class->name); ?>
<?php $__env->startSection('header', $class->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="<?php echo e(route('academic.classes.index')); ?>" class="text-sm text-slate-500 hover:text-slate-700">← Classes</a>
        <span class="text-slate-300">/</span>
        <span class="text-sm font-medium text-slate-800"><?php echo e($class->name); ?></span>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <?php $__currentLoopData = [
            ['Students', $class->students->count()],
            ['Arms', $class->arms->count()],
            ['Subjects', $class->subjects->count()],
            ['Capacity', $class->capacity ?? '—'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $val]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-slate-800"><?php echo e($val); ?></div>
            <div class="text-xs text-slate-500 mt-0.5"><?php echo e($label); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php if($class->arms->count()): ?>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-700 mb-3">Arms</h2>
        <div class="flex flex-wrap gap-2">
            <?php $__currentLoopData = $class->arms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $arm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700"><?php echo e($arm->name); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-700">Students</h2>
            <a href="<?php echo e(route('academic.students.create')); ?>" class="text-xs font-medium text-indigo-600 hover:underline">+ Add Student</a>
        </div>
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Admission No.</th>
                    <th class="px-5 py-3 text-left">Arm</th>
                    <th class="px-5 py-3 text-left">Gender</th>
                    <th class="px-5 py-3 text-left">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $class->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-medium text-slate-800"><?php echo e($student->full_name); ?></td>
                    <td class="px-5 py-3 text-slate-600"><?php echo e($student->admission_number); ?></td>
                    <td class="px-5 py-3 text-slate-600"><?php echo e($student->arm?->name ?? '—'); ?></td>
                    <td class="px-5 py-3 capitalize text-slate-600"><?php echo e($student->gender); ?></td>
                    <td class="px-5 py-3">
                        <a href="<?php echo e(route('academic.students.show', $student)); ?>" class="text-xs text-indigo-600 hover:underline font-medium">View</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400">No students in this class yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views/academic/classes/show.blade.php ENDPATH**/ ?>