<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('header', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500">Total Students</p>
        <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e(number_format($stats['total_students'])); ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500">Total Staff</p>
        <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e(number_format($stats['total_staff'])); ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500">Pending Admissions</p>
        <p class="text-2xl font-bold text-amber-600 mt-1"><?php echo e(number_format($stats['pending_admissions'])); ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500">Outstanding Fees</p>
        <p class="text-2xl font-bold text-red-600 mt-1">₦<?php echo e(number_format($stats['outstanding_fees'], 2)); ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500">This Month Revenue</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">₦<?php echo e(number_format($stats['recent_payments'], 2)); ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Admissions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Recent Admissions</h3>
        </div>
        <div class="p-5">
            <div class="space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $recentAdmissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($admission->first_name); ?> <?php echo e($admission->last_name); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($admission->application_number); ?> · <?php echo e($admission->class_applied_for); ?></p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full bg-<?php echo e($admission->status->color()); ?>-100 text-<?php echo e($admission->status->color()); ?>-700">
                        <?php echo e($admission->status->label()); ?>

                    </span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-gray-500">No recent admissions</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Recent Payments</h3>
        </div>
        <div class="p-5">
            <div class="space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $recentPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($payment->student?->full_name); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($payment->payment_reference); ?></p>
                    </div>
                    <span class="text-sm font-semibold text-emerald-600">₦<?php echo e(number_format($payment->amount, 2)); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-gray-500">No recent payments</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views\dashboard\index.blade.php ENDPATH**/ ?>