<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\backend\requestdata\SectionController;
use App\Http\Controllers\backend\requestdata\DivisionController;
use App\Http\Controllers\backend\requestdata\DepartmentController;
use App\Http\Controllers\hrrequest\RequestDataController;
use App\Http\Controllers\Api\UserController;

// Public user lookup APIs (throttled by 'api' middleware group)
Route::middleware('api')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('api.users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('api.users.show');
});


Route::get('/sections', [SectionController::class, 'apiSection']);
Route::get('/divisions', [DivisionController::class, 'apiDivision']);
Route::get('/departments', [DepartmentController::class, 'apiDepartment']);
// HR Request dynamic data
Route::get('/request-types', [RequestDataController::class, 'types']);
Route::get('/request-subtypes', [RequestDataController::class, 'subtypes']);
    
