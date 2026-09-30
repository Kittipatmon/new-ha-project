<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\RequestData\SectionController;
use App\Http\Controllers\Backend\RequestData\DivisionController;
use App\Http\Controllers\Backend\RequestData\DepartmentController;
use App\Http\Controllers\hrrequest\RequestDataController;
use App\Http\Controllers\Api\UserController;

// Internal user and master-data APIs (protected by auth middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('api.users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('api.users.show');

    Route::get('/sections', [SectionController::class, 'apiSection']);
    Route::get('/divisions', [DivisionController::class, 'apiDivision']);
    Route::get('/departments', [DepartmentController::class, 'apiDepartment']);
    
    // HR Request dynamic data
    Route::get('/request-types', [RequestDataController::class, 'types']);
    Route::get('/request-subtypes', [RequestDataController::class, 'subtypes']);
});
    
