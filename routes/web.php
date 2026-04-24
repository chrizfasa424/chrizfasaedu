<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Academic;
use App\Http\Controllers\Admission\AdmissionController;
use App\Http\Controllers\Financial;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Examination\ResultController;
use App\Http\Controllers\Examination\ResultSheetController;
use App\Http\Controllers\Examination\ResultSubmissionController;
use App\Http\Controllers\Examination\ResultFeedbackController;
use App\Http\Controllers\Examination\ResultCommentController;
use App\Http\Controllers\Academic\AssignmentSubmissionController;
use App\Http\Controllers\Portal\StudentAssignmentController;
use App\Http\Controllers\Communication\AnnouncementController;
use App\Http\Controllers\Communication\MessageController;
use App\Http\Controllers\Library\LibraryController;
use App\Http\Controllers\Portal;
use App\Http\Controllers\Portal\PortalMessageController;
use App\Http\Controllers\Transport\TransportController;
use App\Http\Controllers\Hostel\HostelController;
use App\Http\Controllers\Health\MedicalController;
use App\Http\Controllers\Inventory\AssetController;
use App\Http\Controllers\System\SettingsController;
use App\Http\Controllers\System\HeroSlideController;
use App\Http\Controllers\System\TestimonialController;
use App\Http\Controllers\Reporting\ReportController;
use App\Http\Controllers\MultiSchool\TenantController;
use App\Http\Controllers\NotificationCenterController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes - Enterprise Education Management System
|--------------------------------------------------------------------------
*/

// ── Public / Auth ──────────────────────────────────────────
Route::get('/', [PublicPageController::class, 'index'])->name('public.home');
Route::get('/contact', [PublicPageController::class, 'contactPage'])->name('public.contact');
Route::get('/privacy-policy', [PublicPageController::class, 'privacyPage'])->name('public.privacy');
Route::get('/cookies-policy', [PublicPageController::class, 'cookiesPage'])->name('public.cookies');
Route::post('/contact', [PublicPageController::class, 'submitContact'])
    ->middleware('throttle:5,1')
    ->name('public.contact.submit');
Route::get('/menu/{section}/{slug}', [PublicPageController::class, 'submenuPage'])->name('public.submenu');

