<?php $__env->startSection('title', 'Student Panel'); ?>
<?php $__env->startSection('header', 'Student Panel'); ?>

<?php
    $testimonialsFormTitle = trim((string) ($publicPage['testimonials_form_title'] ?? 'Share Your Testimonial'));
    $testimonialsFormRoleLabel = trim((string) ($publicPage['testimonials_form_role_label'] ?? 'Role or Context'));
    $testimonialsFormRolePlaceholder = trim((string) ($publicPage['testimonials_form_role_placeholder'] ?? 'Student'));
    $testimonialsFormRatingLabel = trim((string) ($publicPage['testimonials_form_rating_label'] ?? 'Rating'));
    $testimonialsFormMessageLabel = trim((string) ($publicPage['testimonials_form_message_label'] ?? 'Your Testimonial'));
    $testimonialsFormMessagePlaceholder = trim((string) ($publicPage['testimonials_form_message_placeholder'] ?? 'Write your experience with the school...'));
    $testimonialsFormSubmitText = trim((string) ($publicPage['testimonials_form_submit_text'] ?? 'Submit Testimonial'));
    $testimonialFormStartedAt = now()->timestamp;
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <section class="rounded-xl border border-gray-200 bg-white p-6">
        <h3 class="text-xl font-semibold text-gray-900">Welcome, <?php echo e($student->full_name); ?></h3>
        <p class="mt-1 text-sm text-gray-600">
            Class: <?php echo e($student->schoolClass->name ?? 'Not assigned'); ?>

            <?php if($student->arm && $student->arm->name): ?>
                • Arm: <?php echo e($student->arm->name); ?>

            <?php endif; ?>
        </p>

        <div class="mt-4 grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Recent Approved Results</p>
                <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo e($results->count()); ?></p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Attendance Records</p>
                <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo e($attendance->count()); ?></p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Recent Invoices</p>
                <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo e($invoices->count()); ?></p>
            </div>
        </div>
    </section>

    <section id="student-testimonial-form" class="rounded-xl border border-gray-200 bg-white p-6">
        <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
            <h3 class="text-xl font-semibold text-gray-900"><?php echo e($testimonialsFormTitle !== '' ? $testimonialsFormTitle : 'Share Your Testimonial'); ?></h3>
            <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Admin Approval Required</span>
        </div>

        <?php if(session('testimonial_success')): ?>
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                <?php echo e(session('testimonial_success')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->has('testimonial_form')): ?>
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                <?php echo e($errors->first('testimonial_form')); ?>

            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('student.testimonials.submit')); ?>" method="POST" class="grid gap-4 md:grid-cols-2">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="started_at" value="<?php echo e(old('started_at', $testimonialFormStartedAt)); ?>">
            <div class="hidden" aria-hidden="true">
                <label for="website" class="sr-only">Website</label>
                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Student Name</label>
                <input type="text" value="<?php echo e($student->full_name); ?>" readonly class="w-full rounded-xl border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-700">
            </div>

            <div>
                <label for="testimonial-role-title" class="mb-1 block text-sm font-semibold text-gray-700"><?php echo e($testimonialsFormRoleLabel !== '' ? $testimonialsFormRoleLabel : 'Role or Context'); ?></label>
                <input id="testimonial-role-title" type="text" name="role_title" maxlength="140" value="<?php echo e(old('role_title', 'Student')); ?>" placeholder="<?php echo e($testimonialsFormRolePlaceholder !== '' ? $testimonialsFormRolePlaceholder : 'Student'); ?>" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm text-gray-900 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                <?php $__errorArgs = ['role_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs font-medium text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label for="testimonial-rating" class="mb-1 block text-sm font-semibold text-gray-700"><?php echo e($testimonialsFormRatingLabel !== '' ? $testimonialsFormRatingLabel : 'Rating'); ?></label>
                <select id="testimonial-rating" name="rating" required class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm text-gray-900 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    <?php $__currentLoopData = [5, 4, 3, 2, 1]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rating): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($rating); ?>" <?php echo e((int) old('rating', 5) === $rating ? 'selected' : ''); ?>><?php echo e($rating); ?> / 5</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['rating'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs font-medium text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="md:col-span-2">
                <label for="testimonial-message" class="mb-1 block text-sm font-semibold text-gray-700"><?php echo e($testimonialsFormMessageLabel !== '' ? $testimonialsFormMessageLabel : 'Your Testimonial'); ?></label>
                <textarea id="testimonial-message" name="message" rows="5" required minlength="20" maxlength="1200" placeholder="<?php echo e($testimonialsFormMessagePlaceholder !== '' ? $testimonialsFormMessagePlaceholder : 'Write your experience with the school...'); ?>" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm text-gray-900 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100"><?php echo e(old('message')); ?></textarea>
                <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs font-medium text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <p class="mt-2 text-xs text-gray-500">For security and moderation, links are not allowed and all submissions are reviewed before publication.</p>
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="inline-flex rounded-full bg-indigo-700 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-800">
                    <?php echo e($testimonialsFormSubmitText !== '' ? $testimonialsFormSubmitText : 'Submit Testimonial'); ?>

                </button>
            </div>
        </form>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-6">
        <h3 class="text-lg font-semibold text-gray-900">Your Recent Testimonial Submissions</h3>
        <p class="mt-1 text-sm text-gray-500">Track the review status of your submissions.</p>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Date</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Rating</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Message</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $studentTestimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $status = strtolower((string) $testimonial->status);
                            $badgeClass = $status === 'approved'
                                ? 'bg-emerald-100 text-emerald-700'
                                : ($status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700');
                        ?>
                        <tr>
                            <td class="px-3 py-3 text-gray-600"><?php echo e($testimonial->created_at?->format('d M Y, h:i A') ?? '-'); ?></td>
                            <td class="px-3 py-3 text-gray-700"><?php echo e(max(1, min(5, (int) $testimonial->rating))); ?>/5</td>
                            <td class="px-3 py-3 text-gray-700"><?php echo e(\Illuminate\Support\Str::limit($testimonial->message, 130)); ?></td>
                            <td class="px-3 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($badgeClass); ?>">
                                    <?php echo e(ucfirst($status)); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-3 py-8 text-center text-sm text-gray-500">No testimonial submissions yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views\portal\student\dashboard.blade.php ENDPATH**/ ?>