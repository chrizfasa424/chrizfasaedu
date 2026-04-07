<?php $__env->startSection('title', 'Fee Structures'); ?>
<?php $__env->startSection('header', 'Fee Structures'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Fee Structures</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage school fees and levies.</p>
        </div>
        <a href="<?php echo e(route('financial.fees.create')); ?>"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Fee
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Category</th>
                    <th class="px-5 py-3 text-left">Amount</th>
                    <th class="px-5 py-3 text-left">Session</th>
                    <th class="px-5 py-3 text-left">Class</th>
                    <th class="px-5 py-3 text-left">Compulsory</th>
                    <th class="px-5 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $fees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-medium text-slate-800"><?php echo e($fee->name); ?></td>
                    <td class="px-5 py-3">
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 capitalize"><?php echo e(str_replace('_',' ',$fee->category)); ?></span>
                    </td>
                    <td class="px-5 py-3 text-slate-700 font-semibold">₦<?php echo e(number_format($fee->amount, 2)); ?></td>
                    <td class="px-5 py-3 text-slate-600"><?php echo e($fee->session?->name ?? '—'); ?></td>
                    <td class="px-5 py-3 text-slate-600"><?php echo e($fee->schoolClass?->name ?? 'All'); ?></td>
                    <td class="px-5 py-3">
                        <?php if($fee->is_compulsory): ?>
                        <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Yes</span>
                        <?php else: ?>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">No</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3">
                        <form method="POST" action="<?php echo e(route('financial.fees.destroy', $fee)); ?>" onsubmit="return confirm('Delete this fee?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-xs text-red-500 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">No fee structures found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div><?php echo e($fees->links()); ?></div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views/financial/fees/index.blade.php ENDPATH**/ ?>