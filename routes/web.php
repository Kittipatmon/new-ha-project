<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Frontend\SystemController;

use App\Http\Controllers\Backend\RequestHRController;
use App\Http\Controllers\Backend\RequestData\RequestDataController;
use App\Http\Controllers\Backend\Users\SectionController;
use App\Http\Controllers\Backend\Users\DivisionController;
use App\Http\Controllers\Backend\Users\DepartmentController;

use App\Http\Controllers\Backend\HrRequest\RequestCategoriesController;
use App\Http\Controllers\Backend\HrRequest\RequestTypeController;
use App\Http\Controllers\Backend\HrRequest\RequestSubtypeController;

use App\Http\Controllers\Backend\ApproveController;
use App\Http\Controllers\Backend\LeaveReportsController;


use App\Http\Controllers\Backend\NewsController;
use App\Http\Controllers\Backend\PosterController;
use App\Http\Controllers\Backend\HeroBackgroundController;
use App\Http\Controllers\Backend\SuggestionController;
use App\Http\Controllers\Backend\TrainingController;
use App\Http\Controllers\Backend\Users\UserController;
use App\Http\Controllers\Backend\Users\UserTypeController;

use App\Http\Controllers\Backend\Manpower\ManpowerController;

use App\Models\datacenter\News;
use App\Models\datacenter\Poster;
use App\Models\datacenter\HeroBackground;
use App\Models\hrrequest\HrRequests;



Route::get('/', function () {
    $newsItems = News::where('is_active', true)
        ->orderBy('published_date', 'desc')
        ->get();

    $highlight = $newsItems->first();
    $otherNews = $newsItems->slice(1);

    // Get active posters grouped or sorted (with scheduling check)
    $heroBannerPosters = Poster::published()
        ->where('position', 'hero_banner')
        ->orderBy('sort_order', 'asc')
        ->orderBy('id', 'desc')
        ->get();

    $heroTopPosters = Poster::published()
        ->where('position', 'hero_top')
        ->orderBy('sort_order', 'asc')
        ->orderBy('id', 'desc')
        ->get();

    $mainPosters = Poster::published()
        ->where('position', 'main_carousel')
        ->orderBy('sort_order', 'asc')
        ->orderBy('id', 'desc')
        ->get();

    $sideTopPoster = Poster::published()
        ->where('position', 'side_top')
        ->orderBy('sort_order', 'asc')
        ->orderBy('id', 'desc')
        ->first();

    $sideBottomPoster = Poster::published()
        ->where('position', 'side_bottom')
        ->orderBy('sort_order', 'asc')
        ->orderBy('id', 'desc')
        ->first();

    // Get hero background images (separate from posters)
    $heroBackgrounds = HeroBackground::active()
        ->orderBy('sort_order', 'asc')
        ->orderBy('id', 'desc')
        ->get();

    return view('welcome', [
        'highlight' => $highlight,
        'otherNews' => $otherNews,
        'newsItems' => $newsItems,
        'heroBannerPosters' => $heroBannerPosters,
        'heroTopPosters' => $heroTopPosters,
        'mainPosters' => $mainPosters,
        'sideTopPoster' => $sideTopPoster,
        'sideBottomPoster' => $sideBottomPoster,
        'heroBackgrounds' => $heroBackgrounds,
    ]);
})->name('welcome');

Route::get('/dashboard', [RequestDataController::class, 'welcomeData'])->middleware(['auth', 'verified'])->name('dashboard');



Route::get('news/detail/{id}', [NewsController::class, 'detail'])->name('news.detail');
Route::get('news-all', [NewsController::class, 'newsAll'])->name('news.newsAll');
Route::get('news/analytics/data', [NewsController::class, 'analyticsData'])->name('news.analytics.data')->middleware(['auth', 'hr.admin']);

// Poster Tracking & Redirection Routes
Route::post('posters/track-views', [PosterController::class, 'trackViews'])->name('posters.track-views');
Route::get('posters/click/{poster}', [PosterController::class, 'handleClick'])->name('posters.click');
Route::get('posters/analytics/data', [PosterController::class, 'analyticsData'])->name('posters.analytics.data')->middleware(['auth', 'hr.admin']);


