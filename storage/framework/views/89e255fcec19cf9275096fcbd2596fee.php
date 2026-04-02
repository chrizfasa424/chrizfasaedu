<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'EMS'); ?> - <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <?php if(auth()->guard()->check()): ?>
        <aside class="w-64 bg-gray-900 text-white min-h-screen fixed left-0 top-0 overflow-y-auto">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-lg font-bold text-emerald-400"><?php echo e(auth()->user()->school?->name ?? 'ChrizFasa EMS'); ?></h1>
                <p class="text-xs text-gray-400 mt-1"><?php echo e(auth()->user()->role->label()); ?></p>
            </div>
            <?php
                $sidebarDashboardRoute = auth()->user()->isSuperAdmin()
                    ? route('multi-school.index')
                    : (auth()->user()->isTeacher()
                        ? route('teacher.dashboard')
                        : (auth()->user()->isStudent()
                            ? route('student.dashboard')
                            : (auth()->user()->isParent() ? route('parent.dashboard') : route('dashboard'))));
            ?>
            <nav class="p-4 space-y-1">
                <a href="<?php echo e($sidebarDashboardRoute); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>

                <?php if(in_array(auth()->user()->role->value, ['super_admin','school_admin','principal','teacher'])): ?>
                <p class="text-xs uppercase text-gray-500 mt-4 mb-2 px-3">Academic</p>
                <a href="<?php echo e(route('academic.sessions.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Sessions & Terms</a>
                <a href="<?php echo e(route('academic.classes.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Classes</a>
                <a href="<?php echo e(route('academic.subjects.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Subjects</a>
                <a href="<?php echo e(route('academic.students.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Students</a>
                <a href="<?php echo e(route('academic.attendance.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Attendance</a>
                <a href="<?php echo e(route('academic.timetable.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Timetable</a>
                <?php endif; ?>

                <?php if(in_array(auth()->user()->role->value, ['super_admin','school_admin','principal'])): ?>
                <p class="text-xs uppercase text-gray-500 mt-4 mb-2 px-3">Admission</p>
                <a href="<?php echo e(route('admission.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Applications</a>
                <?php endif; ?>

                <?php if(in_array(auth()->user()->role->value, ['super_admin','school_admin','principal','teacher'])): ?>
                <p class="text-xs uppercase text-gray-500 mt-4 mb-2 px-3">Examination</p>
                <a href="<?php echo e(route('examination.results.enter-scores')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Enter Scores</a>
                <a href="<?php echo e(route('examination.results.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">View Results</a>
                <?php endif; ?>

                <?php if(in_array(auth()->user()->role->value, ['super_admin','school_admin','accountant'])): ?>
                <p class="text-xs uppercase text-gray-500 mt-4 mb-2 px-3">Finance</p>
                <a href="<?php echo e(route('financial.fees.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Fee Structures</a>
                <a href="<?php echo e(route('financial.invoices.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Invoices</a>
                <a href="<?php echo e(route('financial.payments.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Payments</a>
                <?php endif; ?>

                <?php if(in_array(auth()->user()->role->value, ['super_admin','school_admin','principal'])): ?>
                <p class="text-xs uppercase text-gray-500 mt-4 mb-2 px-3">Management</p>
                <a href="<?php echo e(route('staff.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Staff</a>
                <a href="<?php echo e(route('announcements.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Announcements</a>
                <a href="<?php echo e(route('library.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Library</a>
                <a href="<?php echo e(route('hostel.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Hostel</a>
                <a href="<?php echo e(route('health.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Health</a>
                <a href="<?php echo e(route('assets.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Assets</a>
                <?php endif; ?>

                <?php if(in_array(auth()->user()->role->value, ['super_admin','school_admin'])): ?>
                <p class="text-xs uppercase text-gray-500 mt-4 mb-2 px-3">Reports & System</p>
                <a href="<?php echo e(route('reports.financial')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Financial Reports</a>
                <a href="<?php echo e(route('reports.academic')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Academic Reports</a>
                <a href="<?php echo e(route('system.hero-slides.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Hero Slides</a>
                <a href="<?php echo e(route('system.testimonials.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Testimonials</a>
                <a href="<?php echo e(route('settings.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 text-sm">Settings</a>
                <?php endif; ?>
            </nav>
        </aside>
        <?php endif; ?>

        <!-- Main Content -->
        <main class="<?php echo e(auth()->check() ? 'ml-64' : ''); ?> flex-1 min-h-screen">
            <?php if(auth()->guard()->check()): ?>
            <header class="bg-white shadow-sm border-b px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800"><?php echo $__env->yieldContent('header', 'Dashboard'); ?></h2>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600"><?php echo e(auth()->user()->full_name); ?></span>
                    <form action="<?php echo e(route('logout')); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Logout</button>
                    </form>
                </div>
            </header>
            <?php endif; ?>

            <div class="p-6">
                <?php if(session('success')): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6">
                    <?php echo e(session('success')); ?>

                </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <?php echo e(session('error')); ?>

                </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <ul class="list-disc list-inside text-sm">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>
            </div>

            <?php
                $footerSchool = auth()->user()?->school;
                if (!$footerSchool) {
                    $footerHost = request()->getHost();
                    $footerSchool = \App\Models\School::query()
                        ->where('is_active', true)
                        ->orderByRaw('CASE WHEN domain = ? THEN 0 ELSE 1 END', [$footerHost])
                        ->orderBy('id')
                        ->first();
                }
                $footerPublicPage = \App\Support\PublicPageContent::forSchool($footerSchool);
            ?>
            <?php echo $__env->make('public.partials.footer', ['school' => $footerSchool, 'publicPage' => $footerPublicPage], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </main>
    </div>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views\layouts\app.blade.php ENDPATH**/ ?>