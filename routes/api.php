<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\ProfileEditController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/admins/login',[AuthController::class,'adminLogin']);

Route::middleware('auth:admins')->group(function() {

Route::get('/admins/activities',[AdminController::class,'getActivities']);
Route::post('/admins',[AdminController::class,'addAdmins']);
Route::get('/admins/users', [AdminController::class,'getUsers']);
Route::post('/admins/users/{id}/reset-password',[AdminController::class,'restPassword']);
Route::post('/admins/users', [AdminController::class,'addUser']);
Route::get('/admins/users/profile-edits',[AdminController::class,'getPendingUsers']);
Route::get('/admins/users/{id}/profile-edits',[AdminController::class,'getUserEdits']);
Route::post('/admins/users/{id}/profile-edits/approve',[ProfileEditController::class,'approve']);
Route::post('/admins/users/{id}/profile-edits/decline',[ProfileEditController::class,'decline']);
Route::post('/admins/users/{id}/deactivate',[AdminController::class,'deactiveUser']);
Route::post('/admins/users/{id}/activate',[AdminController::class,'activeUser']);
Route::get('/admins/fields',[FieldController::class,'index']);
Route::get('/admins/notifications',[AdminController::class,'getNotifications']);
Route::get('/admins/users/{id}', [AdminController::class,'getUser']);
Route::delete('/admins/users/{id}', [AdminController::class,'deleteUser']);
Route::get('/admins/charts', [AdminController::class,'charts']);

});

Route::post('/users/login',[UserAuthController::class,'login']);

Route::middleware('auth:users')->group(function() {

    Route::post('/users/profile-edits', [UserController::class, 'store']);
    Route::post('/users/logout',[UserAuthController::class,'logout']);
    Route::put('/users/change-password', [UserAuthController::class, 'changePassword']);
    Route::get('/users/notifications',[UserController::class,'getNotifications']);
    Route::get('/users/profile-edits',[UserController::class,'getUserEdits']);
    Route::get('/users/{id}', [AdminController::class,'getUser']);
});