// Public Recruitment Routes
Route::get('/api/thai-addresses/search', [App\Http\Controllers\Frontend\RecruitmentController::class, 'searchAddress'])->name('api.thai_address.search');
Route::get('/recruitment', [App\Http\Controllers\Frontend\RecruitmentController::class, 'index'])->name('recruitment.index');
Route::get('/recruitment/reports', [App\Http\Controllers\Frontend\RecruitmentController::class, 'requestReport'])->name('recruitment.reports')->middleware('auth');
Route::get('/recruitment/reports/{id}', [App\Http\Controllers\Frontend\RecruitmentController::class, 'requestShow'])->name('recruitment.request_show')->middleware('auth');
Route::get('/recruitment/job/{slug}', [App\Http\Controllers\Frontend\RecruitmentController::class, 'show'])->name('recruitment.show');
Route::get('/recruitment/apply/{slug}', [App\Http\Controllers\Frontend\RecruitmentController::class, 'apply'])->name('recruitment.apply');
Route::post('/recruitment/apply/{slug}', [App\Http\Controllers\Frontend\RecruitmentController::class, 'submitApplication'])->name('recruitment.submit')->middleware('throttle:10,1');
Route::get('/recruitment/success/{slug}', [App\Http\Controllers\Frontend\RecruitmentController::class, 'success'])->name('recruitment.success');

// Candidate Application Status Tracking (Self-Service)
Route::get('/recruitment/track', [App\Http\Controllers\Frontend\RecruitmentController::class, 'trackForm'])->name('recruitment.track');
Route::post('/recruitment/track', [App\Http\Controllers\Frontend\RecruitmentController::class, 'trackStatus'])->name('recruitment.track.post')->middleware('throttle:60,1');

