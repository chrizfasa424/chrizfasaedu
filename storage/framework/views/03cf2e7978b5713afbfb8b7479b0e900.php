<form action="<?php echo e(route('settings.public-page')); ?>" method="POST" class="space-y-8">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Admissions Label</label>
            <input type="text" name="admissions_label" value="<?php echo e(old('admissions_label', $publicPage['admissions_label'] ?? 'Admissions')); ?>" class="w-full rounded-2xl border-slate-300">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Admissions Process Label</label>
            <input type="text" name="admissions_process_label" value="<?php echo e(old('admissions_process_label', $publicPage['admissions_process_label'] ?? 'Admissions Process')); ?>" class="w-full rounded-2xl border-slate-300">
        </div>
        <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Admissions Intro</label>
            <textarea name="admissions_intro" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300"><?php echo e(old('admissions_intro', $publicPage['admissions_intro'] ?? '')); ?></textarea>
        </div>
        <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Admissions Items</label>
            <textarea name="admissions_items_text" rows="10" class="w-full rounded-2xl border-slate-300"><?php echo e(old('admissions_items_text', $admissionsItemsText)); ?></textarea>
            <p class="mt-2 text-xs text-slate-500">Use one line per item in the format: Title | Description</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Admission Steps</label>
            <textarea name="admission_steps" rows="8" class="w-full rounded-2xl border-slate-300"><?php echo e(old('admission_steps', $admissionStepsText)); ?></textarea>
            <p class="mt-2 text-xs text-slate-500">Use one step per line.</p>
        </div>
        <div class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50 p-5">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Visit Booking Button Text</label>
                <input type="text" name="visit_booking_button_text" value="<?php echo e(old('visit_booking_button_text', $publicPage['visit_booking_button_text'] ?? 'Visit Booking')); ?>" class="w-full rounded-2xl border-slate-300">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Quick Apply Button Text</label>
                <input type="text" name="quick_apply_button_text" value="<?php echo e(old('quick_apply_button_text', $publicPage['quick_apply_button_text'] ?? 'Apply Now')); ?>" class="w-full rounded-2xl border-slate-300">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Visit Booking URL</label>
                <input type="url" name="visit_booking_url" value="<?php echo e(old('visit_booking_url', $publicPage['visit_booking_url'] ?? '')); ?>" class="w-full rounded-2xl border-slate-300" placeholder="https://">
            </div>
        </div>
    </div>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">Save Admissions Page</button>
</form>

<?php echo $__env->make('system.settings.partials._submenu-item-editor', [
    '_section'      => 'admissions',
    '_sectionLabel' => trim((string) ($publicPage['admissions_label'] ?? 'Admissions')),
    '_items'        => $publicPage['admissions'] ?? [],
    '_publicPage'   => $publicPage,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views/system/settings/partials/admissions.blade.php ENDPATH**/ ?>