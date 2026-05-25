<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\ProfileController;
use App\Http\Controllers\frontend\SystemController;

use App\Http\Controllers\backend\RequestHRController;
use App\Http\Controllers\backend\requestdata\RequestDataController;
use App\Http\Controllers\backend\users\SectionController;
use App\Http\Controllers\backend\users\DivisionController;
use App\Http\Controllers\backend\users\DepartmentController;

use App\Http\Controllers\backend\hrrequest\RequestCategoriesController;
use App\Http\Controllers\backend\hrrequest\RequestTypeController;
use App\Http\Controllers\backend\hrrequest\RequestSubtypeController;

use App\Http\Controllers\backend\ApproveController;
use App\Http\Controllers\backend\LeaveReportsController;


use App\Http\Controllers\backend\NewsController;
use App\Http\Controllers\backend\SuggestionController;
use App\Http\Controllers\backend\TrainingController;
use App\Http\Controllers\backend\users\UserController;
use App\Http\Controllers\backend\users\UserTypeController;

use App\Http\Controllers\backend\manpower\ManpowerController;

use App\Models\datacenter\News;
use App\Models\hrrequest\HrRequests;

Route::get('/api/departments', [DepartmentController::class, 'apiDepartments']);
Route::resource('departments', DepartmentController::class);

Route::get('/api/sections', [SectionController::class, 'apiSections']);
Route::resource('sections', SectionController::class);

Route::get('/api/divisions', [DivisionController::class, 'apiDivisions']);
Route::resource('division', DivisionController::class);

Route::get('/api/users', [UserController::class, 'apiUsers']);
Route::resource('users', UserController::class);

Route::get('/', function () {
    $newsItems = News::where('is_active', true)
        ->orderBy('published_date', 'desc')
        ->get();

    $highlight = $newsItems->first();
    $otherNews = $newsItems->slice(1);

    return view('welcome', [
        'highlight' => $highlight,
        'otherNews' => $otherNews,
        'newsItems' => $newsItems
    ]);
})->name('welcome');