Route::middleware('auth')->group(function () {
    // Secure Recruitment Document & Photo Access (Role-based & Access-controlled)
    Route::get('/recruitment/documents/{id}', [App\Http\Controllers\Backend\Recruitment\DocumentController::class, 'show'])->name('recruitment.documents.show');
    Route::get('/recruitment/applications/{id}/photo', [App\Http\Controllers\Backend\Recruitment\DocumentController::class, 'photo'])->name('recruitment.documents.photo');

    // welcomeSystem
    Route::get('/welcome-system', [SystemController::class, 'welcomeSystem'])->name('welcome.system');
    Route::get('/requestHR/dashboard', [RequestHRController::class, 'dashboard'])->name('requesthr.dashboard');
    Route::get('/requestHR/dashboard/filter', [RequestHRController::class, 'dashboardFilter'])->name('requesthr.dashboard.filter');

    Route::get('/manpower/dashboard', [ManpowerController::class, 'dashboard'])->name('manpower.dashboard');
    Route::get('/manpower/index', [ManpowerController::class, 'index'])->name('manpower.index');
    Route::get('/manpower/export/excel', [ManpowerController::class, 'exportExcel'])->name('manpower.export.excel');
    Route::get('/manpower/export/pdf', [ManpowerController::class, 'exportPdf'])->name('manpower.export.pdf');

    // HR Forms: Manpower Request (QF-HR-13)
    Route::get('/manpower-request', [App\Http\Controllers\ManpowerRequestController::class, 'index'])->name('manpower-request.index');
    Route::get('/manpower-request/create', [App\Http\Controllers\ManpowerRequestController::class, 'create'])->name('manpower-request.create');
    Route::post('/manpower-request/store', [App\Http\Controllers\ManpowerRequestController::class, 'store'])->name('manpower-request.store');
    Route::get('/manpower-request/show/{id}', [App\Http\Controllers\ManpowerRequestController::class, 'show'])->name('manpower-request.show');
    Route::get('/manpower-request/pdf/{id}', [App\Http\Controllers\ManpowerRequestController::class, 'exportPdf'])->name('manpower-request.pdf');
    // Route::delete('/manpower-request/{id}', [App\Http\Controllers\ManpowerRequestController::class, 'destroy'])->name('manpower-request.destroy');
    Route::get('/manpower-request/data/requests', [App\Http\Controllers\ManpowerRequestController::class, 'dataTableRequests'])->name('manpower-request.data.requests');
    Route::get('/manpower-request/data/requests-alias', [App\Http\Controllers\ManpowerRequestController::class, 'dataTableRequests'])->name('manpower-request.data');
    Route::get('/manpower-request/data/probations', [App\Http\Controllers\ManpowerRequestController::class, 'dataTableProbations'])->name('manpower-request.data.probations');
    Route::get('/manpower-request/data/all', [App\Http\Controllers\ManpowerRequestController::class, 'dataTableAllRequests'])->name('manpower-request.data.all');
    Route::get('/manpower-request/data/probations-alias', [App\Http\Controllers\ManpowerRequestController::class, 'dataTableProbations'])->name('manpower-request.probations-data');

    // HR Forms: Probation Evaluation (QF-HR-18)
    Route::get('/probation-evaluation/create', [App\Http\Controllers\ProbationEvaluationController::class, 'create'])->name('probation-evaluation.create');
    Route::post('/probation-evaluation/store', [App\Http\Controllers\ProbationEvaluationController::class, 'store'])->name('probation-evaluation.store');
    Route::get('/probation-evaluation/show/{id}', [App\Http\Controllers\ProbationEvaluationController::class, 'show'])->name('probation-evaluation.show');
    Route::post('/probation-evaluation/{id}/sign', [App\Http\Controllers\ProbationEvaluationController::class, 'sign'])->name('admin.probation-evaluations.sign');
    Route::get('/probation-evaluation/pdf/{id}', [App\Http\Controllers\ProbationEvaluationController::class, 'exportPdf'])->name('probation-evaluation.pdf');
    // Route::delete('/probation-evaluation/{id}', [App\Http\Controllers\ProbationEvaluationController::class, 'destroy'])->name('probation-evaluation.destroy');

    // HR Forms: Interview Evaluation (QF-HR-25)
    Route::get('/interview-evaluation/create', [App\Http\Controllers\InterviewEvaluationController::class, 'create'])->name('interview-evaluation.create');
    Route::post('/interview-evaluation/store', [App\Http\Controllers\InterviewEvaluationController::class, 'store'])->name('interview-evaluation.store');
    Route::get('/interview-evaluation/show/{id}', [App\Http\Controllers\InterviewEvaluationController::class, 'show'])->name('interview-evaluation.show');
    Route::post('/interview-evaluation/{id}/sign', [App\Http\Controllers\InterviewEvaluationController::class, 'sign'])->name('admin.interview-evaluations.sign');
    Route::get('/interview-evaluation/pdf/{id}', [App\Http\Controllers\InterviewEvaluationController::class, 'exportPdf'])->name('interview-evaluation.pdf');
    // Route::delete('/interview-evaluation/{id}', [App\Http\Controllers\InterviewEvaluationController::class, 'destroy'])->name('interview-evaluation.destroy');

    // Form Sharing & Permission Routes
    Route::post('/form-shares', [App\Http\Controllers\FormShareController::class, 'store'])->name('form-shares.store');
    Route::get('/form-shares/history', [App\Http\Controllers\FormShareController::class, 'history'])->name('form-shares.history');
    Route::get('/form-shares/search-targets', [App\Http\Controllers\FormShareController::class, 'searchTargets'])->name('form-shares.search-targets');
    Route::delete('/form-shares/{id}', [App\Http\Controllers\FormShareController::class, 'destroy'])->name('form-shares.destroy');

    // Admin HR Consideration routes
    Route::get('/admin/manpower-requests', [App\Http\Controllers\Admin\ManpowerRequestController::class, 'index'])->name('admin.manpower-requests.index');
    Route::get('/admin/manpower-requests/data', [App\Http\Controllers\Admin\ManpowerRequestController::class, 'dataTable'])->name('admin.manpower-requests.data');
    Route::get('/admin/manpower-requests/{id}', [App\Http\Controllers\Admin\ManpowerRequestController::class, 'show'])->name('admin.manpower-requests.show');
    Route::post('/admin/manpower-requests/{manpowerRequest}/approve', [App\Http\Controllers\Admin\ManpowerRequestController::class, 'approve'])->name('admin.manpower-requests.approve');
    Route::post('/admin/manpower-requests/{manpowerRequest}/reject', [App\Http\Controllers\Admin\ManpowerRequestController::class, 'reject'])->name('admin.manpower-requests.reject');
    Route::get('/admin/probation-evaluations', [App\Http\Controllers\Admin\ProbationEvaluationController::class, 'index'])->name('admin.probation-evaluations.index');
    Route::get('/admin/probation-evaluations/data', [App\Http\Controllers\Admin\ProbationEvaluationController::class, 'dataTable'])->name('admin.probation-evaluations.data');
    Route::get('/admin/interview-evaluations', [App\Http\Controllers\Admin\InterviewEvaluationController::class, 'index'])->name('admin.interview-evaluations.index');
    Route::get('/admin/interview-evaluations/data', [App\Http\Controllers\Admin\InterviewEvaluationController::class, 'dataTable'])->name('admin.interview-evaluations.data');

    Route::get('/welcomehrrequest', [RequestHRController::class, 'welcomeRequest'])->name('request.hr');
    Route::get('/request-data', [RequestDataController::class, 'welcomeData'])->name('request.data');

    Route::get('/requestHR', [RequestHRController::class, 'requestHR'])->name('requesthr.index');
    Route::get('/requestHR/list', [RequestHRController::class, 'requesthrList'])->name('requesthr.list');
    Route::get('/requestHR/listall', [RequestHRController::class, 'requesthrlistall'])->name('requesthr.listall');
    Route::get('/requestHR/detail/{id}', [RequestHRController::class, 'detailUser'])->name('requesthr.detailUser');
    Route::post('/requestHR/store', [RequestHRController::class, 'requestStore'])->name('request.store');
    Route::get('/requestHR/edit/{id}', [RequestHRController::class, 'requestHREdit'])->name('requesthr.edit');
    Route::post('/requestHR/update/{id}', [RequestHRController::class, 'requestUpdate'])->name('request.update');
    Route::delete('/requestHR/{id}', [RequestHRController::class, 'destroy'])->name('requesthr.destroy');

    //hrlist
    Route::get('/approvehrlist', [ApproveController::class, 'approvehrlist'])->name('approve.approvehrlist');
    Route::get('/detailHR/{id}', [RequestHRController::class, 'detailHr'])->name('requesthr.detailhr');
    Route::post('/hrCheck/{id}', [ApproveController::class, 'hrCheck'])->name('approve.hrCheck');
    //hrlistall
    Route::get('/approvehrlistall', [ApproveController::class, 'approvehrlistall'])->name('approve.approvehrlistall');
    Route::get('/approvehrlistall/export', [ApproveController::class, 'approvehrlistallExport'])->name('approve.approvehrlistall.export');
    Route::get('/approvehrlistall/data', [ApproveController::class, 'approvehrlistallData'])->name('approve.approvehrlistall.data');
    Route::get('/approvehrlistall/pdf', [ApproveController::class, 'approvehrlistallPdf'])->name('approve.approvehrlistall.pdf');

    //manager
    Route::get('/approvemanalist', [ApproveController::class, 'approvemanalist'])->name('approve.approvemanalist');
    Route::get('/detailMana/{id}', [RequestHRController::class, 'detailMana'])->name('requesthr.detailMana');
    Route::post('/managerCheck/{id}', [ApproveController::class, 'managerCheck'])->name('approve.managerCheck');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // All Notifications Center
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');


    Route::middleware('hr.admin')->group(function () {
        Route::get('/api/departments', [DepartmentController::class, 'apiDepartments']);
        Route::get('/api/sections', [SectionController::class, 'apiSections']);
        Route::get('/api/divisions', [DivisionController::class, 'apiDivisions']);
        Route::get('/api/users', [UserController::class, 'apiUsers']);

        Route::resource('sections', SectionController::class);
        Route::resource('divisions', DivisionController::class);
        Route::resource('departments', DepartmentController::class);
        // ข้อมูลพนักงาน และประเภทผู้ใช้งาน: เฉพาะ ADMIN เท่านั้น (ห้าม EDITOR, ห้าม VIEWER)
        Route::middleware('role:admin')->group(function () {
            Route::resource('users', UserController::class);
            Route::post('users/{id}/change-role', [UserController::class, 'updateRole'])->name('users.update_role');
            Route::resource('usertypes', UserTypeController::class);
            Route::delete('sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');
            Route::delete('divisions/{division}', [DivisionController::class, 'destroy'])->name('divisions.destroy');
            Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
            Route::delete('request-categories/{request_category}', [RequestCategoriesController::class, 'destroy'])->name('request-categories.destroy');
            Route::delete('request-types/{id}', [RequestTypeController::class, 'destroy'])->name('request-types.destroy');
            Route::delete('request-subtypes/{id}', [RequestSubtypeController::class, 'destroy'])->name('request-subtypes.destroy');
            Route::delete('news/{news}', [NewsController::class, 'destroy'])->name('news.destroy');
            Route::delete('posters/{poster}', [PosterController::class, 'destroy'])->name('posters.destroy');
            Route::delete('hero-backgrounds/{hero_background}', [HeroBackgroundController::class, 'destroy'])->name('hero-backgrounds.destroy');
        });

        Route::resource('request-categories', RequestCategoriesController::class)->except(['destroy']);
        Route::get('request-types', [RequestTypeController::class, 'index'])->name('request-types.index');
        Route::post('request-types', [RequestTypeController::class, 'store'])->name('request-types.store');
        Route::put('request-types/{id}', [RequestTypeController::class, 'update'])->name('request-types.update');

        Route::get('request-subtypes', [RequestSubtypeController::class, 'index'])->name('request-subtypes.index');
        Route::post('request-subtypes', [RequestSubtypeController::class, 'store'])->name('request-subtypes.store');
        Route::put('request-subtypes/{id}', [RequestSubtypeController::class, 'update'])->name('request-subtypes.update');

        //News & Posters
        Route::resource('news', NewsController::class);
        Route::resource('posters', PosterController::class)->except(['destroy']);

        // Hero Background Management
        Route::resource('hero-backgrounds', HeroBackgroundController::class)->except(['destroy']);
        Route::post('hero-backgrounds/{hero_background}/toggle-active', [HeroBackgroundController::class, 'toggleActive'])->name('hero-backgrounds.toggle-active');

        // Training Backend
        Route::get('/backend/training/dashboard', [TrainingController::class, 'dashboard'])->name('backend.training.dashboard');
        Route::get('/backend/training/applicants', [TrainingController::class, 'applicants'])->name('backend.training.applicants');
        Route::get('/backend/training/{id}/applicants', [TrainingController::class, 'courseApplicants'])->name('backend.training.course.applicants');
        Route::get('/backend/training', [TrainingController::class, 'index'])->name('backend.training.index');
        Route::get('/backend/training/create', [TrainingController::class, 'create'])->name('backend.training.create');
        Route::post('/backend/training', [TrainingController::class, 'store'])->name('backend.training.store');
        Route::get('/backend/training/{id}/edit', [TrainingController::class, 'edit'])->name('backend.training.edit');
        Route::put('/backend/training/{id}', [TrainingController::class, 'update'])->name('backend.training.update');
        Route::delete('/backend/training/{id}', [TrainingController::class, 'destroy'])->name('backend.training.destroy')->middleware('role:admin');

        // Leave Reports
        Route::get('/leavereports/dashboard', [LeaveReportsController::class, 'dashboard'])->name('leavereports.dashboard');
        Route::post('/leavereports/import', [LeaveReportsController::class, 'import'])->name('leavereports.import');
        Route::get('/leavereports/pdf', [LeaveReportsController::class, 'exportData'])->name('leavereports.pdf');

        // Recruitment System Backend
        Route::prefix('backend/recruitment')->name('backend.recruitment.')->group(function () {
            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Backend\Recruitment\DashboardController::class, 'index'])->name('dashboard');
            Route::get('/analytics/data', [App\Http\Controllers\Backend\Recruitment\DashboardController::class, 'analyticsData'])->name('analytics.data');

            // Requests
            Route::get('/requests', [App\Http\Controllers\Backend\Recruitment\RequestController::class, 'index'])->name('requests.index');
            Route::get('/requests/create', [App\Http\Controllers\Backend\Recruitment\RequestController::class, 'create'])->name('requests.create');
            Route::post('/requests', [App\Http\Controllers\Backend\Recruitment\RequestController::class, 'store'])->name('requests.store');
            Route::get('/requests/{recruitmentRequest}', [App\Http\Controllers\Backend\Recruitment\RequestController::class, 'show'])->name('requests.show');
            Route::post('/requests/{recruitmentRequest}/approve', [App\Http\Controllers\Backend\Recruitment\RequestController::class, 'approve'])->name('requests.approve');
            Route::post('/requests/{recruitmentRequest}/reject', [App\Http\Controllers\Backend\Recruitment\RequestController::class, 'reject'])->name('requests.reject');
            Route::post('/requests/{recruitmentRequest}/update-approver', [App\Http\Controllers\Backend\Recruitment\RequestController::class, 'updateApprover'])->name('requests.update-approver');

            // Job Posts
            Route::get('/posts', [App\Http\Controllers\Backend\Recruitment\JobPostController::class, 'index'])->name('posts.index');
            Route::get('/posts/create', [App\Http\Controllers\Backend\Recruitment\JobPostController::class, 'create'])->name('posts.create');
            Route::post('/posts', [App\Http\Controllers\Backend\Recruitment\JobPostController::class, 'store'])->name('posts.store');
            Route::get('/posts/{jobPost}/edit', [App\Http\Controllers\Backend\Recruitment\JobPostController::class, 'edit'])->name('posts.edit');
            Route::put('/posts/{jobPost}', [App\Http\Controllers\Backend\Recruitment\JobPostController::class, 'update'])->name('posts.update');
            Route::delete('/posts/{jobPost}', [App\Http\Controllers\Backend\Recruitment\JobPostController::class, 'destroy'])->name('posts.destroy')->middleware('role:admin');

            // Applicant Management
            Route::get('/applications', [App\Http\Controllers\Backend\Recruitment\ApplicantController::class, 'index'])->name('applications.index');
            Route::get('/applications/{application}', [App\Http\Controllers\Backend\Recruitment\ApplicantController::class, 'show'])->name('applications.show');
            Route::post('/applications/{application}/update-status', [App\Http\Controllers\Backend\Recruitment\ApplicantController::class, 'updateStatus'])->name('applications.update-status');

            // Interviews
            Route::post('/applications/{application}/interviews', [App\Http\Controllers\Backend\Recruitment\InterviewController::class, 'store'])->name('interviews.store');
            Route::post('/interviews/{interview}/status', [App\Http\Controllers\Backend\Recruitment\InterviewController::class, 'updateStatus'])->name('interviews.update-status');
            Route::post('/interviews/{interview}/scores', [App\Http\Controllers\Backend\Recruitment\InterviewController::class, 'storeScores'])->name('interviews.store-scores');

            // Email API
            Route::post('/applications/{application}/send-email', [App\Http\Controllers\Backend\Recruitment\EmailController::class, 'sendDirectEmail'])->name('applications.send-email');

            // Mail Logs
            Route::get('/mail-logs', [App\Http\Controllers\Backend\Recruitment\MailLogController::class, 'index'])->name('mail-logs.index');
            Route::post('/mail-logs/{log}/retry', [App\Http\Controllers\Backend\Recruitment\MailLogController::class, 'retry'])->name('mail-logs.retry');

            // Email Templates Management & Live Editor
            Route::get('/email-templates', [App\Http\Controllers\Backend\Recruitment\EmailTemplateController::class, 'index'])->name('email-templates.index');
            Route::post('/email-templates/{key}', [App\Http\Controllers\Backend\Recruitment\EmailTemplateController::class, 'update'])->name('email-templates.update');
            Route::post('/email-templates/{key}/reset', [App\Http\Controllers\Backend\Recruitment\EmailTemplateController::class, 'reset'])->name('email-templates.reset');
            Route::post('/email-templates/{key}/test-send', [App\Http\Controllers\Backend\Recruitment\EmailTemplateController::class, 'testSend'])->name('email-templates.test-send');
        });

        // Microsoft 365 / Azure Entra ID Settings (Admin only)
        Route::prefix('backend/settings')->name('backend.settings.')->middleware('role:admin')->group(function () {
            Route::get('/microsoft', [App\Http\Controllers\Backend\Settings\MicrosoftSettingController::class, 'index'])->name('microsoft');
            Route::post('/microsoft', [App\Http\Controllers\Backend\Settings\MicrosoftSettingController::class, 'update'])->name('microsoft.update');
            Route::post('/microsoft/test', [App\Http\Controllers\Backend\Settings\MicrosoftSettingController::class, 'testConnection'])->name('microsoft.test');
            Route::delete('/microsoft/users/{id}', [App\Http\Controllers\Backend\Settings\MicrosoftSettingController::class, 'disconnectUser'])->name('microsoft.disconnect-user');
        });

        // System Audit Logs & 5-Year Archive Ledger (Admin only)
        Route::prefix('backend/audit-logs')->name('backend.audit-logs.')->middleware('role:admin')->group(function () {
            Route::get('/', [App\Http\Controllers\Backend\AuditLogController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\Backend\AuditLogController::class, 'getLogsData'])->name('data');
            Route::post('/archive', [App\Http\Controllers\Backend\AuditLogController::class, 'createArchive'])->name('archive');
            Route::post('/archives/clean-expired', [App\Http\Controllers\Backend\AuditLogController::class, 'cleanExpiredArchives'])->name('archives.clean-expired');
            Route::get('/archives/{id}/download', [App\Http\Controllers\Backend\AuditLogController::class, 'downloadArchive'])->name('archives.download');
            Route::get('/archives/{id}/inspect', [App\Http\Controllers\Backend\AuditLogController::class, 'inspectArchive'])->name('archives.inspect');
            Route::get('/{id}', [App\Http\Controllers\Backend\AuditLogController::class, 'show'])->name('show');
        });

        // Database Automated Backups (Admin only)
        Route::prefix('backend/database-backups')->name('backend.database-backups.')->middleware('role:admin')->group(function () {
            Route::get('/', [App\Http\Controllers\Backend\DatabaseBackupController::class, 'index'])->name('index');
            Route::post('/create', [App\Http\Controllers\Backend\DatabaseBackupController::class, 'create'])->name('create');
            Route::get('/{id}/download', [App\Http\Controllers\Backend\DatabaseBackupController::class, 'download'])->name('download');
            Route::get('/{id}/download-md', [App\Http\Controllers\Backend\DatabaseBackupController::class, 'downloadMd'])->name('download-md');
            Route::get('/{id}/password', [App\Http\Controllers\Backend\DatabaseBackupController::class, 'showPassword'])->name('show-password');
            Route::delete('/{id}', [App\Http\Controllers\Backend\DatabaseBackupController::class, 'destroy'])->name('destroy');
            Route::post('/clean-old', [App\Http\Controllers\Backend\DatabaseBackupController::class, 'cleanOld'])->name('clean-old');
        });
    });

    // Microsoft 365 OAuth
    Route::get('/auth/microsoft/redirect', [App\Http\Controllers\Auth\MicrosoftAuthController::class, 'redirect'])->name('auth.microsoft.redirect');
    Route::get('/auth/microsoft/callback', [App\Http\Controllers\Auth\MicrosoftAuthController::class, 'callback'])->name('auth.microsoft.callback');
    Route::post('/auth/microsoft/disconnect', [App\Http\Controllers\Auth\MicrosoftAuthController::class, 'disconnect'])->name('auth.microsoft.disconnect');

    Route::post('users/profile/avatar', [UserController::class, 'updateAvatar'])->name('users.update_avatar');
    //profile user
    Route::get('users/profile/{id}', [UserController::class, 'profileUser'])->name('users.profile');

    //Suggestion
    Route::get('/api/suggestions', [SuggestionController::class, 'apiSuggestions']);
    Route::get('/suggestion', [SuggestionController::class, 'index'])->name('suggestion.index');
    Route::post('/suggestion', [SuggestionController::class, 'store'])->name('suggestion.store');
    Route::get('/suggestion/dashboard', [SuggestionController::class, 'dashboard'])->name('suggestion.dashboard');
    Route::get('/suggestion/list', [SuggestionController::class, 'list'])->name('suggestion.list');
    Route::get('/suggestion/{id}/show', [SuggestionController::class, 'show'])->name('suggestion.show');
    Route::get('/suggestion/user/{id}/show', [SuggestionController::class, 'userShow'])->name('suggestion.user.show');
    Route::get('/suggestion/{id}/edit', [SuggestionController::class, 'edit'])->name('suggestion.edit');
    Route::put('/suggestion/{id}', [SuggestionController::class, 'update'])->name('suggestion.update');
    Route::delete('/suggestion/{id}', [SuggestionController::class, 'destroy'])->name('suggestion.destroy')->middleware('role:admin');

    // Training Frontend
    Route::get('/training/dashboard', [\App\Http\Controllers\TrainingController::class, 'dashboard'])->name('training.dashboard');
    Route::get('/training', [App\Http\Controllers\TrainingController::class, 'index'])->name('training.index');
    Route::get('/training/guide', [App\Http\Controllers\TrainingController::class, 'guide'])->name('training.guide');
    Route::get('/training/apply/{id?}', [App\Http\Controllers\TrainingController::class, 'apply'])->name('training.apply');
    Route::post('/training/store', [App\Http\Controllers\TrainingController::class, 'store'])->name('training.store');

});
// 
require __DIR__ . '/auth.php';