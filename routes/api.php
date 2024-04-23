<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\ProfileEditController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/admins',[AdminController::class,'addAdmins'])->middleware('auth::admins');
Route::get('/admins/users', [AdminController::class,'getUsers'])->middleware('auth:admins');
Route::post('/admins/users', [AdminController::class,'addUser'])->middleware('auth:admins');
Route::post('/admins/login',[AuthController::class,'adminLogin']);
Route::get('/admins/users/profile-edits',[AdminController::class,'getPendingUsers'])->middleware('auth:admins');
Route::get('/admins/users/{id}/profile-edits',[AdminController::class,'getUserEdits'])->middleware('auth:admins');
Route::post('/admins/users/{id}/profile-edits/approve',[ProfileEditController::class,'approve'])->middleware('auth:admins');
Route::post('/admins/users/{id}/profile-edits/decline',[ProfileEditController::class,'decline'])->middleware('auth:admins');
Route::post('/admins/users/{id}/deactivate',[AdminController::class,'deactiveUser'])->middleware('auth:admins');
Route::post('/admins/users/{id}/activate',[AdminController::class,'activeUser'])->middleware('auth:admins');
Route::get('/admins/fields',[FieldController::class,'index'])->middleware('auth:admins');
Route::get('/admins/notifications',[AdminController::class,'getNotifications'])->middleware('auth:admins');
Route::get('/admins/users/{id}', [AdminController::class,'getUser'])->middleware('auth:admins');

Route::delete('/admins/users/{id}', [AdminController::class,'deleteUser'])->middleware('auth:admins');

Route::get('/admins/charts', [AdminController::class,'charts'])->middleware('auth:admins');

Route::post('/users/login',[UserAuthController::class,'login']);

Route::middleware('auth:users')->group(function() {

    Route::post('/users/profile-edits', [UserController::class, 'store']);
    Route::post('/users/logout',[UserAuthController::class,'logout']);
    Route::put('/users/change-password', [UserAuthController::class, 'changePassword']);
    Route::get('/users/notifications',[UserController::class,'getNotifications']);
    Route::get('/users/profile-edits',[UserController::class,'getUserEdits']);
    Route::get('/users/{id}', [AdminController::class,'getUser']);
});