Route::get('/dashboard', function () {
    if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
        abort(403);
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// welcomeSystem
Route::get('/welcome-system', [SystemController::class, 'welcomeSystem'])->name('welcome.system');
Route::get('/requestHR/dashboard', [RequestHRController::class, 'dashboard'])->name('requesthr.dashboard');
Route::get('/requestHR/dashboard/filter', [RequestHRController::class, 'dashboardFilter'])->name('requesthr.dashboard.filter');


Route::get('/manpower/dashboard', [ManpowerController::class, 'dashboard'])->name('manpower.dashboard');
Route::get('/manpower/index', [ManpowerController::class, 'index'])->name('manpower.index');
Route::get('/manpower/export/excel', [ManpowerController::class, 'exportExcel'])->name('manpower.export.excel');
Route::get('/manpower/export/pdf', [ManpowerController::class, 'exportPdf'])->name('manpower.export.pdf');

Route::get('news/detail/{id}', [NewsController::class, 'detail'])->name('news.detail');
Route::get('news-all', [NewsController::class, 'newsAll'])->name('news.newsAll');

Route::get('news-all', [NewsController::class, 'newsAll'])->name('news.newsAll');

// Public Recruitment Routes
Route::get('/recruitment', [App\Http\Controllers\Frontend\RecruitmentController::class, 'index'])->name('recruitment.index');
Route::get('/recruitment/reports', [App\Http\Controllers\Frontend\RecruitmentController::class, 'requestReport'])->name('recruitment.reports')->middleware('auth');
Route::get('/recruitment/reports/{id}', [App\Http\Controllers\Frontend\RecruitmentController::class, 'requestShow'])->name('recruitment.request_show')->middleware('auth');
Route::get('/recruitment/job/{slug}', [App\Http\Controllers\Frontend\RecruitmentController::class, 'show'])->name('recruitment.show');
Route::get('/recruitment/apply/{slug}', [App\Http\Controllers\Frontend\RecruitmentController::class, 'apply'])->name('recruitment.apply');
Route::post('/recruitment/apply/{slug}', [App\Http\Controllers\Frontend\RecruitmentController::class, 'submitApplication'])->name('recruitment.submit');
Route::get('/recruitment/success/{slug}', [App\Http\Controllers\Frontend\RecruitmentController::class, 'success'])->name('recruitment.success');

Route::middleware('auth')->group(function () {

    Route::get('/welcomehrrequest', [RequestHRController::class, 'welcomeRequest'])->name('request.hr');
    Route::get('/request-data', [RequestDataController::class, 'welcomeData'])->name('request.data');

    Route::get('/requestHR', [RequestHRController::class, 'requestHR'])->name('requesthr.index');
    Route::get('/requestHR/list', [RequestHRController::class, 'requesthrList'])->name('requesthr.list');
    Route::get('/requestHR/listall', [RequestHRController::class, 'requesthrlistall'])->name('requesthr.listall');
    Route::get('/requestHR/detail/{id}', [RequestHRController::class, 'detailUser'])->name('requesthr.detailUser');
    Route::post('/requestHR/store', [RequestHRController::class, 'requestStore'])->name('request.store');
    Route::get('/requestHR/edit/{id}', [RequestHRController::class, 'requestHREdit'])->name('requesthr.edit');
    Route::post('/requestHR/update/{id}', [RequestHRController::class, 'requestUpdate'])->name('request.update');

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


    Route::resource('sections', SectionController::class);
    Route::resource('divisions', DivisionController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('users', UserController::class);
    Route::post('users/profile/avatar', [UserController::class, 'updateAvatar'])->name('users.update_avatar');
    Route::delete('users/destroy/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::resource('usertypes', UserTypeController::class);
    // Route::post('users/storeUser', [UserController::class, 'store'])->name('users.storeUser');


    Route::resource('request-categories', RequestCategoriesController::class);
    Route::get('request-types', [RequestTypeController::class, 'index'])->name('request-types.index');
    Route::post('request-types', [RequestTypeController::class, 'store'])->name('request-types.store');
    Route::put('request-types/{id}', [RequestTypeController::class, 'update'])->name('request-types.update');
    Route::delete('request-types/{id}', [RequestTypeController::class, 'destroy'])->name('request-types.destroy');

    Route::get('request-subtypes', [RequestSubtypeController::class, 'index'])->name('request-subtypes.index');
    Route::post('request-subtypes', [RequestSubtypeController::class, 'store'])->name('request-subtypes.store');
    Route::put('request-subtypes/{id}', [RequestSubtypeController::class, 'update'])->name('request-subtypes.update');
    Route::delete('request-subtypes/{id}', [RequestSubtypeController::class, 'destroy'])->name('request-subtypes.destroy');

    //News
    Route::resource('news', NewsController::class);

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
    Route::delete('/suggestion/{id}', [SuggestionController::class, 'destroy'])->name('suggestion.destroy');

    //Training Backend
    Route::get('/backend/training/dashboard', [TrainingController::class, 'dashboard'])->name('backend.training.dashboard');
    Route::get('/backend/training/applicants', [TrainingController::class, 'applicants'])->name('backend.training.applicants');
    Route::get('/backend/training/{id}/applicants', [TrainingController::class, 'courseApplicants'])->name('backend.training.course.applicants');
    Route::get('/backend/training', [TrainingController::class, 'index'])->name('backend.training.index');
    Route::get('/backend/training/create', [TrainingController::class, 'create'])->name('backend.training.create');
    Route::post('/backend/training', [TrainingController::class, 'store'])->name('backend.training.store');
    Route::get('/backend/training/{id}/edit', [TrainingController::class, 'edit'])->name('backend.training.edit');
    Route::put('/backend/training/{id}', [TrainingController::class, 'update'])->name('backend.training.update');
    Route::delete('/backend/training/{id}', [TrainingController::class, 'destroy'])->name('backend.training.destroy');

    // Leave Reports
    Route::get('/leavereports/dashboard', [LeaveReportsController::class, 'dashboard'])->name('leavereports.dashboard');
    Route::post('/leavereports/import', [LeaveReportsController::class, 'import'])->name('leavereports.import');
    Route::get('/leavereports/pdf', [LeaveReportsController::class, 'exportData'])->name('leavereports.pdf');

    // Training Frontend
    Route::get('/training/dashboard', [\App\Http\Controllers\TrainingController::class, 'dashboard'])->name('training.dashboard');
    Route::get('/training', [App\Http\Controllers\TrainingController::class, 'index'])->name('training.index');
    Route::get('/training/apply/{id?}', [App\Http\Controllers\TrainingController::class, 'apply'])->name('training.apply');
    Route::post('/training/store', [App\Http\Controllers\TrainingController::class, 'store'])->name('training.store');

    // Recruitment System
    Route::prefix('backend/recruitment')->name('backend.recruitment.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Backend\Recruitment\DashboardController::class, 'index'])->name('dashboard');

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
        Route::delete('/posts/{jobPost}', [App\Http\Controllers\Backend\Recruitment\JobPostController::class, 'destroy'])->name('posts.destroy');

        // Applicant Management

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
    });

});
// 
require __DIR__ . '/auth.php';