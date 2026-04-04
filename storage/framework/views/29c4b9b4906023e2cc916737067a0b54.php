<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Apply for Admission — <?php echo e($school?->name ?? 'Our School'); ?></title>
    <?php if($faviconPath): ?>
        <link rel="icon" type="image/png" href="<?php echo e(asset('storage/' . ltrim($faviconPath, '/'))); ?>">
    <?php endif; ?>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo $__env->make('public.partials.nav-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        * { font-family: 'Manrope', sans-serif; box-sizing: border-box; }
        :root {
            --primary: <?php echo e($primary); ?>;
            --secondary: <?php echo e($secondary); ?>;
            --hover-text: <?php echo e(data_get($theme, 'primary_text_on_secondary', '#2D1D5C')); ?>;
        }
        .field-input {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.875rem;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            color: #1e293b;
            background: #fff;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .field-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 15%, transparent); }
        .field-input.error { border-color: #ef4444; background: #fff5f5; }
        .field-label { display: block; font-size: 0.8125rem; font-weight: 700; color: #475569; margin-bottom: 0.375rem; text-transform: uppercase; letter-spacing: 0.06em; }
        .step-panel { display: none; }
        .step-panel.active { display: block; }
        .step-circle { width: 2.25rem; height: 2.25rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 800; border: 2px solid #e2e8f0; color: #94a3b8; background: #fff; transition: all 0.3s; flex-shrink: 0; }
        .step-circle.active { background: var(--primary); border-color: var(--primary); color: #fff; }
        .step-circle.done { background: #22c55e; border-color: #22c55e; color: #fff; }
        .step-line { flex: 1; height: 2px; background: #e2e8f0; transition: background 0.3s; }
        .step-line.done { background: #22c55e; }
        .btn-primary { background: var(--primary); color: #fff; border: none; border-radius: 9999px; padding: 0.75rem 2rem; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-primary:hover { filter: brightness(1.1); transform: translateY(-1px); }
        .btn-outline { background: #fff; color: #475569; border: 1.5px solid #e2e8f0; border-radius: 9999px; padding: 0.75rem 2rem; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); transform: translateY(-1px); }
        .review-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem 1rem; }
        @media(max-width:640px) { .review-row { grid-template-columns: 1fr; } }
        .file-drop { border: 2px dashed #cbd5e1; border-radius: 1rem; padding: 1.5rem; text-align: center; cursor: pointer; transition: border-color 0.2s, background 0.2s; }
        .file-drop:hover { border-color: var(--primary); background: color-mix(in srgb, var(--primary) 4%, transparent); }
        .error-msg { display: none; color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; font-weight: 600; }
        .error-msg.show { display: block; }

        /* Grid pattern background */
        body {
            background-color: <?php echo e($bg); ?>;
            background-image:
                linear-gradient(<?php echo e($primary); ?>18 1px, transparent 1px),
                linear-gradient(90deg, <?php echo e($primary); ?>18 1px, transparent 1px);
            background-size: 32px 32px;
        }
    </style>
</head>
<body style="min-height:100vh;color:<?php echo e($muted); ?>;">

<?php echo $__env->make('public.partials.nav', ['school' => $school, 'publicPage' => $publicPage, 'theme' => $theme], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="max-w-3xl mx-auto px-4 py-10">

    
    <div class="text-center mb-8">
        <span class="inline-block rounded-full px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-white mb-4" style="background:<?php echo e($primary); ?>;">Online Admission</span>
        <h1 class="text-3xl font-extrabold" style="color:<?php echo e($ink); ?>;">Apply for Admission</h1>
        <p class="mt-2 text-sm" style="color:<?php echo e($muted); ?>;">Complete all sections carefully. Fields marked <span class="text-red-500 font-bold">*</span> are required.</p>
    </div>

    
    <?php if($errors->any()): ?>
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
        <p class="text-sm font-bold text-red-700 mb-2">Please fix the following errors:</p>
        <ul class="list-disc list-inside space-y-1">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="text-sm text-red-600"><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    
    <div class="mb-8 flex items-center px-2" id="step-indicator">
        <?php $stepLabels = ['Student','Parent','Location','Documents','Review']; ?>
        <?php $__currentLoopData = $stepLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex flex-col items-center gap-1" style="flex:0 0 auto;">
                <div class="step-circle <?php echo e($i === 0 ? 'active' : ''); ?>" id="circle-<?php echo e($i + 1); ?>">
                    <span id="circle-label-<?php echo e($i + 1); ?>"><?php echo e($i + 1); ?></span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wide hidden sm:block" style="color:<?php echo e($muted); ?>;"><?php echo e($label); ?></span>
            </div>
            <?php if(!$loop->last): ?>
            <div class="step-line" id="line-<?php echo e($i + 1); ?>"></div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <p class="text-xs font-semibold text-center mb-6" style="color:<?php echo e($muted); ?>;" id="step-label-text">Step 1 of 5 — Student Information</p>

    
    <form id="admission-form" action="<?php echo e(route('admission.apply.store')); ?>" method="POST" enctype="multipart/form-data" novalidate>
        <?php echo csrf_field(); ?>
        <input type="hidden" name="school_id" value="<?php echo e($school?->id); ?>">
        
        <div aria-hidden="true" style="position:absolute;left:-9999px;overflow:hidden;height:0;opacity:0;pointer-events:none;">
            <input type="text" name="hp_website" id="hp_website" value="" autocomplete="off" tabindex="-1">
        </div>

        
        <div class="step-panel active" id="panel-1">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-7">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl text-white text-sm font-bold" style="background:<?php echo e($primary); ?>;">1</span>
                    <div>
                        <h2 class="text-base font-bold" style="color:<?php echo e($ink); ?>;">Student Information</h2>
                        <p class="text-xs mt-0.5" style="color:<?php echo e($muted); ?>;">Personal details of the applicant</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="field-label">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" id="f_first_name" value="<?php echo e(old('first_name')); ?>" class="field-input" placeholder="e.g. Chisom" autocomplete="given-name" required>
                        <span class="error-msg" id="err-first_name">First name is required</span>
                    </div>
                    <div>
                        <label class="field-label">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" id="f_last_name" value="<?php echo e(old('last_name')); ?>" class="field-input" placeholder="e.g. Okafor" autocomplete="family-name" required>
                        <span class="error-msg" id="err-last_name">Last name is required</span>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="field-label">Other Names</label>
                        <input type="text" name="other_names" value="<?php echo e(old('other_names')); ?>" class="field-input" placeholder="Middle name or other names (optional)" autocomplete="additional-name">
                    </div>
                    <div>
                        <label class="field-label">Gender <span class="text-red-500">*</span></label>
                        <select name="gender" id="f_gender" class="field-input" required>
                            <option value="">— Select Gender —</option>
                            <option value="male"   <?php echo e(old('gender') === 'male'   ? 'selected' : ''); ?>>Male</option>
                            <option value="female" <?php echo e(old('gender') === 'female' ? 'selected' : ''); ?>>Female</option>
                        </select>
                        <span class="error-msg" id="err-gender">Please select gender</span>
                    </div>
                    <div>
                        <label class="field-label">Date of Birth <span class="text-red-500">*</span></label>
                        <input type="date" name="date_of_birth" id="f_date_of_birth" value="<?php echo e(old('date_of_birth')); ?>" max="<?php echo e(date('Y-m-d', strtotime('-1 year'))); ?>" class="field-input" required>
                        <span class="error-msg" id="err-date_of_birth">Date of birth is required</span>
                    </div>
                    <div>
                        <label class="field-label">Class Applying For <span class="text-red-500">*</span></label>
                        <input type="text" name="class_applied_for" id="f_class_applied_for" value="<?php echo e(old('class_applied_for')); ?>" class="field-input" placeholder="e.g. JSS 1, Primary 3, KG 2" required>
                        <span class="error-msg" id="err-class_applied_for">Class is required</span>
                    </div>
                    <div>
                        <label class="field-label">Previous School</label>
                        <input type="text" name="previous_school" value="<?php echo e(old('previous_school')); ?>" class="field-input" placeholder="Name of last school attended (optional)">
                    </div>
                </div>
            </div>
        </div>

        
        <div class="step-panel" id="panel-2">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-7">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl text-white text-sm font-bold" style="background:<?php echo e($primary); ?>;">2</span>
                    <div>
                        <h2 class="text-base font-bold" style="color:<?php echo e($ink); ?>;">Parent / Guardian Information</h2>
                        <p class="text-xs mt-0.5" style="color:<?php echo e($muted); ?>;">Contact details of parent or guardian</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="field-label">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="parent_name" id="f_parent_name" value="<?php echo e(old('parent_name')); ?>" class="field-input" placeholder="e.g. Mr. Emmanuel Okafor" autocomplete="name" required>
                        <span class="error-msg" id="err-parent_name">Parent/guardian name is required</span>
                    </div>
                    <div>
                        <label class="field-label">Phone Number <span class="text-red-500">*</span></label>
                        <input type="tel" name="parent_phone" id="f_parent_phone" value="<?php echo e(old('parent_phone')); ?>" class="field-input" placeholder="e.g. 08012345678" autocomplete="tel" required>
                        <span class="error-msg" id="err-parent_phone">A valid phone number is required</span>
                    </div>
                    <div>
                        <label class="field-label">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="parent_email" id="f_parent_email" value="<?php echo e(old('parent_email')); ?>" class="field-input" placeholder="e.g. parent@example.com" autocomplete="email" required>
                        <span class="error-msg" id="err-parent_email">A valid email address is required</span>
                        <p class="text-xs mt-1.5" style="color:<?php echo e($muted); ?>;">
                            <svg class="inline h-3 w-3 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Admission status updates will be sent to this email
                        </p>
                    </div>
                    <div>
                        <label class="field-label">Relationship to Student</label>
                        <select name="parent_relationship" class="field-input">
                            <option value="">— Select —</option>
                            <option value="Father" <?php echo e(old('parent_relationship') === 'Father' ? 'selected' : ''); ?>>Father</option>
                            <option value="Mother" <?php echo e(old('parent_relationship') === 'Mother' ? 'selected' : ''); ?>>Mother</option>
                            <option value="Guardian" <?php echo e(old('parent_relationship') === 'Guardian' ? 'selected' : ''); ?>>Guardian</option>
                            <option value="Other" <?php echo e(old('parent_relationship') === 'Other' ? 'selected' : ''); ?>>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="field-label">Occupation</label>
                        <input type="text" name="parent_occupation" value="<?php echo e(old('parent_occupation')); ?>" class="field-input" placeholder="e.g. Business Owner, Teacher">
                    </div>
                </div>
            </div>
        </div>

        
        <div class="step-panel" id="panel-3">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-7">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl text-white text-sm font-bold" style="background:<?php echo e($primary); ?>;">3</span>
                    <div>
                        <h2 class="text-base font-bold" style="color:<?php echo e($ink); ?>;">Location &amp; State of Origin</h2>
                        <p class="text-xs mt-0.5" style="color:<?php echo e($muted); ?>;">Residential and origin information</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="field-label">State of Origin <span class="text-red-500">*</span></label>
                        <select name="state_of_origin" id="f_state_of_origin" class="field-input" required onchange="populateLgas(this.value)">
                            <option value="">— Select State —</option>
                            <?php $__currentLoopData = array_keys($states); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($st); ?>" <?php echo e(old('state_of_origin') === $st ? 'selected' : ''); ?>><?php echo e($st); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <span class="error-msg" id="err-state_of_origin">Please select a state</span>
                    </div>
                    <div>
                        <label class="field-label">Local Government Area <span class="text-red-500">*</span></label>
                        <select name="lga" id="f_lga" class="field-input" required disabled>
                            <option value="">— Select State First —</option>
                            <?php if(old('state_of_origin') && old('lga')): ?>
                                <?php $__currentLoopData = \App\Support\NigeriaData::lgasFor(old('state_of_origin')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($l); ?>" <?php echo e(old('lga') === $l ? 'selected' : ''); ?>><?php echo e($l); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </select>
                        <span class="error-msg" id="err-lga">Please select a local government area</span>
                    </div>
                    <div>
                        <label class="field-label">Nationality</label>
                        <input type="text" name="nationality" value="<?php echo e(old('nationality', 'Nigerian')); ?>" class="field-input" placeholder="e.g. Nigerian">
                    </div>
                    <div>
                        <label class="field-label">City / Town</label>
                        <input type="text" name="city" value="<?php echo e(old('city')); ?>" class="field-input" placeholder="e.g. Lagos, Enugu">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="field-label">Home Address</label>
                        <textarea name="address" rows="3" class="field-input" placeholder="Full residential address of parent/guardian" style="resize:vertical;"><?php echo e(old('address')); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="step-panel" id="panel-4">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-7">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl text-white text-sm font-bold" style="background:<?php echo e($primary); ?>;">4</span>
                    <div>
                        <h2 class="text-base font-bold" style="color:<?php echo e($ink); ?>;">Supporting Documents</h2>
                        <p class="text-xs mt-0.5" style="color:<?php echo e($muted); ?>;">All files are optional. Max sizes: photos 2MB, documents 5MB</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800 font-medium mb-6">
                    <strong>Accepted formats:</strong> JPG, PNG, WEBP for photos · PDF or image for documents
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <?php $__currentLoopData = [['photo','Passport Photograph','image/*','image/jpeg,image/png,image/webp','2MB'],['birth_certificate','Birth Certificate','.pdf,image/*','.pdf,image/jpeg,image/png','5MB'],['previous_result','Previous School Result','.pdf,image/*','.pdf,image/jpeg,image/png','5MB']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$name,$label,$accept,$mimes,$size]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <label class="field-label mb-2"><?php echo e($label); ?></label>
                        <div class="file-drop" onclick="document.getElementById('file_<?php echo e($name); ?>').click()">
                            <svg class="mx-auto mb-2 h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.338-2.32 5.75 5.75 0 011.988 11.095H6.75z"/>
                            </svg>
                            <p class="text-xs font-semibold text-slate-500" id="file_label_<?php echo e($name); ?>">Click to upload</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Max <?php echo e($size); ?></p>
                        </div>
                        <input type="file" id="file_<?php echo e($name); ?>" name="<?php echo e($name); ?>" accept="<?php echo e($accept); ?>" class="hidden"
                               onchange="updateFileLabel('<?php echo e($name); ?>', this)">
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        
        <div class="step-panel" id="panel-5">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-7">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl text-white text-sm font-bold" style="background:<?php echo e($primary); ?>;">5</span>
                    <div>
                        <h2 class="text-base font-bold" style="color:<?php echo e($ink); ?>;">Review &amp; Submit</h2>
                        <p class="text-xs mt-0.5" style="color:<?php echo e($muted); ?>;">Confirm all details before submitting</p>
                    </div>
                </div>

                
                <div class="mb-5 rounded-2xl border border-slate-100 bg-slate-50 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400">Student</h3>
                        <button type="button" onclick="goTo(1)" class="text-xs font-semibold hover:underline" style="color:<?php echo e($primary); ?>;">Edit</button>
                    </div>
                    <div class="review-row text-sm">
                        <div><p class="text-slate-400 text-xs font-semibold uppercase tracking-wide mb-0.5">Full Name</p><p class="font-semibold text-slate-800" id="rv-name">—</p></div>
                        <div><p class="text-slate-400 text-xs font-semibold uppercase tracking-wide mb-0.5">Gender</p><p class="font-semibold text-slate-800" id="rv-gender">—</p></div>
                        <div><p class="text-slate-400 text-xs font-semibold uppercase tracking-wide mb-0.5">Date of Birth</p><p class="font-semibold text-slate-800" id="rv-dob">—</p></div>
                        <div><p class="text-slate-400 text-xs font-semibold uppercase tracking-wide mb-0.5">Class</p><p class="font-semibold text-slate-800" id="rv-class">—</p></div>
                        <div><p class="text-slate-400 text-xs font-semibold uppercase tracking-wide mb-0.5">Previous School</p><p class="font-semibold text-slate-800" id="rv-prev-school">—</p></div>
                    </div>
                </div>

                
                <div class="mb-5 rounded-2xl border border-slate-100 bg-slate-50 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400">Parent / Guardian</h3>
                        <button type="button" onclick="goTo(2)" class="text-xs font-semibold hover:underline" style="color:<?php echo e($primary); ?>;">Edit</button>
                    </div>
                    <div class="review-row text-sm">
                        <div><p class="text-slate-400 text-xs font-semibold uppercase tracking-wide mb-0.5">Name</p><p class="font-semibold text-slate-800" id="rv-parent-name">—</p></div>
                        <div><p class="text-slate-400 text-xs font-semibold uppercase tracking-wide mb-0.5">Phone</p><p class="font-semibold text-slate-800" id="rv-parent-phone">—</p></div>
                        <div class="sm:col-span-2"><p class="text-slate-400 text-xs font-semibold uppercase tracking-wide mb-0.5">Email</p><p class="font-semibold text-slate-800" id="rv-parent-email">—</p></div>
                    </div>
                </div>

                
                <div class="mb-5 rounded-2xl border border-slate-100 bg-slate-50 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400">Location</h3>
                        <button type="button" onclick="goTo(3)" class="text-xs font-semibold hover:underline" style="color:<?php echo e($primary); ?>;">Edit</button>
                    </div>
                    <div class="review-row text-sm">
                        <div><p class="text-slate-400 text-xs font-semibold uppercase tracking-wide mb-0.5">State of Origin</p><p class="font-semibold text-slate-800" id="rv-state">—</p></div>
                        <div><p class="text-slate-400 text-xs font-semibold uppercase tracking-wide mb-0.5">LGA</p><p class="font-semibold text-slate-800" id="rv-lga">—</p></div>
                        <div class="sm:col-span-2"><p class="text-slate-400 text-xs font-semibold uppercase tracking-wide mb-0.5">Address</p><p class="font-semibold text-slate-800" id="rv-address">—</p></div>
                    </div>
                </div>

                
                <div class="mb-6 rounded-2xl border border-slate-100 bg-slate-50 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400">Documents</h3>
                        <button type="button" onclick="goTo(4)" class="text-xs font-semibold hover:underline" style="color:<?php echo e($primary); ?>;">Edit</button>
                    </div>
                    <div class="flex flex-wrap gap-2 text-sm" id="rv-docs">
                        <span class="text-slate-400 text-xs">No documents selected</span>
                    </div>
                </div>

                
                <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 mb-6">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" id="declaration" class="mt-1 h-4 w-4 rounded border-slate-300 accent-[--primary]" required>
                        <span class="text-sm text-slate-600 leading-relaxed">
                            I certify that all information provided in this application is <strong>true and accurate</strong> to the best of my knowledge. I understand that providing false information may result in the cancellation of the application.
                        </span>
                    </label>
                    <span class="error-msg show hidden" id="err-declaration">You must accept the declaration to submit</span>
                </div>

                
                <p class="text-xs text-slate-400 text-center mb-4">
                    This form is protected against automated submissions.
                </p>
            </div>
        </div>

        
        <div class="mt-6 flex items-center justify-between">
            <button type="button" id="btn-back" onclick="navStep(-1)" class="btn-outline hidden">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back
            </button>
            <div></div>
            <div class="flex gap-3">
                <button type="button" id="btn-next" onclick="navStep(1)" class="btn-primary">
                    Next Step
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button type="submit" id="btn-submit" class="btn-primary hidden" onclick="return checkDeclaration()">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Submit Application
                </button>
            </div>
        </div>
    </form>
</div>

<?php echo $__env->make('public.partials.footer', ['school' => $school, 'publicPage' => $publicPage], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
const lgaMap = <?php echo json_encode($states, 15, 512) ?>;

const stepLabels = [
    'Student Information',
    'Parent / Guardian',
    'Location & Origin',
    'Supporting Documents',
    'Review & Submit',
];

let currentStep = 1;
const totalSteps = 5;

// ── Navigation ──────────────────────────────────────────────────
function navStep(dir) {
    const next = currentStep + dir;
    if (dir > 0 && !validateStep(currentStep)) return;
    if (next < 1 || next > totalSteps) return;
    goTo(next);
}

function goTo(step) {
    document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-' + step).classList.add('active');

    // Update circles
    for (let i = 1; i <= totalSteps; i++) {
        const c = document.getElementById('circle-' + i);
        const lbl = document.getElementById('circle-label-' + i);
        c.className = 'step-circle';
        if (i < step) {
            c.classList.add('done');
            lbl.innerHTML = '&#10003;';
        } else if (i === step) {
            c.classList.add('active');
            lbl.textContent = i;
        } else {
            lbl.textContent = i;
        }
    }

    // Update lines
    for (let i = 1; i < totalSteps; i++) {
        const line = document.getElementById('line-' + i);
        line.className = 'step-line' + (i < step ? ' done' : '');
    }

    document.getElementById('step-label-text').textContent =
        'Step ' + step + ' of ' + totalSteps + ' — ' + stepLabels[step - 1];

    document.getElementById('btn-back').classList.toggle('hidden', step === 1);
    document.getElementById('btn-next').classList.toggle('hidden', step === totalSteps);
    document.getElementById('btn-submit').classList.toggle('hidden', step !== totalSteps);

    if (step === totalSteps) buildReview();

    window.scrollTo({ top: 0, behavior: 'smooth' });
    currentStep = step;
}

// ── Validation ──────────────────────────────────────────────────
const stepRequired = {
    1: ['first_name','last_name','gender','date_of_birth','class_applied_for'],
    2: ['parent_name','parent_phone','parent_email'],
    3: ['state_of_origin','lga'],
    4: [],
    5: [],
};

function validateStep(step) {
    let valid = true;
    const fields = stepRequired[step] || [];

    fields.forEach(name => {
        const el = document.querySelector('[name="' + name + '"]');
        const err = document.getElementById('err-' + name);
        if (!el) return;

        const isEmpty = !el.value.trim();
        const isInvalidEmail = name === 'parent_email' && el.value && !isValidEmail(el.value);
        const isInvalidPhone = name === 'parent_phone' && el.value && !isValidPhone(el.value);

        const bad = isEmpty || isInvalidEmail || isInvalidPhone;
        el.classList.toggle('error', bad);
        if (err) {
            if (isInvalidEmail) err.textContent = 'Please enter a valid email address';
            else if (isInvalidPhone) err.textContent = 'Please enter a valid phone number';
            err.classList.toggle('show', bad);
        }
        if (bad) valid = false;
    });

    return valid;
}

function isValidEmail(v) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim());
}

function isValidPhone(v) {
    return /^[0-9\+\-\s\(\)]{7,20}$/.test(v.trim());
}

function checkDeclaration() {
    const cb = document.getElementById('declaration');
    const err = document.getElementById('err-declaration');
    if (!cb.checked) {
        err.classList.remove('hidden');
        err.classList.add('show');
        return false;
    }
    err.classList.add('hidden');
    return true;
}

// ── State / LGA ────────────────────────────────────────────────
function populateLgas(state) {
    const sel = document.getElementById('f_lga');
    sel.innerHTML = '<option value="">— Select LGA —</option>';
    sel.disabled = !state;
    if (state && lgaMap[state]) {
        lgaMap[state].forEach(lga => {
            const opt = document.createElement('option');
            opt.value = opt.textContent = lga;
            sel.add(opt);
        });
    }
}

// Pre-populate LGA if old value exists (server-side re-render)
(function initLga() {
    const stateEl = document.getElementById('f_state_of_origin');
    const lgaEl   = document.getElementById('f_lga');
    if (stateEl && stateEl.value) {
        populateLgas(stateEl.value);
        if (lgaEl && '<?php echo e(old('lga')); ?>') {
            lgaEl.value = '<?php echo e(old('lga')); ?>';
        }
    }
})();

// ── File labels ────────────────────────────────────────────────
function updateFileLabel(name, input) {
    const lbl = document.getElementById('file_label_' + name);
    if (input.files && input.files[0]) {
        lbl.textContent = input.files[0].name;
        lbl.style.color = '#22c55e';
    }
}

// ── Review builder ─────────────────────────────────────────────
function buildReview() {
    const g = id => (document.getElementById(id) || { textContent: '—' });
    const v = name => {
        const el = document.querySelector('[name="' + name + '"]');
        return el && el.value.trim() ? el.value.trim() : '—';
    };

    const fn = v('first_name'), ln = v('last_name'), on = v('other_names');
    g('rv-name').textContent = [fn, on !== '—' ? on : '', ln].filter(Boolean).join(' ');
    g('rv-gender').textContent = v('gender') !== '—' ? v('gender').charAt(0).toUpperCase() + v('gender').slice(1) : '—';
    g('rv-dob').textContent = v('date_of_birth');
    g('rv-class').textContent = v('class_applied_for');
    g('rv-prev-school').textContent = v('previous_school');
    g('rv-parent-name').textContent = v('parent_name');
    g('rv-parent-phone').textContent = v('parent_phone');
    g('rv-parent-email').textContent = v('parent_email');
    g('rv-state').textContent = v('state_of_origin');
    g('rv-lga').textContent = v('lga');
    g('rv-address').textContent = v('address');

    const docsEl = document.getElementById('rv-docs');
    const docNames = ['photo','birth_certificate','previous_result'];
    const docLabels = {'photo':'Passport Photo','birth_certificate':'Birth Certificate','previous_result':'Previous Result'};
    const attached = docNames.filter(n => {
        const el = document.getElementById('file_' + n);
        return el && el.files && el.files[0];
    });
    if (attached.length) {
        docsEl.innerHTML = attached.map(n =>
            `<span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold text-white" style="background:var(--primary)">
                &#10003; ${docLabels[n]}
            </span>`
        ).join('');
    } else {
        docsEl.innerHTML = '<span class="text-slate-400 text-xs">No documents selected (optional)</span>';
    }
}

// ── Clear errors on input ──────────────────────────────────────
document.querySelectorAll('.field-input').forEach(el => {
    el.addEventListener('input', function () {
        this.classList.remove('error');
        const err = document.getElementById('err-' + this.name);
        if (err) err.classList.remove('show');
    });
});

// Show errors for old() (server re-render after validation fail)
<?php if($errors->any()): ?>
    // Force the page to show the correct step on server-side errors
    const failedFields = <?php echo json_encode($errors->keys(), 15, 512) ?>;
    const stepMap = {
        'first_name':1,'last_name':1,'gender':1,'date_of_birth':1,'class_applied_for':1,
        'parent_name':2,'parent_phone':2,'parent_email':2,
        'state_of_origin':3,'lga':3,
        'photo':4,'birth_certificate':4,'previous_result':4,
    };
    let targetStep = 1;
    if (failedFields.length > 0 && stepMap[failedFields[0]]) {
        targetStep = stepMap[failedFields[0]];
    }
    goTo(targetStep);
    failedFields.forEach(name => {
        const el = document.querySelector('[name="' + name + '"]');
        const err = document.getElementById('err-' + name);
        if (el) el.classList.add('error');
        if (err) err.classList.add('show');
    });
<?php endif; ?>
</script>
</body>
</html>
<?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views/admission/apply.blade.php ENDPATH**/ ?>