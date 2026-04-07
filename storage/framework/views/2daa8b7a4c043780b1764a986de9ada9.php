<form action="<?php echo e(route('settings.update')); ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">School Name</label>
            <input type="text" name="name" value="<?php echo e(old('name', $school->name)); ?>" class="w-full rounded-2xl border-slate-300">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Email Address</label>
            <input type="email" name="email" value="<?php echo e(old('email', $school->email)); ?>" class="w-full rounded-2xl border-slate-300">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Phone Number</label>
            <input type="text" name="phone" value="<?php echo e(old('phone', $school->phone)); ?>" class="w-full rounded-2xl border-slate-300">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Website</label>
            <input type="url" name="website" value="<?php echo e(old('website', $school->website)); ?>" class="w-full rounded-2xl border-slate-300" placeholder="https://example.com">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Main School Domain</label>
            <input type="text" name="domain" value="<?php echo e(old('domain', $school->domain)); ?>" class="w-full rounded-2xl border-slate-300" placeholder="chrizfasaedu.test">
            <p class="mt-2 text-xs text-slate-500">Enter only the domain name. Do not include http:// or any path.</p>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Current Public Link</label>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                <?php if($school->domain): ?>
                    <a href="http://<?php echo e($school->domain); ?>" target="_blank" rel="noopener" class="font-semibold text-[#2D1D5C] hover:underline">http://<?php echo e($school->domain); ?></a>
                <?php else: ?>
                    No custom domain saved yet.
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Motto / Hero Line</label>
        <input type="text" name="motto" value="<?php echo e(old('motto', $school->motto)); ?>" class="w-full rounded-2xl border-slate-300">
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Address</label>
        <textarea name="address" rows="4" class="w-full rounded-2xl border-slate-300"><?php echo e(old('address', $school->address)); ?></textarea>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
            <label class="mb-2 block text-sm font-semibold text-slate-700">School Logo</label>
            <input type="file" name="logo" accept="image/*" class="w-full rounded-2xl border-slate-300 bg-white">
            <label class="mt-4 inline-flex items-center text-sm font-medium text-red-600">
                <input type="checkbox" name="remove_logo" value="1" class="mr-2 rounded border-slate-300">
                Remove current logo
            </label>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
            <p class="mb-3 text-sm font-semibold text-slate-700">Current Logo Preview</p>
            <?php if($school->logo): ?>
                <img src="<?php echo e(asset('storage/' . ltrim($school->logo, '/'))); ?>" alt="School Logo" class="h-24 w-24 rounded-2xl border border-slate-200 bg-white object-cover p-1">
            <?php else: ?>
                <div class="flex h-24 w-24 items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white text-xs text-slate-400">No logo</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Site Favicon</label>
            <input type="file" name="favicon" accept=".ico,image/png,image/jpeg,image/webp" class="w-full rounded-2xl border-slate-300 bg-white">
            <p class="mt-2 text-xs text-slate-500">Recommended: a square png or ico used in the browser tab and bookmarks.</p>
            <label class="mt-4 inline-flex items-center text-sm font-medium text-red-600">
                <input type="checkbox" name="remove_favicon" value="1" class="mr-2 rounded border-slate-300">
                Remove current favicon
            </label>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
            <p class="mb-3 text-sm font-semibold text-slate-700">Current Favicon Preview</p>
            <?php if($faviconPath): ?>
                <img src="<?php echo e(asset('storage/' . ltrim($faviconPath, '/'))); ?>" alt="Site Favicon" class="h-16 w-16 rounded-2xl border border-slate-200 bg-white object-cover p-1">
            <?php else: ?>
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white text-[11px] text-slate-400">No icon</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <h2 class="text-base font-bold text-slate-900">Navigation and Shared Button Text</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Header Apply Text</label>
                <input type="text" form="public-site-settings-form" name="header_apply_text" value="<?php echo e(old('header_apply_text', $publicPage['header_apply_text'] ?? 'Apply')); ?>" class="w-full rounded-2xl border-slate-300">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Header Portal Text</label>
                <input type="text" form="public-site-settings-form" name="header_portal_login_text" value="<?php echo e(old('header_portal_login_text', $publicPage['header_portal_login_text'] ?? 'Portal Login')); ?>" class="w-full rounded-2xl border-slate-300">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Mobile Apply Text</label>
                <input type="text" form="public-site-settings-form" name="mobile_apply_text" value="<?php echo e(old('mobile_apply_text', $publicPage['mobile_apply_text'] ?? 'Apply Now')); ?>" class="w-full rounded-2xl border-slate-300">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Mobile Portal Text</label>
                <input type="text" form="public-site-settings-form" name="mobile_portal_login_text" value="<?php echo e(old('mobile_portal_login_text', $publicPage['mobile_portal_login_text'] ?? 'Portal Login')); ?>" class="w-full rounded-2xl border-slate-300">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Site Title Suffix</label>
                <input type="text" form="public-site-settings-form" name="site_title_suffix" value="<?php echo e(old('site_title_suffix', $publicPage['site_title_suffix'] ?? 'KG, Primary and Secondary School')); ?>" class="w-full rounded-2xl border-slate-300">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Mobile Menu Title</label>
                <input type="text" form="public-site-settings-form" name="mobile_menu_title" value="<?php echo e(old('mobile_menu_title', $publicPage['mobile_menu_title'] ?? 'Menu')); ?>" class="w-full rounded-2xl border-slate-300">
            </div>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-2xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">Save Site Settings</button>
        <button type="submit" form="public-site-settings-form" class="inline-flex items-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-[#2D1D5C] hover:text-[#2D1D5C]">Save Shared Navigation Text</button>
    </div>
</form>

<form id="public-site-settings-form" action="<?php echo e(route('settings.public-page')); ?>" method="POST" class="hidden">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
</form>

<?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views/system/settings/partials/site-settings.blade.php ENDPATH**/ ?>