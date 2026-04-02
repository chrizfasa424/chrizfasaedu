

<?php $__env->startSection('title', 'Settings'); ?>
<?php $__env->startSection('header', 'School Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900">School Identity and Contact</h3>
        <p class="text-sm text-gray-500 mt-1">This information is used on the public website and across the portal.</p>

        <form action="<?php echo e(route('settings.update')); ?>" method="POST" enctype="multipart/form-data" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">School Name</label>
                <input type="text" name="name" value="<?php echo e(old('name', $school->name)); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="<?php echo e(old('email', $school->email)); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" value="<?php echo e(old('phone', $school->phone)); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                <input type="url" name="website" value="<?php echo e(old('website', $school->website)); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="https://example.com">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Motto / Hero Line</label>
                <input type="text" name="motto" value="<?php echo e(old('motto', $school->motto)); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" rows="3" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"><?php echo e(old('address', $school->address)); ?></textarea>
            </div>

            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">School Logo</label>
                    <input type="file" name="logo" accept="image/*" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <label class="inline-flex items-center mt-3 text-sm text-red-600">
                        <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 mr-2">
                        Remove current logo
                    </label>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-2">Current Logo Preview</p>
                    <?php if($school->logo): ?>
                        <img src="<?php echo e(asset('storage/' . ltrim($school->logo, '/'))); ?>" alt="School Logo" class="h-20 w-20 object-cover rounded-lg border border-gray-200 bg-gray-50">
                    <?php else: ?>
                        <div class="h-20 w-20 rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-xs text-gray-400">
                            No logo
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="inline-flex items-center px-5 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-semibold transition duration-200 hover:-translate-y-0.5 hover:bg-emerald-700">
                    Save Identity Settings
                </button>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900">Public Homepage Content Manager</h3>
        <p class="text-sm text-gray-500 mt-1">Manage all public website sections. Use one item per line in the format: <span class="font-medium">Title | Description</span>.</p>

        <form action="<?php echo e(route('settings.public-page')); ?>" method="POST" enctype="multipart/form-data" class="mt-6 space-y-8">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hero Badge Text</label>
                    <input type="text" name="hero_badge_text" value="<?php echo e(old('hero_badge_text', $publicPage['hero_badge_text'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Admission Button Label</label>
                    <input type="text" name="cta_primary_text" value="<?php echo e(old('cta_primary_text', $publicPage['cta_primary_text'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hero Title</label>
                    <input type="text" name="hero_title" value="<?php echo e(old('hero_title', $publicPage['hero_title'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hero Subtitle</label>
                    <textarea name="hero_subtitle" rows="3" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"><?php echo e(old('hero_subtitle', $publicPage['hero_subtitle'] ?? '')); ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Button Label</label>
                    <input type="text" name="cta_secondary_text" value="<?php echo e(old('cta_secondary_text', $publicPage['cta_secondary_text'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Admission Session Text</label>
                    <input type="text" name="admission_session_text" value="<?php echo e(old('admission_session_text', $publicPage['admission_session_text'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 p-4 bg-gray-50">
                <h4 class="text-sm font-semibold text-gray-900">Brand Theme Manager</h4>
                <p class="text-xs text-gray-500 mt-1">Each school can control its own public colors and visual style here. These settings are saved per school and applied automatically on that school domain.</p>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Theme Style</label>
                        <select name="theme_style" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <?php $themeStyle = old('theme_style', $publicPage['theme_style'] ?? 'modern-grid'); ?>
                            <option value="modern-grid" <?php echo e($themeStyle === 'modern-grid' ? 'selected' : ''); ?>>Modern Grid</option>
                            <option value="soft-gradient" <?php echo e($themeStyle === 'soft-gradient' ? 'selected' : ''); ?>>Soft Gradient</option>
                            <option value="minimal-clean" <?php echo e($themeStyle === 'minimal-clean' ? 'selected' : ''); ?>>Minimal Clean</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Primary Brand Color</label>
                        <input type="color" name="primary_color" value="<?php echo e(old('primary_color', $publicPage['primary_color'] ?? '#2D1D5C')); ?>" class="h-11 w-full rounded-lg border border-gray-300 bg-white p-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Brand Color</label>
                        <input type="color" name="secondary_color" value="<?php echo e(old('secondary_color', $publicPage['secondary_color'] ?? '#DFE753')); ?>" class="h-11 w-full rounded-lg border border-gray-300 bg-white p-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Site Background Color</label>
                        <input type="color" name="site_background_color" value="<?php echo e(old('site_background_color', $publicPage['site_background_color'] ?? '#F8FAFC')); ?>" class="h-11 w-full rounded-lg border border-gray-300 bg-white p-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Heading Text Color</label>
                        <input type="color" name="heading_text_color" value="<?php echo e(old('heading_text_color', $publicPage['heading_text_color'] ?? '#0F172A')); ?>" class="h-11 w-full rounded-lg border border-gray-300 bg-white p-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Body Text Color</label>
                        <input type="color" name="body_text_color" value="<?php echo e(old('body_text_color', $publicPage['body_text_color'] ?? '#475569')); ?>" class="h-11 w-full rounded-lg border border-gray-300 bg-white p-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Card Surface Color</label>
                        <input type="color" name="surface_color" value="<?php echo e(old('surface_color', $publicPage['surface_color'] ?? '#FFFFFF')); ?>" class="h-11 w-full rounded-lg border border-gray-300 bg-white p-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Soft Surface Tint</label>
                        <input type="color" name="soft_surface_color" value="<?php echo e(old('soft_surface_color', $publicPage['soft_surface_color'] ?? '#EEF6FF')); ?>" class="h-11 w-full rounded-lg border border-gray-300 bg-white p-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Header Color</label>
                        <input type="color" name="header_bg_color" value="<?php echo e(old('header_bg_color', $publicPage['header_bg_color'] ?? '#2D1D5C')); ?>" class="h-11 w-full rounded-lg border border-gray-300 bg-white p-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Color</label>
                        <input type="color" name="footer_bg_color" value="<?php echo e(old('footer_bg_color', $publicPage['footer_bg_color'] ?? '#2D1D5C')); ?>" class="h-11 w-full rounded-lg border border-gray-300 bg-white p-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Separator Color</label>
                        <input type="color" name="footer_separator_color" value="<?php echo e(old('footer_separator_color', $publicPage['footer_separator_color'] ?? '#DFE753')); ?>" class="h-11 w-full rounded-lg border border-gray-300 bg-white p-1">
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Hero Metrics</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <?php for($i = 0; $i < 4; $i++): ?>
                        <div class="rounded-lg border border-gray-200 p-3 bg-gray-50">
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Metric <?php echo e($i + 1); ?> Value</label>
                            <input type="text" name="metric_<?php echo e($i + 1); ?>_value" value="<?php echo e(old('metric_' . ($i + 1) . '_value', $publicPage['metrics'][$i]['value'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 mb-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Metric <?php echo e($i + 1); ?> Label</label>
                            <input type="text" name="metric_<?php echo e($i + 1); ?>_label" value="<?php echo e(old('metric_' . ($i + 1) . '_label', $publicPage['metrics'][$i]['label'] ?? '')); ?>" class="w-full rounded-lg border-gray-300">
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

                        <div class="rounded-xl border border-gray-200 p-4 bg-gray-50">
                <h4 class="text-sm font-semibold text-gray-900">Homepage Text Labels</h4>
                <p class="text-xs text-gray-500 mt-1">Edit section headings, button labels, and quick-contact labels shown on the homepage.</p>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Programs Label</label>
                        <input type="text" name="programs_label" value="<?php echo e(old('programs_label', $publicPage['programs_label'] ?? 'Programs')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Admissions Label</label>
                        <input type="text" name="admissions_label" value="<?php echo e(old('admissions_label', $publicPage['admissions_label'] ?? 'Admissions')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Admissions Process Label</label>
                        <input type="text" name="admissions_process_label" value="<?php echo e(old('admissions_process_label', $publicPage['admissions_process_label'] ?? 'Admissions Process')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Academics Label</label>
                        <input type="text" name="academics_label" value="<?php echo e(old('academics_label', $publicPage['academics_label'] ?? 'Academic Excellence')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Facilities Label</label>
                        <input type="text" name="facilities_label" value="<?php echo e(old('facilities_label', $publicPage['facilities_label'] ?? 'Facilities')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">About Label</label>
                        <input type="text" name="about_label" value="<?php echo e(old('about_label', $publicPage['about_label'] ?? 'About Us')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student Life Label</label>
                        <input type="text" name="student_life_label" value="<?php echo e(old('student_life_label', $publicPage['student_life_label'] ?? 'Student Life')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parents Label</label>
                        <input type="text" name="parents_label" value="<?php echo e(old('parents_label', $publicPage['parents_label'] ?? 'Parents')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Label</label>
                        <input type="text" name="contact_label" value="<?php echo e(old('contact_label', $publicPage['contact_label'] ?? 'Contact')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Header Apply Text</label>
                        <input type="text" name="header_apply_text" value="<?php echo e(old('header_apply_text', $publicPage['header_apply_text'] ?? 'Apply')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Header Portal Text</label>
                        <input type="text" name="header_portal_login_text" value="<?php echo e(old('header_portal_login_text', $publicPage['header_portal_login_text'] ?? 'Portal Login')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Apply Text</label>
                        <input type="text" name="mobile_apply_text" value="<?php echo e(old('mobile_apply_text', $publicPage['mobile_apply_text'] ?? 'Apply Now')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Portal Text</label>
                        <input type="text" name="mobile_portal_login_text" value="<?php echo e(old('mobile_portal_login_text', $publicPage['mobile_portal_login_text'] ?? 'Portal Login')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parents Portal Button Text</label>
                        <input type="text" name="parents_portal_button_text" value="<?php echo e(old('parents_portal_button_text', $publicPage['parents_portal_button_text'] ?? 'Parent Portal Login')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Visit Booking Button Text</label>
                        <input type="text" name="visit_booking_button_text" value="<?php echo e(old('visit_booking_button_text', $publicPage['visit_booking_button_text'] ?? 'Visit Booking')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quick Apply Button Text</label>
                        <input type="text" name="quick_apply_button_text" value="<?php echo e(old('quick_apply_button_text', $publicPage['quick_apply_button_text'] ?? 'Apply Now')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimonials Badge</label>
                        <input type="text" name="testimonials_badge_text" value="<?php echo e(old('testimonials_badge_text', $publicPage['testimonials_badge_text'] ?? 'Testimonials')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimonials Heading</label>
                        <input type="text" name="testimonials_heading" value="<?php echo e(old('testimonials_heading', $publicPage['testimonials_heading'] ?? 'What Parents and Student Say')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimonials Intro</label>
                        <input type="text" name="testimonials_subheading" value="<?php echo e(old('testimonials_subheading', $publicPage['testimonials_subheading'] ?? 'We value authentic feedback from our school community. Submitted testimonials are reviewed by the admin before publication.')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimonials Form Title</label>
                        <input type="text" name="testimonials_form_title" value="<?php echo e(old('testimonials_form_title', $publicPage['testimonials_form_title'] ?? 'Share Your Testimonial')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimonials Name Label</label>
                        <input type="text" name="testimonials_form_name_label" value="<?php echo e(old('testimonials_form_name_label', $publicPage['testimonials_form_name_label'] ?? 'Full Name')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimonials Name Placeholder</label>
                        <input type="text" name="testimonials_form_name_placeholder" value="<?php echo e(old('testimonials_form_name_placeholder', $publicPage['testimonials_form_name_placeholder'] ?? 'Enter your full name')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimonials Role Label</label>
                        <input type="text" name="testimonials_form_role_label" value="<?php echo e(old('testimonials_form_role_label', $publicPage['testimonials_form_role_label'] ?? 'Role or Context')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimonials Role Placeholder</label>
                        <input type="text" name="testimonials_form_role_placeholder" value="<?php echo e(old('testimonials_form_role_placeholder', $publicPage['testimonials_form_role_placeholder'] ?? 'Parent, student, alumni, guardian, etc.')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimonials Rating Label</label>
                        <input type="text" name="testimonials_form_rating_label" value="<?php echo e(old('testimonials_form_rating_label', $publicPage['testimonials_form_rating_label'] ?? 'Rating')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimonials Message Label</label>
                        <input type="text" name="testimonials_form_message_label" value="<?php echo e(old('testimonials_form_message_label', $publicPage['testimonials_form_message_label'] ?? 'Your Testimonial')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimonials Message Placeholder</label>
                        <input type="text" name="testimonials_form_message_placeholder" value="<?php echo e(old('testimonials_form_message_placeholder', $publicPage['testimonials_form_message_placeholder'] ?? 'Write your experience with the school...')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimonials Submit Button</label>
                        <input type="text" name="testimonials_form_submit_text" value="<?php echo e(old('testimonials_form_submit_text', $publicPage['testimonials_form_submit_text'] ?? 'Submit Testimonial')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimonials Slider Title</label>
                        <input type="text" name="testimonials_slider_title" value="<?php echo e(old('testimonials_slider_title', $publicPage['testimonials_slider_title'] ?? 'Approved Testimonials')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimonials Empty Text</label>
                        <input type="text" name="testimonials_empty_text" value="<?php echo e(old('testimonials_empty_text', $publicPage['testimonials_empty_text'] ?? 'No testimonials have been approved yet. Be the first to share your experience.')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimonials Success / Error Text</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" name="testimonials_success_text" value="<?php echo e(old('testimonials_success_text', $publicPage['testimonials_success_text'] ?? 'Thank you for your testimonial. It has been submitted for admin review.')); ?>" class="w-full rounded-lg border-gray-300" placeholder="Success message">
                            <input type="text" name="testimonials_error_text" value="<?php echo e(old('testimonials_error_text', $publicPage['testimonials_error_text'] ?? 'Unable to submit testimonial. Please try again.')); ?>" class="w-full rounded-lg border-gray-300" placeholder="Error message">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quick Contact Title</label>
                        <input type="text" name="quick_contact_label" value="<?php echo e(old('quick_contact_label', $publicPage['quick_contact_label'] ?? 'Quick Contact')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Label</label>
                        <input type="text" name="contact_phone_label" value="<?php echo e(old('contact_phone_label', $publicPage['contact_phone_label'] ?? 'Phone')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Label</label>
                        <input type="text" name="contact_whatsapp_label" value="<?php echo e(old('contact_whatsapp_label', $publicPage['contact_whatsapp_label'] ?? 'WhatsApp')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Label</label>
                        <input type="text" name="contact_email_label" value="<?php echo e(old('contact_email_label', $publicPage['contact_email_label'] ?? 'Email')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address Label</label>
                        <input type="text" name="contact_address_label" value="<?php echo e(old('contact_address_label', $publicPage['contact_address_label'] ?? 'Address')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Menu Overview Suffix</label>
                        <input type="text" name="menu_overview_suffix" value="<?php echo e(old('menu_overview_suffix', $publicPage['menu_overview_suffix'] ?? 'Overview')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hero Slider Placeholder Text</label>
                        <input type="text" name="hero_slider_placeholder_text" value="<?php echo e(old('hero_slider_placeholder_text', $publicPage['hero_slider_placeholder_text'] ?? 'Upload hero slider images from Admin Settings to personalize this section.')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-gray-200 p-4 bg-gray-50">
                <h4 class="text-sm font-semibold text-gray-900">Contact, Submenu and Footer Microcopy</h4>
                <p class="text-xs text-gray-500 mt-1">Control all remaining public labels and helper text shown on contact, submenu, navbar and footer.</p>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Site Title Suffix</label>
                        <input type="text" name="site_title_suffix" value="<?php echo e(old('site_title_suffix', $publicPage['site_title_suffix'] ?? 'KG, Primary and Secondary School')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Menu Title</label>
                        <input type="text" name="mobile_menu_title" value="<?php echo e(old('mobile_menu_title', $publicPage['mobile_menu_title'] ?? 'Menu')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Quick Links Title</label>
                        <input type="text" name="footer_quick_links_title" value="<?php echo e(old('footer_quick_links_title', $publicPage['footer_quick_links_title'] ?? 'Quick Links')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Resources Title</label>
                        <input type="text" name="footer_resources_title" value="<?php echo e(old('footer_resources_title', $publicPage['footer_resources_title'] ?? 'Resources')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Contact Title</label>
                        <input type="text" name="footer_contact_title" value="<?php echo e(old('footer_contact_title', $publicPage['footer_contact_title'] ?? 'Contact')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Browser Title</label>
                        <input type="text" name="contact_page_browser_title" value="<?php echo e(old('contact_page_browser_title', $publicPage['contact_page_browser_title'] ?? 'Contact Us')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Badge Text</label>
                        <input type="text" name="contact_page_badge_text" value="<?php echo e(old('contact_page_badge_text', $publicPage['contact_page_badge_text'] ?? 'Contact Us')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Page Heading</label>
                        <input type="text" name="contact_page_heading" value="<?php echo e(old('contact_page_heading', $publicPage['contact_page_heading'] ?? 'We are here to help you')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div class="md:col-span-2 lg:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Page Subheading</label>
                        <input type="text" name="contact_page_subheading" value="<?php echo e(old('contact_page_subheading', $publicPage['contact_page_subheading'] ?? 'Send us a message and our admissions or support team will respond as soon as possible.')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Form Title</label>
                        <input type="text" name="contact_form_title" value="<?php echo e(old('contact_form_title', $publicPage['contact_form_title'] ?? 'Contact Us Form')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Name Label</label>
                        <input type="text" name="contact_form_full_name_label" value="<?php echo e(old('contact_form_full_name_label', $publicPage['contact_form_full_name_label'] ?? 'Full Name')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Name Placeholder</label>
                        <input type="text" name="contact_form_full_name_placeholder" value="<?php echo e(old('contact_form_full_name_placeholder', $publicPage['contact_form_full_name_placeholder'] ?? 'Enter your full name')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email Label</label>
                        <input type="text" name="contact_form_email_label" value="<?php echo e(old('contact_form_email_label', $publicPage['contact_form_email_label'] ?? 'Email')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email Placeholder</label>
                        <input type="text" name="contact_form_email_placeholder" value="<?php echo e(old('contact_form_email_placeholder', $publicPage['contact_form_email_placeholder'] ?? 'you@example.com')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone Label</label>
                        <input type="text" name="contact_form_phone_label" value="<?php echo e(old('contact_form_phone_label', $publicPage['contact_form_phone_label'] ?? 'Phone Number')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone Placeholder</label>
                        <input type="text" name="contact_form_phone_placeholder" value="<?php echo e(old('contact_form_phone_placeholder', $publicPage['contact_form_phone_placeholder'] ?? '+234...')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Subject Label</label>
                        <input type="text" name="contact_form_subject_label" value="<?php echo e(old('contact_form_subject_label', $publicPage['contact_form_subject_label'] ?? 'Subject')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Subject Placeholder</label>
                        <input type="text" name="contact_form_subject_placeholder" value="<?php echo e(old('contact_form_subject_placeholder', $publicPage['contact_form_subject_placeholder'] ?? 'How can we help?')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Message Label</label>
                        <input type="text" name="contact_form_message_label" value="<?php echo e(old('contact_form_message_label', $publicPage['contact_form_message_label'] ?? 'Message')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Message Placeholder</label>
                        <input type="text" name="contact_form_message_placeholder" value="<?php echo e(old('contact_form_message_placeholder', $publicPage['contact_form_message_placeholder'] ?? 'Write your message...')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Submit Button</label>
                        <input type="text" name="contact_form_submit_text" value="<?php echo e(old('contact_form_submit_text', $publicPage['contact_form_submit_text'] ?? 'Send Message')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Info Title</label>
                        <input type="text" name="contact_info_title" value="<?php echo e(old('contact_info_title', $publicPage['contact_info_title'] ?? 'Contact Information')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Missing Text</label>
                        <input type="text" name="contact_not_provided_text" value="<?php echo e(old('contact_not_provided_text', $publicPage['contact_not_provided_text'] ?? 'Not provided yet')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact More Details Title</label>
                        <input type="text" name="contact_more_details_title" value="<?php echo e(old('contact_more_details_title', $publicPage['contact_more_details_title'] ?? 'More Contact Details')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Submenu Card One Title</label>
                        <input type="text" name="submenu_highlight_one_title" value="<?php echo e(old('submenu_highlight_one_title', $publicPage['submenu_highlight_one_title'] ?? 'What Students Gain')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Submenu Card One Text</label>
                        <input type="text" name="submenu_highlight_one_text" value="<?php echo e(old('submenu_highlight_one_text', $publicPage['submenu_highlight_one_text'] ?? 'Learners receive practical support, clear expectations, and measurable progress across this focus area.')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Submenu Card Two Title</label>
                        <input type="text" name="submenu_highlight_two_title" value="<?php echo e(old('submenu_highlight_two_title', $publicPage['submenu_highlight_two_title'] ?? 'How We Deliver')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Submenu Card Two Text</label>
                        <input type="text" name="submenu_highlight_two_text" value="<?php echo e(old('submenu_highlight_two_text', $publicPage['submenu_highlight_two_text'] ?? 'Delivery is structured to be balanced and moderate, so parents and students can follow the process with confidence.')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Submenu Primary Button</label>
                        <input type="text" name="submenu_primary_button_text" value="<?php echo e(old('submenu_primary_button_text', $publicPage['submenu_primary_button_text'] ?? 'Start Admission')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Submenu Back Prefix</label>
                        <input type="text" name="submenu_back_button_prefix" value="<?php echo e(old('submenu_back_button_prefix', $publicPage['submenu_back_button_prefix'] ?? 'Back to')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Submenu Side Prefix</label>
                        <input type="text" name="submenu_more_in_prefix" value="<?php echo e(old('submenu_more_in_prefix', $publicPage['submenu_more_in_prefix'] ?? 'More In')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Map Embed Title</label>
                        <input type="text" name="map_embed_title_text" value="<?php echo e(old('map_embed_title_text', $publicPage['map_embed_title_text'] ?? 'School map')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Submenu Description Fallback Template</label>
                        <input type="text" name="submenu_description_fallback_template" value="<?php echo e(old('submenu_description_fallback_template', $publicPage['submenu_description_fallback_template'] ?? 'The {title} area gives learners and families structured support, practical guidance, and a balanced learning experience.')); ?>" class="w-full rounded-lg border-gray-300">
                        <p class="mt-1 text-xs text-gray-500">Use <code>{title}</code> where the submenu item title should appear.</p>
                    </div>
                    <div class="md:col-span-2 lg:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Status Messages</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" name="contact_status_unavailable_text" value="<?php echo e(old('contact_status_unavailable_text', $publicPage['contact_status_unavailable_text'] ?? 'Contact form is currently unavailable. Please try again later.')); ?>" class="w-full rounded-lg border-gray-300" placeholder="Form unavailable message">
                            <input type="text" name="contact_status_recipient_missing_text" value="<?php echo e(old('contact_status_recipient_missing_text', $publicPage['contact_status_recipient_missing_text'] ?? 'Contact recipient is not configured by admin yet.')); ?>" class="w-full rounded-lg border-gray-300" placeholder="Recipient missing message">
                            <input type="text" name="contact_status_send_error_text" value="<?php echo e(old('contact_status_send_error_text', $publicPage['contact_status_send_error_text'] ?? 'Message could not be sent right now. Please try again shortly.')); ?>" class="w-full rounded-lg border-gray-300" placeholder="Delivery error message">
                            <input type="text" name="contact_status_success_text" value="<?php echo e(old('contact_status_success_text', $publicPage['contact_status_success_text'] ?? 'Thank you. Your message has been received. Our team will contact you shortly.')); ?>" class="w-full rounded-lg border-gray-300" placeholder="Success message">
                        </div>
                    </div>
                </div>
            </div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Programs Intro</label>
                    <input type="text" name="programs_intro" value="<?php echo e(old('programs_intro', $publicPage['programs_intro'] ?? '')); ?>" class="w-full rounded-lg border-gray-300">
                    <label class="block text-sm font-medium text-gray-700 mt-3 mb-1">Programs Items</label>
                    <textarea name="program_items_text" rows="8" class="w-full rounded-lg border-gray-300"><?php echo e(old('program_items_text', $programItemsText)); ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Admissions Intro</label>
                    <input type="text" name="admissions_intro" value="<?php echo e(old('admissions_intro', $publicPage['admissions_intro'] ?? '')); ?>" class="w-full rounded-lg border-gray-300">
                    <label class="block text-sm font-medium text-gray-700 mt-3 mb-1">Admissions Items</label>
                    <textarea name="admissions_items_text" rows="8" class="w-full rounded-lg border-gray-300"><?php echo e(old('admissions_items_text', $admissionsItemsText)); ?></textarea>
                </div>                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Academics Intro</label>
                    <input type="text" name="academics_intro" value="<?php echo e(old('academics_intro', $publicPage['academics_intro'] ?? 'A Structured Learning Culture With Mentorship At The Center.')); ?>" class="w-full rounded-lg border-gray-300">
                    <label class="block text-sm font-medium text-gray-700 mt-3 mb-1">Academics Context Text</label>
                    <textarea name="academics_support_text" rows="4" class="w-full rounded-lg border-gray-300"><?php echo e(old('academics_support_text', $publicPage['academics_support_text'] ?? 'Our school culture is built around consistent learning outcomes, high accountability, and teacher-student mentorship that develops confidence and character.')); ?></textarea>

                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Highlight Card 1 Title</label>
                            <input type="text" name="academic_highlight_1_title" value="<?php echo e(old('academic_highlight_1_title', $publicPage['academic_highlights'][0]['title'] ?? 'STEM-First Curriculum')); ?>" class="w-full rounded-lg border-gray-300 mb-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Highlight Card 1 Context</label>
                            <textarea name="academic_highlight_1_description" rows="3" class="w-full rounded-lg border-gray-300"><?php echo e(old('academic_highlight_1_description', $publicPage['academic_highlights'][0]['description'] ?? 'Coding, robotics, and science labs integrated into junior and senior classes.')); ?></textarea>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Highlight Card 2 Title</label>
                            <input type="text" name="academic_highlight_2_title" value="<?php echo e(old('academic_highlight_2_title', $publicPage['academic_highlights'][1]['title'] ?? 'Student Leadership')); ?>" class="w-full rounded-lg border-gray-300 mb-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Highlight Card 2 Context</label>
                            <textarea name="academic_highlight_2_description" rows="3" class="w-full rounded-lg border-gray-300"><?php echo e(old('academic_highlight_2_description', $publicPage['academic_highlights'][1]['description'] ?? 'Public speaking, media, and entrepreneurship clubs with measurable outcomes.')); ?></textarea>
                        </div>
                    </div>

                    <label class="block text-sm font-medium text-gray-700 mt-3 mb-1">Academics Items (Legacy)</label>
                    <textarea name="academics_items_text" rows="8" class="w-full rounded-lg border-gray-300"><?php echo e(old('academics_items_text', $academicsItemsText)); ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Facilities Intro</label>
                    <input type="text" name="facilities_intro" value="<?php echo e(old('facilities_intro', $publicPage['facilities_intro'] ?? '')); ?>" class="w-full rounded-lg border-gray-300">
                    <label class="block text-sm font-medium text-gray-700 mt-3 mb-1">Facilities Items (one item per line)</label>
                    <textarea name="facilities" rows="8" class="w-full rounded-lg border-gray-300"><?php echo e(old('facilities', $facilitiesText)); ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">About Us Intro</label>
                    <input type="text" name="about_intro" value="<?php echo e(old('about_intro', $publicPage['about_intro'] ?? '')); ?>" class="w-full rounded-lg border-gray-300">
                    <p class="mt-3 rounded-lg border border-indigo-100 bg-indigo-50 px-3 py-2 text-xs text-indigo-700">
                        About cards now use the banner manager below (full image + bold text overlay). Legacy items are preserved automatically.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student Life Intro</label>
                    <input type="text" name="student_life_intro" value="<?php echo e(old('student_life_intro', $publicPage['student_life_intro'] ?? '')); ?>" class="w-full rounded-lg border-gray-300">
                    <label class="block text-sm font-medium text-gray-700 mt-3 mb-1">Student Life Items</label>
                    <textarea name="student_life_items_text" rows="8" class="w-full rounded-lg border-gray-300"><?php echo e(old('student_life_items_text', $studentLifeItemsText)); ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Parents Intro</label>
                    <input type="text" name="parents_intro" value="<?php echo e(old('parents_intro', $publicPage['parents_intro'] ?? '')); ?>" class="w-full rounded-lg border-gray-300">
                    <p class="mt-3 rounded-lg border border-indigo-100 bg-indigo-50 px-3 py-2 text-xs text-indigo-700">
                        Parents cards now use the banner manager below (image + bold title + context text). Existing legacy text items are preserved automatically.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Intro</label>
                    <input type="text" name="contact_intro" value="<?php echo e(old('contact_intro', $publicPage['contact_intro'] ?? '')); ?>" class="w-full rounded-lg border-gray-300">
                    <label class="block text-sm font-medium text-gray-700 mt-3 mb-1">Contact Items</label>
                    <textarea name="contact_items_text" rows="8" class="w-full rounded-lg border-gray-300"><?php echo e(old('contact_items_text', $contactItemsText)); ?></textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Why Choose Us Section Label</label>
                    <input type="text" name="why_choose_us_label" value="<?php echo e(old('why_choose_us_label', $publicPage['why_choose_us_label'] ?? 'Why Choose Us')); ?>" class="w-full rounded-lg border-gray-300">
                    <label class="block text-sm font-medium text-gray-700 mt-3 mb-1">Why Choose Us Intro Text</label>
                    <input type="text" name="why_choose_us_intro" value="<?php echo e(old('why_choose_us_intro', $publicPage['why_choose_us_intro'] ?? '')); ?>" class="w-full rounded-lg border-gray-300" placeholder="Optional text shown under section label">
                    <p class="mt-3 rounded-lg border border-indigo-100 bg-indigo-50 px-3 py-2 text-xs text-indigo-700">
                        Use the Why Choose Us Banner Manager below to control card image, title, and context text.
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Admission Steps (one step per line)</label>
                    <textarea name="admission_steps" rows="6" class="w-full rounded-lg border-gray-300"><?php echo e(old('admission_steps', $admissionStepsText)); ?></textarea>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number</label>
                        <input type="text" name="whatsapp" value="<?php echo e(old('whatsapp', $publicPage['whatsapp'] ?? '')); ?>" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Visit Booking URL</label>
                        <input type="url" name="visit_booking_url" value="<?php echo e(old('visit_booking_url', $publicPage['visit_booking_url'] ?? '')); ?>" class="w-full rounded-lg border-gray-300" placeholder="https://">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Map Embed URL</label>
                        <input type="url" name="map_embed_url" value="<?php echo e(old('map_embed_url', $publicPage['map_embed_url'] ?? '')); ?>" class="w-full rounded-lg border-gray-300" placeholder="https://maps.google.com/...">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php for($i = 1; $i <= 3; $i++): ?>
                    <?php $slide = $publicPage['hero_slides'][$i - 1] ?? null; ?>
                    <div class="rounded-xl border border-gray-200 p-4 bg-gray-50">
                        <h5 class="text-sm font-semibold text-gray-900 mb-3">Hero Slide <?php echo e($i); ?></h5>
                        <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Image</label>
                        <input type="file" name="hero_slide_<?php echo e($i); ?>" accept="image/*" class="w-full rounded-lg border-gray-300 mb-3">

                        <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Caption</label>
                        <input type="text" name="hero_slide_<?php echo e($i); ?>_caption" value="<?php echo e(old('hero_slide_' . $i . '_caption', $slide['caption'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 mb-3">

                        <label class="inline-flex items-center text-xs text-red-600">
                            <input type="checkbox" name="remove_hero_slide_<?php echo e($i); ?>" value="1" class="rounded border-gray-300 mr-2">
                            Remove this slide
                        </label>

                        <div class="mt-3">
                            <p class="text-xs text-gray-500 mb-1">Current preview</p>
                            <?php if(!empty($slide['path'])): ?>
                                <img src="<?php echo e(asset('storage/' . ltrim($slide['path'], '/'))); ?>" alt="Hero Slide <?php echo e($i); ?>" class="h-24 w-full rounded-lg object-cover border border-gray-200">
                            <?php else: ?>
                                <div class="h-24 w-full rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-xs text-gray-400">
                                    No image
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
            <div class="rounded-xl border border-gray-200 p-4 bg-gray-50 space-y-4">
                <h4 class="text-sm font-semibold text-gray-900">Academic Visual Manager</h4>
                <p class="text-xs text-gray-500">Upload the two right-side images used in the Academic Excellence section on the homepage.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php for($i = 1; $i <= 2; $i++): ?>
                        <?php $slot = $academicsVisualSlots[$i - 1] ?? ['image' => null]; ?>
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <h5 class="text-sm font-semibold text-gray-900 mb-3">Academic Image <?php echo e($i); ?></h5>

                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Image</label>
                            <input type="file" name="academic_image_<?php echo e($i); ?>" accept="image/*" class="w-full rounded-lg border-gray-300 mb-3">

                            <label class="inline-flex items-center text-xs text-red-600">
                                <input type="checkbox" name="remove_academic_image_<?php echo e($i); ?>" value="1" class="rounded border-gray-300 mr-2">
                                Remove this image
                            </label>

                            <div class="mt-3">
                                <p class="text-xs text-gray-500 mb-1">Current preview</p>
                                <?php if(!empty($slot['image'])): ?>
                                    <img src="<?php echo e(asset('storage/' . ltrim($slot['image'], '/'))); ?>" alt="Academic Image <?php echo e($i); ?>" class="h-32 w-full rounded-lg object-cover border border-gray-200">
                                <?php else: ?>
                                    <div class="h-32 w-full rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-xs text-gray-400">
                                        No image
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 p-4 bg-gray-50 space-y-4">
                <h4 class="text-sm font-semibold text-gray-900">Why Choose Us Banner Manager</h4>
                <p class="text-xs text-gray-500">Upload full-size image banners and provide title + context text for the Why Choose Us section.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <?php for($i = 1; $i <= 4; $i++): ?>
                        <?php $banner = $whyChooseUsBannerSlots[$i - 1] ?? ['image' => null, 'title' => '', 'description' => '']; ?>
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <h5 class="text-sm font-semibold text-gray-900 mb-3">Why Banner <?php echo e($i); ?></h5>

                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Image</label>
                            <input type="file" name="why_banner_<?php echo e($i); ?>_image" accept="image/*" class="w-full rounded-lg border-gray-300 mb-3">

                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Banner Title (Bold)</label>
                            <input type="text" name="why_banner_<?php echo e($i); ?>_title" value="<?php echo e(old('why_banner_' . $i . '_title', $banner['title'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 mb-3">

                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Banner Context Text</label>
                            <textarea name="why_banner_<?php echo e($i); ?>_description" rows="3" class="w-full rounded-lg border-gray-300 mb-3"><?php echo e(old('why_banner_' . $i . '_description', $banner['description'] ?? '')); ?></textarea>

                            <label class="inline-flex items-center text-xs text-red-600">
                                <input type="checkbox" name="remove_why_banner_<?php echo e($i); ?>" value="1" class="rounded border-gray-300 mr-2">
                                Remove this banner image
                            </label>

                            <div class="mt-3">
                                <p class="text-xs text-gray-500 mb-1">Current preview</p>
                                <?php if(!empty($banner['image'])): ?>
                                    <img src="<?php echo e(asset('storage/' . ltrim($banner['image'], '/'))); ?>" alt="Why Banner <?php echo e($i); ?>" class="h-24 w-full rounded-lg object-cover border border-gray-200">
                                <?php else: ?>
                                    <div class="h-24 w-full rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-xs text-gray-400">
                                        No image
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 p-4 bg-gray-50 space-y-4">
                <h4 class="text-sm font-semibold text-gray-900">About Us Banner Manager</h4>
                <p class="text-xs text-gray-500">Upload full-size image banners and provide title + context text for the About Us section.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php for($i = 1; $i <= 6; $i++): ?>
                        <?php $banner = $aboutBannerSlots[$i - 1] ?? ['image' => null, 'title' => '', 'description' => '']; ?>
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <h5 class="text-sm font-semibold text-gray-900 mb-3">About Banner <?php echo e($i); ?></h5>

                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Image</label>
                            <input type="file" name="about_banner_<?php echo e($i); ?>_image" accept="image/*" class="w-full rounded-lg border-gray-300 mb-3">

                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Banner Title (Bold)</label>
                            <input type="text" name="about_banner_<?php echo e($i); ?>_title" value="<?php echo e(old('about_banner_' . $i . '_title', $banner['title'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 mb-3">

                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Banner Context Text</label>
                            <textarea name="about_banner_<?php echo e($i); ?>_description" rows="3" class="w-full rounded-lg border-gray-300 mb-3"><?php echo e(old('about_banner_' . $i . '_description', $banner['description'] ?? '')); ?></textarea>

                            <label class="inline-flex items-center text-xs text-red-600">
                                <input type="checkbox" name="remove_about_banner_<?php echo e($i); ?>" value="1" class="rounded border-gray-300 mr-2">
                                Remove this banner image
                            </label>

                            <div class="mt-3">
                                <p class="text-xs text-gray-500 mb-1">Current preview</p>
                                <?php if(!empty($banner['image'])): ?>
                                    <img src="<?php echo e(asset('storage/' . ltrim($banner['image'], '/'))); ?>" alt="About Banner <?php echo e($i); ?>" class="h-24 w-full rounded-lg object-cover border border-gray-200">
                                <?php else: ?>
                                    <div class="h-24 w-full rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-xs text-gray-400">
                                        No image
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="rounded-xl border border-gray-200 p-4 bg-gray-50 space-y-4">
                <h4 class="text-sm font-semibold text-gray-900">Parents Banner Manager</h4>
                <p class="text-xs text-gray-500">Upload banner images and add bold title + context text for the Parents section on the homepage.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php for($i = 1; $i <= 6; $i++): ?>
                        <?php $banner = $parentsBannerSlots[$i - 1] ?? ['image' => null, 'title' => '', 'description' => '']; ?>
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <h5 class="text-sm font-semibold text-gray-900 mb-3">Parents Banner <?php echo e($i); ?></h5>

                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Image</label>
                            <input type="file" name="parent_banner_<?php echo e($i); ?>_image" accept="image/*" class="w-full rounded-lg border-gray-300 mb-3">

                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Banner Title (Bold)</label>
                            <input type="text" name="parent_banner_<?php echo e($i); ?>_title" value="<?php echo e(old('parent_banner_' . $i . '_title', $banner['title'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 mb-3">

                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Banner Context Text</label>
                            <textarea name="parent_banner_<?php echo e($i); ?>_description" rows="3" class="w-full rounded-lg border-gray-300 mb-3"><?php echo e(old('parent_banner_' . $i . '_description', $banner['description'] ?? '')); ?></textarea>

                            <label class="inline-flex items-center text-xs text-red-600">
                                <input type="checkbox" name="remove_parent_banner_<?php echo e($i); ?>" value="1" class="rounded border-gray-300 mr-2">
                                Remove this banner image
                            </label>

                            <div class="mt-3">
                                <p class="text-xs text-gray-500 mb-1">Current preview</p>
                                <?php if(!empty($banner['image'])): ?>
                                    <img src="<?php echo e(asset('storage/' . ltrim($banner['image'], '/'))); ?>" alt="Parents Banner <?php echo e($i); ?>" class="h-24 w-full rounded-lg object-cover border border-gray-200">
                                <?php else: ?>
                                    <div class="h-24 w-full rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-xs text-gray-400">
                                        No image
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="rounded-xl border border-gray-200 p-4 bg-gray-50 space-y-4">
                <h4 class="text-sm font-semibold text-gray-900">Footer Content Manager</h4>
                <p class="text-xs text-gray-500">All footer content is controlled here. Links format: <span class="font-medium">Label | URL</span>.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Logo</label>
                        <input type="file" name="footer_logo" accept="image/*" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <label class="inline-flex items-center mt-2 text-sm text-red-600">
                            <input type="checkbox" name="remove_footer_logo" value="1" class="rounded border-gray-300 mr-2">
                            Remove footer logo
                        </label>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-1">Footer Logo Preview</p>
                        <?php if(!empty($publicPage['footer_logo'])): ?>
                            <img src="<?php echo e(asset('storage/' . ltrim($publicPage['footer_logo'], '/'))); ?>" alt="Footer Logo" class="h-20 w-20 rounded-lg object-cover border border-gray-200 bg-white">
                        <?php elseif(!empty($school->logo)): ?>
                            <img src="<?php echo e(asset('storage/' . ltrim($school->logo, '/'))); ?>" alt="School Logo" class="h-20 w-20 rounded-lg object-cover border border-gray-200 bg-white">
                        <?php else: ?>
                            <div class="h-20 w-20 rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-xs text-gray-400">No logo</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Footer Description</label>
                    <textarea name="footer_description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"><?php echo e(old('footer_description', $publicPage['footer_description'] ?? '')); ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Contact Address</label>
                        <input type="text" name="footer_contact_address" value="<?php echo e(old('footer_contact_address', $publicPage['footer_contact_address'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Contact Phone</label>
                        <input type="text" name="footer_contact_phone" value="<?php echo e(old('footer_contact_phone', $publicPage['footer_contact_phone'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Contact Email</label>
                        <input type="email" name="footer_contact_email" value="<?php echo e(old('footer_contact_email', $publicPage['footer_contact_email'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Quick Links</label>
                        <textarea name="footer_quick_links_text" rows="6" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"><?php echo e(old('footer_quick_links_text', $footerQuickLinksText)); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Resources</label>
                        <textarea name="footer_resources_text" rows="6" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"><?php echo e(old('footer_resources_text', $footerResourcesText)); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Social Links</label>
                        <textarea name="footer_social_links_text" rows="6" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"><?php echo e(old('footer_social_links_text', $footerSocialLinksText)); ?></textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Footer Note</label>
                    <input type="text" name="footer_note" value="<?php echo e(old('footer_note', $publicPage['footer_note'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="inline-flex items-center px-5 py-2.5 rounded-lg bg-gray-900 text-white text-sm font-semibold transition duration-200 hover:-translate-y-0.5 hover:bg-black">
                    Save Public Homepage Content
                </button>
                <button
                    type="submit"
                    form="reset-theme-form"
                    class="inline-flex items-center px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 transition duration-200 hover:-translate-y-0.5 hover:border-gray-400 hover:text-gray-900"
                    onclick="return confirm('Reset header and footer colors to the default school theme?');"
                >
                    Reset Theme To Default
                </button>
            </div>
        </form>
        <form id="reset-theme-form" action="<?php echo e(route('settings.public-page.reset-theme')); ?>" method="POST" class="hidden">
            <?php echo csrf_field(); ?>
        </form>
    </section>

    <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900">System Preferences</h3>
        <p class="text-sm text-gray-500 mt-1">Operational settings for results and communication.</p>

        <form action="<?php echo e(route('settings.system')); ?>" method="POST" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <?php
                $system = $school->settings ?? [];
                $smtp = $system['smtp'] ?? [];
            ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Grading System</label>
                <select name="grading_system" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Select grading system</option>
                    <option value="waec" <?php echo e(old('grading_system', $system['grading_system'] ?? '') === 'waec' ? 'selected' : ''); ?>>WAEC</option>
                    <option value="custom" <?php echo e(old('grading_system', $system['grading_system'] ?? '') === 'custom' ? 'selected' : ''); ?>>Custom</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Currency Symbol</label>
                <input type="text" name="currency_symbol" value="<?php echo e(old('currency_symbol', $system['currency_symbol'] ?? 'NGN')); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-3">
                <label class="inline-flex items-center text-sm text-gray-700">
                    <input type="checkbox" name="result_approval_required" value="1" class="rounded border-gray-300 mr-2" <?php echo e(old('result_approval_required', $system['result_approval_required'] ?? false) ? 'checked' : ''); ?>>
                    Result approval required
                </label>
                <label class="inline-flex items-center text-sm text-gray-700">
                    <input type="checkbox" name="online_admission_enabled" value="1" class="rounded border-gray-300 mr-2" <?php echo e(old('online_admission_enabled', $system['online_admission_enabled'] ?? true) ? 'checked' : ''); ?>>
                    Online admission enabled
                </label>
                <label class="inline-flex items-center text-sm text-gray-700">
                    <input type="checkbox" name="sms_notifications_enabled" value="1" class="rounded border-gray-300 mr-2" <?php echo e(old('sms_notifications_enabled', $system['sms_notifications_enabled'] ?? false) ? 'checked' : ''); ?>>
                    SMS notifications enabled
                </label>
                <label class="inline-flex items-center text-sm text-gray-700">
                    <input type="checkbox" name="email_notifications_enabled" value="1" class="rounded border-gray-300 mr-2" <?php echo e(old('email_notifications_enabled', $system['email_notifications_enabled'] ?? false) ? 'checked' : ''); ?>>
                    Email notifications enabled
                </label>
            </div>

            <div class="md:col-span-2 mt-2 rounded-xl border border-gray-200 bg-gray-50 p-4">
                <h4 class="text-sm font-semibold text-gray-900">SMTP Setup (Admin Controlled)</h4>
                <p class="mt-1 text-xs text-gray-500">These credentials are used for Contact Us form email delivery.</p>

                <div class="mt-4">
                    <label class="inline-flex items-center text-sm text-gray-700">
                        <input type="checkbox" name="smtp_enabled" value="1" class="rounded border-gray-300 mr-2" <?php echo e(old('smtp_enabled', $smtp['enabled'] ?? false) ? 'checked' : ''); ?>>
                        Enable SMTP sending for Contact Us form
                    </label>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                        <input type="text" name="smtp_host" value="<?php echo e(old('smtp_host', $smtp['host'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="smtp.example.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Port</label>
                        <input type="number" name="smtp_port" value="<?php echo e(old('smtp_port', $smtp['port'] ?? 587)); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="587">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                        <select name="smtp_encryption" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <?php $smtpEncryption = old('smtp_encryption', $smtp['encryption'] ?? 'tls'); ?>
                            <option value="tls" <?php echo e($smtpEncryption === 'tls' ? 'selected' : ''); ?>>TLS</option>
                            <option value="ssl" <?php echo e($smtpEncryption === 'ssl' ? 'selected' : ''); ?>>SSL</option>
                            <option value="none" <?php echo e($smtpEncryption === 'none' ? 'selected' : ''); ?>>None</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Username</label>
                        <input type="text" name="smtp_username" value="<?php echo e(old('smtp_username', $smtp['username'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Password</label>
                        <input type="password" name="smtp_password" value="" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Leave blank to keep existing password">
                        <p class="mt-1 text-xs text-gray-500">Leave blank to retain the current saved password.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Email</label>
                        <input type="email" name="smtp_from_address" value="<?php echo e(old('smtp_from_address', $smtp['from_address'] ?? '')); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="noreply@school.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Name</label>
                        <input type="text" name="smtp_from_name" value="<?php echo e(old('smtp_from_name', $smtp['from_name'] ?? ($school->name ?? ''))); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="School Name">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Recipient Email</label>
                        <input type="email" name="smtp_to_address" value="<?php echo e(old('smtp_to_address', $smtp['to_address'] ?? ($school->email ?? ''))); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="admissions@school.com">
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="inline-flex items-center px-5 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-semibold transition duration-200 hover:-translate-y-0.5 hover:bg-emerald-700">
                    Save System Preferences
                </button>
            </div>
        </form>

        <?php if($errors->has('smtp_test')): ?>
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                <?php echo e($errors->first('smtp_test')); ?>

            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('settings.smtp-test')); ?>" method="POST" class="mt-4 grid grid-cols-1 md:grid-cols-[1fr_auto] gap-3 rounded-xl border border-gray-200 bg-gray-50 p-4">
            <?php echo csrf_field(); ?>
            <?php $smtp = $system['smtp'] ?? []; ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Test Recipient Email</label>
                <input type="email" name="smtp_test_recipient" value="<?php echo e(old('smtp_test_recipient', $smtp['to_address'] ?? ($school->email ?? ''))); ?>" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="recipient@school.com">
            </div>
            <div class="md:self-end">
                <button type="submit" class="inline-flex items-center px-5 py-2.5 rounded-lg bg-gray-900 text-white text-sm font-semibold transition duration-200 hover:-translate-y-0.5 hover:bg-black">
                    Send Test SMTP Email
                </button>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>









<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views\system\settings.blade.php ENDPATH**/ ?>