Route::get('/admin-access', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/admin-access', [LoginController::class, 'login'])->middleware('throttle:8,1');
Route::get('/staff', [LoginController::class, 'showStaffLoginForm'])->name('staff.login');
Route::post('/staff', [LoginController::class, 'staffLogin'])->name('staff.login.submit')->middleware('throttle:8,1');
Route::get('/staff-access', [LoginController::class, 'showStaffLoginForm']);
Route::post('/staff-access', [LoginController::class, 'staffLogin'])->middleware('throttle:8,1');
Route::get('/portal', [LoginController::class, 'showPortalLoginForm'])->name('portal.login');
Route::post('/portal', [LoginController::class, 'portalLogin'])->name('portal.login.submit')->middleware('throttle:8,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/staff/logout', [LoginController::class, 'staffLogout'])->name('staff.logout');
Route::post('/portal/logout', [LoginController::class, 'portalLogout'])->name('portal.logout');
Route::get('/csrf-token', function (Request $request) {
    $request->session()->regenerateToken();

    return response()->json([
        'token' => csrf_token(),
    ]);
})->middleware('throttle:30,1')->name('csrf.token');

// Admin password reset
Route::get('/admin-access/forgot-password', [PasswordResetController::class, 'showAdminForgotForm'])->name('admin.password.request');
Route::post('/admin-access/forgot-password', [PasswordResetController::class, 'sendAdminResetLink'])->name('admin.password.email')->middleware('throttle:4,1');
Route::get('/admin-access/reset-password/{token}', [PasswordResetController::class, 'showAdminResetForm'])->name('admin.password.reset');
Route::post('/admin-access/reset-password', [PasswordResetController::class, 'resetAdminPassword'])->name('admin.password.update')->middleware('throttle:6,1');

// Portal (student/parent) password reset
Route::get('/portal/forgot-password', [PasswordResetController::class, 'showPortalForgotForm'])->name('portal.password.request');
Route::post('/portal/forgot-password', [PasswordResetController::class, 'sendPortalResetLink'])->name('portal.password.email')->middleware('throttle:4,1');
Route::get('/portal/reset-password/{token}', [PasswordResetController::class, 'showPortalResetForm'])->name('portal.password.reset');
Route::post('/portal/reset-password', [PasswordResetController::class, 'resetPortalPassword'])->name('portal.password.update')->middleware('throttle:6,1');

// Online Admission (public)
Route::get('/apply', [AdmissionController::class, 'applyOnline'])->name('admission.apply');
Route::get('/apply/success', [AdmissionController::class, 'success'])->name('admission.success');
Route::post('/apply', [AdmissionController::class, 'store'])->name('admission.apply.store')->middleware('throttle:5,10');

// ── Authenticated Routes ───────────────────────────────────
Route::middleware(['redirect.portal.from.admin', 'auth', 'force.password.change'])->group(function () {

    // ── Profile (admin / staff / teacher — web guard) ──────
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',                   [ProfileController::class, 'show'])->name('show');
        Route::post('/info',              [ProfileController::class, 'updateInfo'])->name('update.info');
        Route::post('/details',           [ProfileController::class, 'updateProfileDetails'])->name('update.details');
        Route::post('/password',          [ProfileController::class, 'changePassword'])->name('change.password');
        Route::post('/photo',             [ProfileController::class, 'updatePhoto'])->name('update.photo');
        Route::delete('/photo',           [ProfileController::class, 'deletePhoto'])->name('delete.photo');
    });

    // ── Dashboard ──────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/system/audit-logs', [DashboardController::class, 'auditLogs'])->name('system.audit-logs.index');
    Route::get('/teacher/dashboard', [DashboardController::class, 'teacherDashboard'])->name('teacher.dashboard');
    Route::get('/staff/dashboard', [DashboardController::class, 'staffDashboard'])->name('staff.dashboard')->middleware('role:staff');

    // ── Academic Module ────────────────────────────────────
    Route::prefix('academic')->name('academic.')
        ->middleware('role:super_admin,school_admin,principal,vice_principal,teacher,staff')
        ->group(function () {

        // Sessions & Terms
        Route::resource('sessions', Academic\SessionController::class)->only(['index', 'store']);
        Route::post('sessions/{session}/set-current', [Academic\SessionController::class, 'setAsCurrent'])->name('sessions.set-current');

        // Terms (nested under sessions view)
        Route::post('sessions/{session}/terms', [Academic\TermController::class, 'store'])->name('terms.store');
        Route::put('terms/{term}', [Academic\TermController::class, 'update'])->name('terms.update');
        Route::delete('terms/{term}', [Academic\TermController::class, 'destroy'])->name('terms.destroy');
        Route::post('terms/{term}/set-current', [Academic\TermController::class, 'setCurrent'])->name('terms.set-current');

        // Classes & Arms
        Route::resource('classes', Academic\ClassController::class);

        // Subjects
        Route::resource('subjects', Academic\SubjectController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('teaching-assignments', [Academic\TeachingAssignmentController::class, 'index'])
            ->middleware('role:super_admin,school_admin,principal,vice_principal')
            ->name('teaching-assignments.index');
        Route::post('teaching-assignments', [Academic\TeachingAssignmentController::class, 'update'])
            ->middleware('role:super_admin,school_admin,principal,vice_principal')
            ->name('teaching-assignments.update');

        // Students
        Route::post('students/bulk-delete', [Academic\StudentController::class, 'bulkDestroy'])->name('students.bulk-destroy');
        Route::resource('students', Academic\StudentController::class);
        Route::post('students/promote', [Academic\StudentController::class, 'promote'])->name('students.promote');
        Route::post('students/{student}/toggle-active', [Academic\StudentController::class, 'toggleActive'])->name('students.toggle-active');
        Route::post('students/{student}/reset-password', [Academic\StudentController::class, 'resetPassword'])->name('students.reset-password');
        Route::post('students/{student}/change-password', [Academic\StudentController::class, 'changePassword'])->name('students.change-password');

        // Attendance
        Route::get('attendance', [Academic\AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('attendance', [Academic\AttendanceController::class, 'store'])->name('attendance.store');
        Route::post('attendance/import', [Academic\AttendanceController::class, 'importSheet'])->name('attendance.import');
        Route::get('attendance/history', [Academic\AttendanceController::class, 'history'])->name('attendance.history');

        // Timetable
        Route::get('timetable', [Academic\TimetableController::class, 'index'])->name('timetable.index');
        Route::post('timetable', [Academic\TimetableController::class, 'store'])->name('timetable.store');
        Route::put('timetable/{timetable}', [Academic\TimetableController::class, 'update'])->name('timetable.update');
        Route::delete('timetable/{timetable}', [Academic\TimetableController::class, 'destroy'])->name('timetable.destroy');
        Route::post('timetable/generate-sample', [Academic\TimetableController::class, 'generateSample'])->name('timetable.generate-sample');

        // Assignments
        Route::get('assignments', [Academic\AssignmentController::class, 'index'])->name('assignments.index');
        Route::post('assignments', [Academic\AssignmentController::class, 'store'])->name('assignments.store');
        Route::put('assignments/{assignment}', [Academic\AssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('assignments/{assignment}', [Academic\AssignmentController::class, 'destroy'])->name('assignments.destroy');
        Route::post('assignments/{assignment}/publish', [Academic\AssignmentController::class, 'publish'])->name('assignments.publish');
        Route::post('assignments/{assignment}/unpublish', [Academic\AssignmentController::class, 'unpublish'])->name('assignments.unpublish');
        Route::get('assignments/{assignment}/download', [Academic\AssignmentController::class, 'download'])->name('assignments.download');
        Route::get('assignments/{assignment}/submissions', [AssignmentSubmissionController::class, 'index'])->name('assignments.submissions.index');
        Route::patch('assignment-submissions/{assignmentSubmission}/review', [AssignmentSubmissionController::class, 'review'])->name('assignment-submissions.review');
        Route::get('assignment-submissions/{assignmentSubmission}/download', [AssignmentSubmissionController::class, 'download'])->name('assignment-submissions.download');
    });

    // ── Admission Module ───────────────────────────────────
    Route::prefix('admission')->name('admission.')
        ->middleware('role:super_admin,school_admin,principal,vice_principal,teacher,staff')
        ->group(function () {
        Route::get('/', [AdmissionController::class, 'index'])->name('index');
        Route::get('/{admission}', [AdmissionController::class, 'show'])->name('show');
        Route::get('/{admission}/documents/{document}', [AdmissionController::class, 'showDocument'])->name('documents.show');
        Route::patch('/{admission}/review', [AdmissionController::class, 'review'])->name('review');
        Route::patch('/{admission}/student-email', [AdmissionController::class, 'updateStudentEmail'])->name('update-student-email');
        Route::post('/{admission}/enroll', [AdmissionController::class, 'enroll'])->name('enroll');
        Route::post('/{admission}/sync-login-email', [AdmissionController::class, 'syncStudentLoginEmail'])->name('sync-login-email');
        Route::delete('/{admission}', [AdmissionController::class, 'destroy'])->name('destroy');
    });

    // ── Financial Module ───────────────────────────────────
    Route::prefix('financial')->name('financial.')
        ->middleware('role:super_admin,school_admin,principal,vice_principal,accountant')
        ->group(function () {
        // Fee Structures
        Route::resource('fees', Financial\FeeController::class)->only(['index', 'create', 'store', 'update', 'destroy']);

        // Invoices
        Route::get('invoices', [Financial\InvoiceController::class, 'index'])->name('invoices.index');
        Route::post('invoices/generate', [Financial\InvoiceController::class, 'generate'])->name('invoices.generate');
        Route::get('invoices/{invoice}', [Financial\InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('invoices/{invoice}/print', [Financial\InvoiceController::class, 'print'])->name('invoices.print');
        Route::get('invoices/{invoice}/pdf', [Financial\InvoiceController::class, 'pdf'])->name('invoices.pdf');

        // Payments
        Route::get('payments', [Financial\PaymentController::class, 'index'])->name('payments.index');
        Route::post('payments/manual', [Financial\PaymentController::class, 'recordManual'])->name('payments.manual');
        Route::post('payments/paystack', [Financial\PaymentController::class, 'initiatePaystack'])->name('payments.paystack');
        Route::get('payments/paystack/callback', [Financial\PaymentController::class, 'paystackCallback'])->name('payments.paystack.callback');
    });

    // ── Examination / Results ──────────────────────────────
    Route::prefix('examination')->name('examination.')
        ->middleware('role:super_admin,school_admin,principal,vice_principal,teacher,staff')
        ->group(function () {
        Route::prefix('result-submissions')->name('result-submissions.')
            ->middleware('role:super_admin,school_admin,principal,vice_principal,teacher,staff')
            ->group(function () {
            Route::get('/', [ResultSubmissionController::class, 'index'])->name('index');
            Route::post('/', [ResultSubmissionController::class, 'store'])->name('store');
            Route::get('/template', [ResultSubmissionController::class, 'template'])->name('template');
            Route::get('/{resultSubmission}', [ResultSubmissionController::class, 'show'])->name('show');
            Route::post('/{resultSubmission}/submit', [ResultSubmissionController::class, 'submit'])->name('submit');
            Route::post('/{resultSubmission}/review', [ResultSubmissionController::class, 'review'])
                ->middleware('role:super_admin,school_admin,principal,vice_principal')
                ->name('review');
            Route::post('/{resultSubmission}/import', [ResultSubmissionController::class, 'import'])
                ->middleware('role:super_admin,school_admin,principal,vice_principal')
                ->name('import');
            Route::get('/{resultSubmission}/download', [ResultSubmissionController::class, 'download'])->name('download');
        });

        Route::get('results', [ResultController::class, 'index'])->name('results.index');
        Route::get('results/enter-scores', [ResultController::class, 'enterScores'])->name('results.enter-scores');
        Route::post('results/store-scores', [ResultController::class, 'storeScores'])->name('results.store-scores');
        Route::post('results/compute', [ResultController::class, 'computeResults'])->name('results.compute');
        Route::post('results/approve', [ResultController::class, 'approveResults'])->name('results.approve');
        Route::get('results/import', [ResultController::class, 'importForm'])->name('results.import');
        Route::post('results/import', [ResultController::class, 'import'])->name('results.import.store');
        Route::get('results/template', [ResultController::class, 'downloadTemplate'])->name('results.template');
        Route::get('results/export', [ResultController::class, 'export'])->name('results.export');
        Route::put('results/{result}', [ResultController::class, 'update'])->name('results.update');
        Route::delete('results/{result}', [ResultController::class, 'destroy'])->name('results.destroy');
        Route::get('report-card/{student}/{term}', [ResultController::class, 'reportCard'])->name('results.report-card');
        Route::get('report-card/{student}/{term}/download', [ResultController::class, 'downloadReportCard'])->name('results.report-card.download');
        Route::get('results/{student}/{term}/download-excel', [ResultController::class, 'exportStudentResults'])->name('results.student-export');

        // New Result Sheet Module (Import + Validation + Printable Terminal Sheets)
        Route::prefix('result-sheets')->name('result-sheets.')
            ->middleware('role:super_admin,school_admin,principal,vice_principal')
            ->group(function () {
            Route::get('import/{assessmentType?}', [ResultSheetController::class, 'importForm'])
                ->whereIn('assessmentType', ['first_test', 'second_test', 'exam', 'full_result'])
                ->name('import');
            Route::post('preview', [ResultSheetController::class, 'previewImport'])->name('preview');
            Route::get('preview/{batch}', [ResultSheetController::class, 'showPreview'])->name('preview.show');
            Route::post('commit/{batch}', [ResultSheetController::class, 'commitImport'])->name('commit');
            Route::get('history', [ResultSheetController::class, 'history'])->name('history');
            Route::get('publishing', [ResultSheetController::class, 'publishing'])->name('publishing');
            Route::post('publish', [ResultSheetController::class, 'publish'])->name('publish');
            Route::post('unpublish', [ResultSheetController::class, 'unpublish'])->name('unpublish');
            Route::get('template', [ResultSheetController::class, 'downloadTemplate'])->name('template');
            Route::get('class-sheet', [ResultSheetController::class, 'classSheet'])->name('class-sheet');
            Route::get('student/{studentResult}', [ResultSheetController::class, 'studentSheet'])->name('student');
            Route::get('student/{studentResult}/pdf', [ResultSheetController::class, 'studentSheetPdf'])->name('student.pdf');
            Route::get('bulk-print', [ResultSheetController::class, 'bulkPrint'])->name('bulk-print');
            Route::get('errors/{batch}', [ResultSheetController::class, 'downloadErrors'])->name('errors');
        });

        // Student Result Feedback / Query (admin view + response)
        Route::prefix('result-feedback')->name('result-feedback.')
            ->middleware('role:super_admin,school_admin,principal,vice_principal,teacher')
            ->group(function () {
            Route::get('/', [ResultFeedbackController::class, 'index'])->name('index');
            Route::patch('/{feedback}', [ResultFeedbackController::class, 'update'])->name('update');
            Route::delete('/{feedback}', [ResultFeedbackController::class, 'destroy'])->name('destroy');
        });

        // Result Comments (teacher/principal/vice principal + admin visibility controls)
        Route::prefix('result-comments')->name('result-comments.')
            ->middleware('role:super_admin,school_admin,principal,vice_principal,teacher')
            ->group(function () {
            Route::get('/', [ResultCommentController::class, 'index'])->name('index');
            Route::post('/generate-teacher-ai', [ResultCommentController::class, 'generateTeacherComments'])->name('generate-teacher-ai.bulk');
            Route::post('/{studentResult}/generate-teacher-ai', [ResultCommentController::class, 'generateTeacherComment'])->name('generate-teacher-ai');
            Route::patch('/{studentResult}', [ResultCommentController::class, 'update'])->name('update');
            Route::patch('/{studentResult}/visibility', [ResultCommentController::class, 'updateVisibility'])->name('visibility');
        });
    });

    // ── Staff Module (Admin-only) ──────────────────────────
    Route::middleware('role:super_admin,school_admin,principal,vice_principal')->group(function () {
        Route::post('admin/staff/bulk-delete', [StaffController::class, 'bulkDestroy'])->name('staff.bulk-destroy');
        Route::post('admin/staff/{staff}/reset-password', [StaffController::class, 'resetPassword'])->name('staff.reset-password');
        Route::resource('admin/staff', StaffController::class)->names('staff');
    });

    // ── Communication ──────────────────────────────────────
    Route::resource('announcements', AnnouncementController::class)
        ->middleware('role:super_admin,school_admin,principal,vice_principal,teacher,staff')
        ->only(['index', 'store', 'destroy']);

    // ── Messaging (admin) ──────────────────────────────────
    Route::prefix('messages')->name('messages.')
        ->middleware('role:super_admin,school_admin,principal,vice_principal,teacher,staff')
        ->group(function () {
        Route::get('/unread-count',  [MessageController::class, 'unreadCount'])->name('unread-count');
        Route::get('/',              [MessageController::class, 'index'])->name('index');
        Route::get('/create',        [MessageController::class, 'create'])->name('create');
        Route::post('/',             [MessageController::class, 'store'])->name('store');
        Route::get('/{message}',     [MessageController::class, 'show'])->name('show');
        Route::delete('/{message}',  [MessageController::class, 'destroy'])->name('destroy');
    });

    // ── Library ────────────────────────────────────────────
    Route::prefix('library')->name('library.')
        ->middleware('role:super_admin,school_admin,principal,vice_principal,librarian,staff')
        ->group(function () {
        Route::get('/', [LibraryController::class, 'index'])->name('index');
        Route::post('/books', [LibraryController::class, 'store'])->name('store');
        Route::post('/borrow', [LibraryController::class, 'borrow'])->name('borrow');
        Route::post('/return/{borrowing}', [LibraryController::class, 'returnBook'])->name('return');
    });

    // ── Transport ──────────────────────────────────────────
    Route::resource('transport', TransportController::class)
        ->middleware('role:super_admin,school_admin,principal,vice_principal,driver,staff')
        ->only(['index', 'store', 'update']);

    // ── Hostel ─────────────────────────────────────────────
    Route::middleware('role:super_admin,school_admin,principal,vice_principal,staff')->group(function () {
        Route::get('hostel', [HostelController::class, 'index'])->name('hostel.index');
        Route::post('hostel', [HostelController::class, 'store'])->name('hostel.store');
        Route::post('hostel/allocate', [HostelController::class, 'allocateRoom'])->name('hostel.allocate');
    });

    // ── Health ─────────────────────────────────────────────
    Route::middleware('role:super_admin,school_admin,principal,vice_principal,nurse,staff')->group(function () {
        Route::get('health', [MedicalController::class, 'index'])->name('health.index');
        Route::post('health', [MedicalController::class, 'store'])->name('health.store');
    });

    // ── Inventory ──────────────────────────────────────────
    Route::resource('assets', AssetController::class)
        ->middleware('role:super_admin,school_admin,principal,vice_principal,staff')
        ->only(['index', 'store', 'update']);

    // ── Reporting ──────────────────────────────────────────
    Route::prefix('reports')->name('reports.')
        ->middleware('role:super_admin,school_admin,principal,vice_principal')
        ->group(function () {
        Route::get('financial', [ReportController::class, 'financialDashboard'])->name('financial');
        Route::get('academic', [ReportController::class, 'academicDashboard'])->name('academic');
        Route::get('attendance', [ReportController::class, 'attendanceReport'])->name('attendance');
    });

    // ── Settings ───────────────────────────────────────────
    Route::middleware('role:super_admin,school_admin')->group(function () {
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('settings/{page}', [SettingsController::class, 'showPage'])->name('settings.page');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::put('settings/system', [SettingsController::class, 'updateSettings'])->name('settings.system');
    Route::post('settings/rich-text/upload', [SettingsController::class, 'uploadRichTextImage'])
        ->middleware('throttle:20,1')
        ->name('settings.rich-text.upload');
    Route::post('settings/system/test-smtp', [SettingsController::class, 'sendSmtpTest'])->name('settings.smtp-test');
    Route::post('settings/public-page/reset-theme', [SettingsController::class, 'resetThemeDefaults'])->name('settings.public-page.reset-theme');
    Route::put('settings/public-page', [SettingsController::class, 'updatePublicPage'])->name('settings.public-page');
    Route::post('settings/submenu-image/upload', [SettingsController::class, 'uploadSubmenuImage'])->name('settings.submenu-image.upload');
    Route::post('settings/submenu-image/remove', [SettingsController::class, 'removeSubmenuImage'])->name('settings.submenu-image.remove');
    Route::post('settings/submenu-content/save', [SettingsController::class, 'saveSubmenuContent'])->name('settings.submenu-content.save');
    Route::post('settings/submenu-content-image/upload', [SettingsController::class, 'uploadSubmenuContentImage'])->name('settings.submenu-content-image.upload');
    Route::post('settings/submenu-content-image/remove', [SettingsController::class, 'removeSubmenuContentImage'])->name('settings.submenu-content-image.remove');
    Route::post('settings/faqs/save', [SettingsController::class, 'saveFaqs'])->name('settings.faqs.save');
    Route::prefix('system')->name('system.')->group(function () {
        Route::resource('hero-slides', HeroSlideController::class)->except('show');
        Route::patch('hero-slides/{heroSlide}/toggle', [HeroSlideController::class, 'toggle'])->name('hero-slides.toggle');
        Route::patch('hero-slides/reorder', [HeroSlideController::class, 'reorder'])->name('hero-slides.reorder');
        Route::get('testimonials', [TestimonialController::class, 'index'])->name('testimonials.index');
        Route::get('testimonials/bulk', fn() => redirect()->route('system.testimonials.index'));
        Route::post('testimonials/bulk', [TestimonialController::class, 'bulkAction'])->name('testimonials.bulk');
        Route::post('testimonials/{testimonial}/approve', [TestimonialController::class, 'approve'])->name('testimonials.approve');
        Route::post('testimonials/{testimonial}/reject', [TestimonialController::class, 'reject'])->name('testimonials.reject');
        Route::post('testimonials/{testimonial}/delete', [TestimonialController::class, 'destroy'])->name('testimonials.destroy');
    });
    });

    // ── Multi-School / SaaS (Super Admin only) ─────────────
    Route::prefix('admin')->name('multi-school.')->middleware('role:super_admin')->group(function () {
        Route::get('/dashboard', [TenantController::class, 'index'])->name('index');
        Route::get('/domains', [TenantController::class, 'domains'])->name('domains');
        Route::post('/onboard', [TenantController::class, 'onboard'])->name('onboard');
        Route::post('/domains/clear-cache', [TenantController::class, 'clearDomainCache'])->name('domains.clear-cache');
        Route::put('/domains/{school}', [TenantController::class, 'updateDomain'])->name('domains.update');
    });
});

// ── Student / Parent Portal Routes (portal guard) ──────────
Route::middleware(['portal.guard', 'auth:portal', 'role:student,parent'])->group(function () {
    Route::get('/student/dashboard', [Portal\StudentPortalController::class, 'dashboard'])->name('student.dashboard');
    Route::post('/student/testimonials', [Portal\StudentPortalController::class, 'submitTestimonial'])
        ->middleware('throttle:3,10')
        ->name('student.testimonials.submit');
    Route::get('/my/results', [Portal\StudentPortalController::class, 'resultsCenter'])->name('portal.results.center');
    Route::get('/my/result-feedback', [Portal\StudentPortalController::class, 'resultFeedbackCenter'])->name('portal.results.feedback.index');
    Route::post('/my/results/feedback', [Portal\StudentPortalController::class, 'storeResultFeedback'])
        ->middleware('throttle:6,10')
        ->name('portal.results.feedback.store');
    Route::post('/my/result-feedback/{feedback}/read-response', [Portal\StudentPortalController::class, 'markResultFeedbackResponseRead'])
        ->name('portal.results.feedback.read');
    Route::get('/my/result-sheets/{studentResult}/pdf', [Portal\StudentPortalController::class, 'downloadResultSheetPdf'])
        ->name('portal.results.sheet.pdf');
    Route::get('/my/results/{term}', [Portal\StudentPortalController::class, 'reportCard'])->name('portal.results.report-card');
    Route::get('/my/results/{term}/download', [Portal\StudentPortalController::class, 'downloadReportCard'])->name('portal.results.report-card.download');
    Route::get('/my/results/{term}/download-excel', [Portal\StudentPortalController::class, 'downloadResultExcel'])->name('portal.results.download-excel');
    Route::get('/my/timetable', [Portal\StudentPortalController::class, 'timetable'])->name('portal.timetable');
    Route::get('/my/attendance', [Academic\AttendanceController::class, 'portalView'])->name('portal.attendance');
    Route::get('/my/assignments', [Portal\StudentPortalController::class, 'assignments'])->name('portal.assignments');
    Route::get('/my/assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('portal.assignments.show');
    Route::post('/my/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('portal.assignments.submit');
    Route::post('/my/assignments/{assignment}/feedback', [StudentAssignmentController::class, 'feedback'])->name('portal.assignments.feedback');
    Route::get('/my/assignments/{assignment}/download', [Portal\StudentPortalController::class, 'downloadAssignment'])->name('portal.assignments.download');
    Route::get('/parent/dashboard', [Portal\ParentPortalController::class, 'dashboard'])->name('parent.dashboard');
    Route::get('/parent/academic-overview', [Portal\ParentPortalController::class, 'academicOverview'])->name('parent.academic-overview');
    Route::get('/parent/results-grades', [Portal\ParentPortalController::class, 'resultsGrades'])->name('parent.results-grades');
    Route::get('/parent/fees-summary', [Portal\ParentPortalController::class, 'feesSummary'])->name('parent.fees-summary');

    // ── Messaging (portal) ────────────────────────────────
    Route::prefix('my/messages')->name('portal.messages.')->group(function () {
        Route::get('/unread-count',       [PortalMessageController::class, 'unreadCount'])->name('unread-count');
        Route::get('/',                   [PortalMessageController::class, 'index'])->name('index');
        Route::get('/{message}',          [PortalMessageController::class, 'show'])->name('show');
        Route::post('/{message}/reply',   [PortalMessageController::class, 'reply'])->name('reply');
    });

    // ── Profile (student / parent — portal guard) ──────────
    Route::prefix('my/profile')->name('portal.profile.')->group(function () {
        Route::get('/',          [ProfileController::class, 'show'])->name('show');
        Route::post('/info',     [ProfileController::class, 'updateInfo'])->name('update.info');
        Route::post('/details',  [ProfileController::class, 'updateProfileDetails'])->name('update.details');
        Route::post('/password', [ProfileController::class, 'changePassword'])->name('change.password');
        Route::post('/photo',    [ProfileController::class, 'updatePhoto'])->name('update.photo');
        Route::delete('/photo',  [ProfileController::class, 'deletePhoto'])->name('delete.photo');
    });
});

Route::middleware(['redirect.portal.from.admin', 'auth:web,portal'])->group(function () {
    Route::get('/notifications', [NotificationCenterController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}/open', [NotificationCenterController::class, 'open'])->name('notifications.open');
    Route::post('/notifications/read-all', [NotificationCenterController::class, 'markAllRead'])->name('notifications.read-all');
});


// Extended finance payment workflow routes
Route::middleware(['redirect.portal.from.admin', 'auth', 'role:super_admin,school_admin,principal,vice_principal,accountant'])->prefix('financial')->name('financial.')->group(function () {
    Route::get('bank-accounts', [Financial\BankAccountController::class, 'index'])->name('bank-accounts.index');
    Route::post('bank-accounts', [Financial\BankAccountController::class, 'store'])->name('bank-accounts.store');
    Route::put('bank-accounts/{bankAccount}', [Financial\BankAccountController::class, 'update'])->name('bank-accounts.update');
    Route::delete('bank-accounts/{bankAccount}', [Financial\BankAccountController::class, 'destroy'])->name('bank-accounts.destroy');
    Route::post('bank-accounts/{bankAccount}/default', [Financial\BankAccountController::class, 'setDefault'])->name('bank-accounts.default');

    Route::get('payment-methods', [Financial\PaymentMethodController::class, 'index'])->name('payment-methods.index');
    Route::put('payment-methods/{paymentMethod}', [Financial\PaymentMethodController::class, 'update'])->name('payment-methods.update');
    Route::post('payment-methods/{paymentMethod}/gateway-settings', [Financial\PaymentMethodController::class, 'updateGatewaySettings'])->name('payment-methods.gateway-settings');

    Route::get('bursary-signatures', [Financial\BursarySignatureController::class, 'index'])->name('bursary-signatures.index');
    Route::post('bursary-signatures', [Financial\BursarySignatureController::class, 'store'])->name('bursary-signatures.store');
    Route::put('bursary-signatures/{bursarySignature}', [Financial\BursarySignatureController::class, 'update'])->name('bursary-signatures.update');
    Route::delete('bursary-signatures/{bursarySignature}', [Financial\BursarySignatureController::class, 'destroy'])->name('bursary-signatures.destroy');
    Route::post('bursary-signatures/{bursarySignature}/default', [Financial\BursarySignatureController::class, 'setDefault'])->name('bursary-signatures.default');

    Route::get('payments/{payment}/review', [Financial\PaymentController::class, 'review'])->name('payments.review');
    Route::post('payments/{payment}/verify', [Financial\PaymentController::class, 'verify'])->name('payments.verify');
    Route::get('payments/{payment}/proof', [Financial\PaymentController::class, 'proofFile'])->name('payments.proof');
    Route::get('payments/{payment}/receipt', [Financial\PaymentController::class, 'receipt'])->name('payments.receipt');
});

// Student payment portal routes
Route::middleware(['portal.guard', 'auth:portal', 'role:student,parent'])->group(function () {
    Route::get('/my/invoices', [Portal\StudentPaymentController::class, 'invoices'])->name('portal.invoices.index');
    Route::get('/my/invoices/{invoice}', [Portal\StudentPaymentController::class, 'invoiceShow'])->name('portal.invoices.show');
    Route::get('/my/invoices/{invoice}/print', [Portal\StudentPaymentController::class, 'invoicePrint'])->name('portal.invoices.print');
    Route::get('/my/invoices/{invoice}/pdf', [Portal\StudentPaymentController::class, 'invoicePdf'])->name('portal.invoices.pdf');

    Route::get('/my/payments', [Portal\StudentPaymentController::class, 'index'])->name('portal.payments.index');
    Route::post('/my/payments/offline', [Portal\StudentPaymentController::class, 'submitOffline'])->name('portal.payments.offline.store');
    Route::post('/my/payments/online/initiate', [Portal\StudentPaymentController::class, 'initiateOnline'])->name('portal.payments.online.initiate');
    Route::get('/my/payments/paystack/callback', [Portal\StudentPaymentController::class, 'paystackCallback'])->name('portal.payments.paystack.callback');
    Route::get('/my/payments/flutterwave/callback', [Portal\StudentPaymentController::class, 'flutterwaveCallback'])->name('portal.payments.flutterwave.callback');
    Route::get('/my/payments/receipts/{receipt}', [Portal\StudentPaymentController::class, 'receipt'])->name('portal.payments.receipt');
    Route::get('/my/payments/receipts/{receipt}/pdf', [Portal\StudentPaymentController::class, 'receiptPdf'])->name('portal.payments.receipt.pdf');
});

// Gateway webhooks
Route::post('/webhooks/paystack', [Financial\PaymentWebhookController::class, 'paystack'])
    ->name('webhooks.paystack')
    ->middleware('throttle:120,1')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/webhooks/flutterwave', [Financial\PaymentWebhookController::class, 'flutterwave'])
    ->name('webhooks.flutterwave')
    ->middleware('throttle:120,1')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
