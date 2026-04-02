<?php $__env->startSection('title', 'Multi-School Dashboard'); ?>
<?php $__env->startSection('header', 'Multi-School Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <section class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total Schools</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900"><?php echo e(number_format($summary['totalSchools'])); ?></p>
            <p class="mt-2 text-sm text-slate-500">All registered schools across the platform.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Active Schools</p>
            <p class="mt-3 text-3xl font-semibold text-emerald-600"><?php echo e(number_format($summary['activeSchools'])); ?></p>
            <p class="mt-2 text-sm text-slate-500">Schools currently active and visible in the system.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">This Page</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900"><?php echo e(number_format($summary['schoolsOnPage'])); ?></p>
            <p class="mt-2 text-sm text-slate-500">Records loaded on the current dashboard page.</p>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.1fr,1.6fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-violet-700">School Onboarding</p>
                <h3 class="mt-2 text-2xl font-semibold text-slate-900">Add a new school tenant</h3>
                <p class="mt-2 text-sm text-slate-500">Create a school, assign its first admin, and provision the subscription in one workflow.</p>
            </div>

            <form action="<?php echo e(route('multi-school.onboard')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">School name</label>
                        <input id="name" name="name" type="text" value="<?php echo e(old('name')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                    </div>
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">School email</label>
                        <input id="email" name="email" type="email" value="<?php echo e(old('email')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="phone" class="mb-2 block text-sm font-medium text-slate-700">Phone</label>
                        <input id="phone" name="phone" type="text" value="<?php echo e(old('phone')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                    </div>
                    <div>
                        <label for="school_type" class="mb-2 block text-sm font-medium text-slate-700">School type</label>
                        <select id="school_type" name="school_type" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                            <option value="">Select type</option>
                            <option value="primary" <?php if(old('school_type') === 'primary'): echo 'selected'; endif; ?>>Primary</option>
                            <option value="secondary" <?php if(old('school_type') === 'secondary'): echo 'selected'; endif; ?>>Secondary</option>
                            <option value="combined" <?php if(old('school_type') === 'combined'): echo 'selected'; endif; ?>>Combined</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="address" class="mb-2 block text-sm font-medium text-slate-700">Address</label>
                    <input id="address" name="address" type="text" value="<?php echo e(old('address')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="city" class="mb-2 block text-sm font-medium text-slate-700">City</label>
                        <input id="city" name="city" type="text" value="<?php echo e(old('city')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                    </div>
                    <div>
                        <label for="state" class="mb-2 block text-sm font-medium text-slate-700">State</label>
                        <input id="state" name="state" type="text" value="<?php echo e(old('state')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="plan" class="mb-2 block text-sm font-medium text-slate-700">Subscription plan</label>
                        <select id="plan" name="plan" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                            <option value="">Select plan</option>
                            <option value="basic" <?php if(old('plan') === 'basic'): echo 'selected'; endif; ?>>Basic</option>
                            <option value="standard" <?php if(old('plan') === 'standard'): echo 'selected'; endif; ?>>Standard</option>
                            <option value="premium" <?php if(old('plan') === 'premium'): echo 'selected'; endif; ?>>Premium</option>
                            <option value="enterprise" <?php if(old('plan') === 'enterprise'): echo 'selected'; endif; ?>>Enterprise</option>
                        </select>
                    </div>
                    <div>
                        <label for="admin_name" class="mb-2 block text-sm font-medium text-slate-700">Admin full name</label>
                        <input id="admin_name" name="admin_name" type="text" value="<?php echo e(old('admin_name')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                    </div>
                </div>

                <div>
                    <label for="admin_email" class="mb-2 block text-sm font-medium text-slate-700">Admin email</label>
                    <input id="admin_email" name="admin_email" type="email" value="<?php echo e(old('admin_email')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                </div>

                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    The first school admin account will be created with a temporary password of <span class="font-semibold">changeme123</span>.
                </div>

                <button type="submit" class="inline-flex items-center rounded-full bg-[#2D1D5C] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C] focus:outline-none focus:ring-2 focus:ring-[#DFE753] focus:ring-offset-2">
                    Create School Tenant
                </button>
            </form>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-6 py-5 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400">Tenant Directory</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-900">Registered schools</h3>
                </div>
                <div class="rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600">
                    <?php echo e(number_format($schools->total())); ?> schools
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">School</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Students</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Staff</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Plan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php $__empty_1 = true; $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-6 py-5">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900"><?php echo e($school->name); ?></p>
                                        <p class="mt-1 text-sm text-slate-500"><?php echo e($school->email ?: 'No email provided'); ?></p>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-sm text-slate-600"><?php echo e(ucfirst($school->school_type ?? 'n/a')); ?></td>
                                <td class="px-6 py-5 text-sm font-medium text-slate-900"><?php echo e(number_format($school->students_count)); ?></td>
                                <td class="px-6 py-5 text-sm font-medium text-slate-900"><?php echo e(number_format($school->staff_count)); ?></td>
                                <td class="px-6 py-5 text-sm text-slate-600"><?php echo e(ucfirst($school->subscription_plan ?? 'n/a')); ?></td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?php echo e($school->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700'); ?>">
                                        <?php echo e($school->is_active ? 'Active' : 'Inactive'); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">
                                    No schools have been onboarded yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-6 py-4">
                <?php echo e($schools->links()); ?>

            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views\multi-school\index.blade.php ENDPATH**/ ?>