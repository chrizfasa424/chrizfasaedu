<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Admission - ChrizFasa EMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .theme-cta-solid {
            background-color: var(--submenu-primary, #2D1D5C);
            border: 1px solid var(--submenu-primary, #2D1D5C);
            color: #ffffff;
        }

        .theme-cta-solid:hover,
        .theme-cta-solid:focus-visible {
            background-color: var(--submenu-secondary, #DFE753);
            border-color: var(--submenu-secondary, #DFE753);
            color: var(--submenu-hover-text, #2D1D5C);
        }
            .text-gray-900 {
            color: var(--theme-heading, #0F172A) !important;
        }

        .text-gray-500 {
            color: var(--theme-body, #475569) !important;
        }
    </style>
</head>
<?php
    $schoolName = $school?->name ?? 'ChrizFasa EMS';
    $theme = \App\Support\ThemePalette::fromPublicPage($publicPage);
    $siteBackgroundColor = $theme['site_background'];
    $headerBgColor = $theme['header'];
    $submenuPrimaryColor = $theme['primary']['500'];
    $submenuSecondaryColor = $theme['secondary']['500'];
    $submenuHoverTextColor = $theme['primary_text_on_secondary'];
    $themeHeadingColor = $theme['ink'];
    $themeBodyColor = $theme['muted'];
?>
<body class="min-h-screen" style="background-color: <?php echo e($siteBackgroundColor); ?>; color: <?php echo e($themeBodyColor); ?>; --submenu-primary: <?php echo e($submenuPrimaryColor); ?>; --submenu-secondary: <?php echo e($submenuSecondaryColor); ?>; --submenu-hover-text: <?php echo e($submenuHoverTextColor); ?>; --theme-heading: <?php echo e($themeHeadingColor); ?>; --theme-body: <?php echo e($themeBodyColor); ?>;">

    <!-- Header -->
    <nav class="shadow-sm border-b border-white/10" style="background-color: <?php echo e($headerBgColor); ?>;">
        <div class="max-w-4xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="text-xl font-bold text-white"><?php echo e($schoolName); ?></a>
            <a href="<?php echo e(route('login')); ?>" class="text-sm text-white hover:underline font-medium">Already applied? Login</a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-10">

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Online Admission Application</h1>
            <p class="text-gray-500 mt-2">Fill in the details below to apply for admission</p>
        </div>

        <?php if($errors->any()): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside text-sm">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <form action="<?php echo e(route('admission.apply.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
            <?php echo csrf_field(); ?>

            <!-- Student Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-5 pb-3 border-b">Student Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" value="<?php echo e(old('first_name')); ?>" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="<?php echo e(old('last_name')); ?>" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Other Names</label>
                        <input type="text" name="other_names" value="<?php echo e(old('other_names')); ?>"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                        <select name="gender" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">-- Select --</option>
                            <option value="male" <?php echo e(old('gender') == 'male' ? 'selected' : ''); ?>>Male</option>
                            <option value="female" <?php echo e(old('gender') == 'female' ? 'selected' : ''); ?>>Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth <span class="text-red-500">*</span></label>
                        <input type="date" name="date_of_birth" value="<?php echo e(old('date_of_birth')); ?>" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Class Applying For <span class="text-red-500">*</span></label>
                        <input type="text" name="class_applied_for" value="<?php echo e(old('class_applied_for')); ?>" placeholder="e.g. JSS 1, SSS 2" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">State of Origin</label>
                        <input type="text" name="state_of_origin" value="<?php echo e(old('state_of_origin')); ?>"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">LGA</label>
                        <input type="text" name="lga" value="<?php echo e(old('lga')); ?>"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Previous School</label>
                        <input type="text" name="previous_school" value="<?php echo e(old('previous_school')); ?>"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Home Address</label>
                        <textarea name="address" rows="2"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"><?php echo e(old('address')); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Parent / Guardian Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-5 pb-3 border-b">Parent / Guardian Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="parent_name" value="<?php echo e(old('parent_name')); ?>" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                        <input type="tel" name="parent_phone" value="<?php echo e(old('parent_phone')); ?>" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="parent_email" value="<?php echo e(old('parent_email')); ?>"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Occupation</label>
                        <input type="text" name="parent_occupation" value="<?php echo e(old('parent_occupation')); ?>"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-5 pb-3 border-b">Documents (Optional)</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Passport Photo</label>
                        <input type="file" name="photo" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        <p class="text-xs text-gray-400 mt-1">Max 2MB, JPG/PNG</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Birth Certificate</label>
                        <input type="file" name="birth_certificate" accept=".pdf,image/*"
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        <p class="text-xs text-gray-400 mt-1">Max 5MB, PDF/Image</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Previous School Result</label>
                        <input type="file" name="previous_result" accept=".pdf,image/*"
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        <p class="text-xs text-gray-400 mt-1">Max 5MB, PDF/Image</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="theme-cta-solid px-8 py-3 rounded-lg font-medium transition text-sm">
                    Submit Application
                </button>
            </div>
        </form>
    </div>
    <?php echo $__env->make('public.partials.footer', ['school' => $school, 'publicPage' => $publicPage], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>
</html>
<?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views\admission\apply.blade.php ENDPATH**/ ?>